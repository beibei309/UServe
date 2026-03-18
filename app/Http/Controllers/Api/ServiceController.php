<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentService;
use App\Models\Category;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceDetailResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /**
     * Get paginated list of services with filtering and search
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get and sanitize inputs
            $search = $request->input('search', '');
            $categoryIdRaw = $request->input('category_id');
            $categoryId = filter_var($categoryIdRaw, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]) ?: null;
            $sort = $request->input('sort', 'recommended');
            $availableOnly = $request->input('available_only');
            $perPage = max(1, min((int) $request->input('per_page', 15), 50)); // 1..50 items per page

            $allowedSorts = ['recommended', 'newest', 'oldest', 'price_low', 'price_high', 'rating'];
            if (!in_array($sort, $allowedSorts, true)) {
                $sort = 'recommended';
            }

            // Build query
            $query = StudentService::with(['user', 'category'])
                ->withCount(['reviews' => function ($query) {
                    $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
                }])
                ->withAvg(['reviews as average_rating' => function ($query) {
                    $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
                }], 'hr_rating')
                ->where('hss_approval_status', 'approved')
                ->where('hss_is_active', true)
                ->whereHas('user', function ($q) {
                    $q->where('hu_role', 'helper')
                      ->where('hu_is_suspended', 0)
                      ->where('hu_is_blacklisted', 0)
                      ->where('hu_is_blocked', 0);
                })
                ->whereHas('category');

            // Apply filters
            if ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('hss_title', 'like', "%$search%")
                        ->orWhere('hss_description', 'like', "%$search%");
                });
            }

            if ($categoryId) {
                $query->where('hss_category_id', $categoryId);
            }

            if ($availableOnly === '1' || $availableOnly === 'true' || $availableOnly === true) {
                $query->where('hss_status', 'available');
            } elseif ($availableOnly === '0' || $availableOnly === 'false' || $availableOnly === false) {
                $query->where('hss_status', 'unavailable');
            }

            // Apply sorting
            switch ($sort) {
                case 'recommended':
                    $query->recommended();
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'price_low':
                    $query->orderBy('hss_basic_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('hss_basic_price', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('average_rating', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            // Get paginated results
            $services = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => ServiceResource::collection($services->items()),
                'pagination' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                    'from' => $services->firstItem(),
                    'to' => $services->lastItem(),
                    'has_more_pages' => $services->hasMorePages(),
                ],
                'filters' => [
                    'search' => $search,
                    'category_id' => $categoryId,
                    'sort' => $sort,
                    'available_only' => $availableOnly,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed information about a specific service
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            if (!is_numeric($id) || (int) $id < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            $service = StudentService::with([
                'user',
                'category',
                'reviews' => function ($query) {
                    $query->with('reviewer')->latest();
                }
            ])
            ->withCount(['reviews' => function ($query) {
                $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
            }])
            ->withAvg(['reviews as average_rating' => function ($query) {
                $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
            }], 'hr_rating')
            ->findOrFail((int) $id);

            // Check for missing relationships
            if (!$service->user || !$service->category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service provider or category not found'
                ], 404);
            }

            // Check if service provider is suspended/blocked
            if ($service->user->hu_is_suspended || $service->user->hu_is_blacklisted || $service->user->hu_is_blocked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not available'
                ], 404);
            }

            // Check if service is approved and active
            if ($service->hss_approval_status !== 'approved' || !$service->hss_is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ServiceDetailResource($service)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch service details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all categories for filtering
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Category::all();

            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($categories)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
