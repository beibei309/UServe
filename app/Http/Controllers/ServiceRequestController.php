<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\StudentService;
use App\Http\Controllers\PointsController;
use App\Notifications\ServiceRequestStatusUpdated;
use App\Services\ServiceImageUrlResolver;
use App\Services\ServiceRequestNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceRequestController extends BaseController
{
    public function __construct(
        private readonly ServiceRequestNotificationService $serviceRequestNotificationService,
        private readonly ServiceImageUrlResolver $serviceImageUrlResolver
    ) {
        $this->middleware('auth');
    }

    /**
     * Store a new service request
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            // ...existing code...

            // 1. Validate basic fields (Make times nullable for flexibility)
            $validated = $request->validate([
                'student_service_id' => 'required|exists:h2u_student_services,hss_id',
                'selected_dates' => 'required|date',
                'start_time' => 'nullable|string',
                'end_time' => 'nullable|string',
                'selected_package' => 'required|string',
                'message' => 'nullable|string|max:1000',
                'offered_price' => 'nullable|numeric|min:0|max:99999.99',
            ]);

            $studentService = StudentService::findOrFail($validated['student_service_id']);

            // Check availability
            if (! $studentService->hss_is_active || ! $studentService->user->hu_is_available) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'success' => false,
                    'message' => 'Service or provider unavailable.',
                    'data' => null
                ], 400);
            }

            // Check for existing active requests from this user
            $hasActiveRequest = ServiceRequest::where('hsr_requester_id', $user->hu_id)
                ->where('hsr_student_service_id', $studentService->hss_id)
                ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress'])
                ->exists();
            if ($hasActiveRequest) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'success' => false,
                    'message' => 'You already have an active request with this helper.',
                    'data' => null
                ], 400);
            }

            // --- LOGIC SPLIT: Session vs Task ---
            $startTime = $validated['start_time'];
            $endTime = $validated['end_time'];
            if ($studentService->hss_session_duration) {
                if (! $startTime || ! $endTime) {
                    return response()->json([
                        'code' => 422,
                        'status' => 'error',
                        'success' => false,
                        'message' => 'Start and End time are required for this service.',
                        'data' => null
                    ], 422);
                }

                $selectedDateJson = json_encode($validated['selected_dates']);
                $overlapping = ServiceRequest::where('hsr_student_service_id', $studentService->hss_id)
                    ->whereRaw('hsr_selected_dates::text = ?', [$selectedDateJson])
                    ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress', 'approved'])
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('hsr_start_time', '<', $endTime)
                            ->where('hsr_end_time', '>', $startTime);
                    })
                    ->exists();
                if ($overlapping) {
                    return response()->json([
                        'code' => 400,
                        'status' => 'error',
                        'success' => false,
                        'message' => 'This time slot is booked. Please select another.',
                        'data' => null
                    ], 400);
                }
            } else {
                $startTime = $startTime ?? '00:00';
                $endTime = $endTime ?? '23:59';
            }

            // Create Request
            $serviceRequest = ServiceRequest::create([
                'hsr_student_service_id' => $studentService->hss_id,
                'hsr_requester_id' => $user->hu_id,
                'hsr_provider_id' => $studentService->hss_user_id,
                'hsr_selected_dates' => [$validated['selected_dates']],
                'hsr_start_time' => $startTime,
                'hsr_end_time' => $endTime,
                'hsr_selected_package' => [$validated['selected_package']],
                'hsr_message' => $validated['message'] ?? null,
                'hsr_offered_price' => $validated['offered_price'] ?? null,
                'hsr_status' => 'pending',
            ]);

            $this->serviceRequestNotificationService->notifyCreated($serviceRequest, $studentService, $user);

            return response()->json([
                'code' => 200,
                'status' => 'ok',
                'success' => true,
                'message' => 'Service request sent successfully!',
                'data' => [
                    'request_id' => $serviceRequest->hsr_id
                ]
            ], 200);
            // ...existing code...
        } catch (\Exception $e) {
            Log::error('ServiceRequest store error: '.$e->getMessage());
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'success' => false,
                'message' => 'Server error occurred.',
                'data' => null
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $currentUserId = (int) ($user->hu_id ?? $user->id);
            $isSellerMode = session('view_mode', 'buyer') === 'seller';
            $isHelperView = ($user->hu_role ?? null) === 'helper'
                && $isSellerMode
                && $user->canAccessSellerFeatures();

            $query = ServiceRequest::query()
                ->with(['studentService.category', 'requester', 'provider'])
                ->orderByDesc('created_at');

            if ($isHelperView) {
                $query->where('hsr_provider_id', $currentUserId);

                if ($request->filled('search')) {
                    $search = trim((string) $request->input('search'));
                    $query->where(function ($sub) use ($search) {
                        $sub->whereHas('studentService', function ($q) use ($search) {
                            $q->where('hss_title', 'like', "%{$search}%");
                        })->orWhereHas('requester', function ($q) use ($search) {
                            $q->where('hu_name', 'like', "%{$search}%")
                                ->orWhere('hu_email', 'like', "%{$search}%");
                        });
                    });
                }

                if ($request->filled('category')) {
                    $categoryId = (int) $request->input('category');
                    $query->whereHas('studentService', function ($q) use ($categoryId) {
                        $q->where('hss_category_id', $categoryId);
                    });
                }

                if ($request->filled('service_type')) {
                    $serviceTypeId = (int) $request->input('service_type');
                    $query->where('hsr_student_service_id', $serviceTypeId);
                }

                if ($request->filled('status')) {
                    $status = (string) $request->input('status');
                    if ($status === 'disputed') {
                        $query->where(function ($sub) {
                            $sub->where('hsr_status', 'disputed')
                                ->orWhere('hsr_payment_status', 'dispute');
                        });
                    } else {
                        $query->where('hsr_status', $status);
                    }
                }
            } else {
                $query->where('hsr_requester_id', $currentUserId);
            }

            if ($request->expectsJson() || $request->ajax()) {
                $perPage = (int) $request->input('per_page', 15);
                $page = (int) $request->input('page', 1);
                $requests = $query->paginate($perPage, ['*'], 'page', $page);

                return response()->json([
                    'success' => true,
                    'data' => $requests->items(),
                    'pagination' => [
                        'total' => $requests->total(),
                        'per_page' => $requests->perPage(),
                        'current_page' => $requests->currentPage(),
                        'last_page' => $requests->lastPage(),
                        'from' => $requests->firstItem(),
                        'to' => $requests->lastItem(),
                    ],
                ], 200);
            }

            $requests = $query->get();
            $this->decorateRequestsForUi($requests, $currentUserId, $isHelperView);

            $requestedTab = (string) $request->input('tab', '');
            $validTabs = ['pending', 'in-progress', 'completed'];

            if (in_array($requestedTab, $validTabs, true)) {
                $defaultStatusTab = $requestedTab;
            } else {
                $pendingCount = $requests->where('hsr_status', 'pending')->count();
                $inProgressCount = $requests->whereIn('hsr_status', ['accepted', 'in_progress', 'waiting_payment', 'disputed'])->count();
                $completedCount = $requests->whereIn('hsr_status', ['completed', 'cancelled', 'rejected'])->count();

                if ($pendingCount > 0) {
                    $defaultStatusTab = 'pending';
                } elseif ($inProgressCount > 0) {
                    $defaultStatusTab = 'in-progress';
                } elseif ($completedCount > 0) {
                    $defaultStatusTab = 'completed';
                } else {
                    $defaultStatusTab = 'pending';
                }
            }

            if ($isHelperView) {
                $serviceTypes = StudentService::query()
                    ->where('hss_user_id', $currentUserId)
                    ->orderBy('hss_title')
                    ->get(['hss_id', 'hss_title']);

                $categoryIds = StudentService::query()
                    ->where('hss_user_id', $currentUserId)
                    ->whereNotNull('hss_category_id')
                    ->pluck('hss_category_id')
                    ->unique()
                    ->values();

                $categories = Category::query()
                    ->whereIn('hc_id', $categoryIds)
                    ->orderBy('hc_name')
                    ->get();

                $receivedRequests = $requests;
                $serviceRequestsHelperJsVersion = @filemtime(public_path('js/nonadmin-service-requests-helper.js')) ?: time();

                return view('service-requests.helper', compact(
                    'receivedRequests',
                    'categories',
                    'serviceTypes',
                    'defaultStatusTab',
                    'serviceRequestsHelperJsVersion'
                ));
            }

            $sentRequests = $requests;
            $uniqueCategories = $sentRequests
                ->map(fn ($sr) => optional(optional($sr->studentService)->category)->hc_name)
                ->filter()
                ->unique()
                ->values();
            $serviceRequestsIndexJsVersion = @filemtime(public_path('js/nonadmin-service-requests-index.js')) ?: time();

            return view('service-requests.index', compact(
                'sentRequests',
                'uniqueCategories',
                'defaultStatusTab',
                'serviceRequestsIndexJsVersion'
            ));
        } catch (\Exception $e) {
            Log::error('ServiceRequest index error: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'pagination' => null,
                    'message' => 'Server error occurred.'
                ], 500);
            }

            return back()->with('error', 'Unable to load service requests right now.');
        }
    }

    /**
     * Show a specific service request
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        // Only requester and provider can view the request
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to view this request.');
        }

        $serviceRequest->load([
            'studentService.category',
            'requester',
            'provider',
            'reviews.reviewer',
        ]);
        $service = $serviceRequest->studentService;

        $currentUserId = (int) ($user->hu_id ?? $user->id);
        $isRequester = $currentUserId === (int) $serviceRequest->hsr_requester_id;
        $isProvider = $currentUserId === (int) $serviceRequest->hsr_provider_id;
        $providerRestricted = (bool) ($serviceRequest->provider->hu_is_suspended || $serviceRequest->provider->hu_is_blacklisted || $serviceRequest->provider->hu_is_blocked);
        $buyerRestricted = (bool) ($serviceRequest->requester->hu_is_suspended || $serviceRequest->requester->hu_is_blacklisted);
        $isRestricted = $providerRestricted || $buyerRestricted;

        $selectedPackageRaw = is_array($serviceRequest->hsr_selected_package)
            ? ($serviceRequest->hsr_selected_package[0] ?? 'custom')
            : ($serviceRequest->hsr_selected_package ?? 'custom');
        $selectedPackageLabel = trim(str_replace('"', '', (string) $selectedPackageRaw));
        $selectedPackageLabel = $selectedPackageLabel !== '' ? $selectedPackageLabel : 'Custom';

        $headerVisual = $this->resolveShowHeaderVisual($serviceRequest->hsr_status, $isRestricted);

        $requestedDateDisplays = collect((array) $serviceRequest->hsr_selected_dates)
            ->filter()
            ->map(function ($date) {
                try {
                    return Carbon::parse($date)->format('l, F j, Y');
                } catch (\Throwable $e) {
                    return (string) $date;
                }
            })
            ->values();

        $providerAverageRating = Review::where('hr_reviewee_id', $serviceRequest->provider->hu_id)->avg('hr_rating') ?? 0;
        $buyerAverageRating = Review::where('hr_reviewee_id', $serviceRequest->requester->hu_id)->avg('hr_rating') ?? 0;
        $hasCurrentUserReviewed = $serviceRequest->reviews
            ->contains(fn (Review $review) => (int) $review->hr_reviewer_id === $currentUserId);
        $contactPhone = $isProvider ? $serviceRequest->requester->hu_phone : $serviceRequest->provider->hu_phone;

        $serviceImageFallback = upsi2u_avatar_url($service->hss_title ?? 'Service');
        $serviceImageUrl = $this->serviceImageUrlResolver->resolveGeneralImageUrl(
            $service->hss_image_path,
            $serviceImageFallback
        );

        $paymentProofUrl = null;
        $paymentProofIsPdf = false;
        if ($serviceRequest->hsr_payment_proof) {
            $proofPath = $serviceRequest->hsr_payment_proof;
            if (Str::startsWith($proofPath, ['http://', 'https://'])) {
                $paymentProofUrl = $proofPath;
            } else {
                $paymentProofUrl = route('service-requests.payment-proof', $serviceRequest);
            }
            $paymentProofIsPdf = Str::endsWith(strtolower($proofPath), '.pdf');
        }

        return view('service-requests.show', compact(
            'serviceRequest',
            'service',
            'isRequester',
            'isProvider',
            'providerRestricted',
            'buyerRestricted',
            'isRestricted',
            'selectedPackageLabel',
            'headerVisual',
            'requestedDateDisplays',
            'providerAverageRating',
            'buyerAverageRating',
            'hasCurrentUserReviewed',
            'contactPhone',
            'serviceImageUrl',
            'serviceImageFallback',
            'paymentProofUrl',
            'paymentProofIsPdf'
        ));
    }

    private function decorateRequestsForUi($requests, int $currentUserId, bool $helperView): void
    {
        $requestIds = $requests->pluck('hsr_id')->all();
        $reviewsByRequest = collect();
        if (!empty($requestIds)) {
            $reviewsByRequest = Review::whereIn('hr_service_request_id', $requestIds)
                ->get()
                ->groupBy('hr_service_request_id');
        }

        $requests->transform(function (ServiceRequest $request) use ($reviewsByRequest, $helperView, $currentUserId) {
            $service = $request->studentService;
            $requestReviews = $reviewsByRequest->get((int) $request->hsr_id, collect());
            $selectedPackageRaw = is_array($request->hsr_selected_package)
                ? ($request->hsr_selected_package[0] ?? 'custom')
                : ($request->hsr_selected_package ?? 'custom');
            $packageLabel = trim(str_replace('"', '', (string) $selectedPackageRaw));
            $packageLabel = $packageLabel !== '' ? $packageLabel : 'Custom';
            $pkgType = strtolower($packageLabel);

            $dates = is_array($request->hsr_selected_dates) ? $request->hsr_selected_dates : (array) $request->hsr_selected_dates;
            $dates = array_values(array_filter($dates));
            $firstDate = $dates[0] ?? null;
            $firstDateDisplay = null;
            if ($firstDate) {
                try {
                    $firstDateDisplay = Carbon::parse($firstDate)->format('M j, Y');
                } catch (\Throwable $e) {
                    $firstDateDisplay = (string) $firstDate;
                }
            }

            $isSellerRestricted = (bool) ($request->provider->hu_is_suspended || $request->provider->hu_is_blacklisted || $request->provider->hu_is_blocked);
            $hasDatePassed = false;
            if ($firstDate) {
                try {
                    $hasDatePassed = now()->startOfDay()->gte(Carbon::parse($firstDate)->startOfDay());
                } catch (\Throwable $e) {
                    $hasDatePassed = false;
                }
            }

            $request->ui_package_label = $packageLabel;
            $request->ui_pkg_duration = $service->{$pkgType.'_duration'} ?? null;
            $request->ui_pkg_frequency = $service->{$pkgType.'_frequency'} ?? null;
            $request->ui_first_date_display = $firstDateDisplay;
            $request->ui_date_count = count($dates);
            $request->ui_is_seller_restricted = $isSellerRestricted;
            $request->ui_has_date_passed = $hasDatePassed;
            $request->ui_created_human = optional($request->created_at)->diffForHumans();
            $request->ui_updated_human = optional($request->updated_at)->diffForHumans();
            $request->ui_updated_date = optional($request->updated_at)->format('M j, Y');
            $request->ui_reviewed_by_auth = $requestReviews->contains(function (Review $review) use ($currentUserId) {
                return (int) $review->hr_reviewer_id === $currentUserId;
            });
            $request->ui_review_for_helper = $requestReviews->first(function (Review $review) use ($request) {
                return (int) $review->hr_reviewee_id === (int) $request->hsr_provider_id;
            });
            $request->ui_review_by_helper = $requestReviews->first(function (Review $review) use ($request) {
                return (int) $review->hr_reviewer_id === (int) $request->hsr_provider_id;
            });
            $request->ui_display_id = str_pad((string) $request->hsr_id, 5, '0', STR_PAD_LEFT);
            $paymentProofPath = (string) ($request->hsr_payment_proof ?? '');
            $request->ui_has_payment_proof = $paymentProofPath !== '';
            $request->ui_payment_proof_url = null;
            $request->ui_payment_proof_extension = strtolower(pathinfo(parse_url($paymentProofPath, PHP_URL_PATH) ?: $paymentProofPath, PATHINFO_EXTENSION));
            if ($request->ui_has_payment_proof) {
                if (Str::startsWith($paymentProofPath, ['http://', 'https://'])) {
                    $request->ui_payment_proof_url = $paymentProofPath;
                } else {
                    $request->ui_payment_proof_url = route('service-requests.payment-proof', $request);
                }
            }

            if (! $helperView) {
                $request->ui_status_text = strtoupper(str_replace('_', ' ', (string) $request->hsr_status));
                $request->ui_inprogress_style = $this->resolveBuyerInProgressStyles($request->hsr_status, $isSellerRestricted);
                $request->ui_completed_theme = $this->resolveBuyerCompletedTheme($request->hsr_status);
            } else {
                $request->ui_helper_inprogress_theme = $this->resolveHelperInProgressTheme($request->hsr_status);
                $request->ui_helper_completed_theme = $this->resolveHelperCompletedTheme($request->hsr_status);
            }

            return $request;
        });
    }

    private function resolveBuyerInProgressStyles(string $status, bool $isSellerRestricted): array
    {
        if ($isSellerRestricted) {
            return [
                'badge' => 'border-red-200 bg-red-100 text-red-700',
                'card' => 'border-red-300 bg-red-50 hover:border-red-400',
                'stripe' => 'bg-red-500',
                'status_text' => 'SELLER RESTRICTED',
            ];
        }

        $badge = match ($status) {
            'disputed' => 'border-red-200 bg-red-50 text-red-700',
            'waiting_payment' => 'border-yellow-200 bg-yellow-50 text-yellow-700',
            default => 'border-blue-200 bg-blue-50 text-blue-700',
        };
        $card = match ($status) {
            'disputed' => 'border-red-200 hover:border-red-300 bg-white',
            'waiting_payment' => 'border-yellow-200 hover:border-yellow-300 bg-white',
            default => 'border-blue-100 hover:border-blue-200 bg-white',
        };
        $stripe = match ($status) {
            'disputed' => 'bg-red-500',
            'waiting_payment' => 'bg-yellow-500',
            default => 'bg-blue-500',
        };

        return [
            'badge' => $badge,
            'card' => $card,
            'stripe' => $stripe,
            'status_text' => strtoupper(str_replace('_', ' ', $status)),
        ];
    }

    private function resolveBuyerCompletedTheme(string $status): array
    {
        return match ($status) {
            'completed' => [
                'border' => 'border-green-200 hover:border-green-300',
                'strip' => 'bg-green-500',
                'badge' => 'bg-green-100 text-green-700',
                'icon_bg' => 'bg-green-50',
                'icon_text' => 'text-green-600',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            'rejected' => [
                'border' => 'border-red-200 hover:border-red-300',
                'strip' => 'bg-red-500',
                'badge' => 'bg-red-100 text-red-700',
                'icon_bg' => 'bg-red-50',
                'icon_text' => 'text-red-600',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            default => [
                'border' => 'border-gray-200 hover:border-gray-300',
                'strip' => 'bg-gray-400',
                'badge' => 'bg-gray-100 text-gray-600',
                'icon_bg' => 'bg-gray-50',
                'icon_text' => 'text-gray-500',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
            ],
        };
    }

    private function resolveHelperInProgressTheme(string $status): array
    {
        return match ($status) {
            'disputed' => ['color' => 'red', 'border' => 'border-red-200', 'bg' => 'bg-red-500'],
            'waiting_payment' => ['color' => 'yellow', 'border' => 'border-yellow-200', 'bg' => 'bg-yellow-400'],
            'in_progress' => ['color' => 'blue', 'border' => 'border-blue-200', 'bg' => 'bg-blue-500'],
            default => ['color' => 'gray', 'border' => 'border-gray-200', 'bg' => 'bg-gray-400'],
        };
    }

    private function resolveHelperCompletedTheme(string $status): array
    {
        return match ($status) {
            'completed' => [
                'border' => 'border-green-200 hover:border-green-300',
                'strip' => 'bg-green-500',
                'badge' => 'bg-green-100 text-green-700',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            'rejected' => [
                'border' => 'border-red-200 hover:border-red-300',
                'strip' => 'bg-red-500',
                'badge' => 'bg-red-100 text-red-700',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            default => [
                'border' => 'border-gray-200 hover:border-gray-300',
                'strip' => 'bg-gray-400',
                'badge' => 'bg-gray-100 text-gray-600',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
            ],
        };
    }

    private function resolveShowHeaderVisual(string $status, bool $isRestricted): array
    {
        if ($isRestricted) {
            return ['style' => 'background: linear-gradient(to right, #ef4444, #b91c1c);', 'dot' => 'bg-red-400'];
        }

        return match ($status) {
            'in_progress', 'accepted' => ['style' => 'background: linear-gradient(to right, #3b82f6, #4f46e5);', 'dot' => 'bg-blue-300'],
            'completed' => ['style' => 'background: linear-gradient(to right, #10b981, #059669);', 'dot' => 'bg-green-300'],
            'cancelled', 'rejected', 'disputed' => ['style' => 'background: linear-gradient(to right, #6b7280, #374151);', 'dot' => 'bg-red-300'],
            'waiting_payment' => ['style' => 'background: linear-gradient(to right, #facc15, #ca8a04);', 'dot' => 'bg-yellow-300'],
            default => ['style' => 'background: linear-gradient(to right, #facc15, #fb923c);', 'dot' => 'bg-white'],
        };
    }

    /**
     * Accept a service request
     */
    public function accept(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ((int) $serviceRequest->hsr_provider_id !== (int) $user->hu_id) {
            abort(403, 'You are not authorized to accept this request.');
        }

        if (! $serviceRequest->isPending()) {
            return back()->with('error', 'This request cannot be accepted.');
        }

        $serviceRequest->accept();

        $this->notifyRequesterOfStatus($serviceRequest, 'accepted');

        return back()->with('success', 'Service request accepted successfully!');
    }

    /**
     * Reject a service request
     */
    public function reject(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ((int) $serviceRequest->hsr_provider_id !== (int) $user->hu_id) {
            abort(403, 'You are not authorized to reject this request.');
        }

        if (! $serviceRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be rejected.',
                'error' => 'This request cannot be rejected.',
            ], 400);
        }

        // 1. VALIDATE: Reason is mandatory
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // 2. UPDATE: Save status AND reason
        $serviceRequest->update([
            'hsr_status' => 'rejected',
            'hsr_rejection_reason' => $request->rejection_reason,
        ]);

        $this->notifyRequesterOfStatus($serviceRequest, 'rejected');

        return back()->with('success', 'Service request rejected.');
    }

    public function markInProgress(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ((int) $serviceRequest->hsr_provider_id !== (int) $user->hu_id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (! $serviceRequest->isAccepted()) {
            return back()->with('error', 'This request must be accepted first.');
        }

        $serviceRequest->update([
            'hsr_status' => 'in_progress',
            'hsr_started_at' => now(),
        ]);

        $this->notifyRequesterOfStatus($serviceRequest, 'in_progress');

        return back()->with('success', 'Service marked as in progress!');
    }

    public function markWorkFinished(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ((int) $serviceRequest->hsr_provider_id !== (int) $user->hu_id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (! $serviceRequest->isInProgress()) {
            return back()->with('error', 'This request must be in progress first.');
        }

        $serviceRequest->update([
            'hsr_status' => 'waiting_payment',
            'hsr_finished_at' => now(),
        ]);

        $this->notifyRequesterOfStatus($serviceRequest, 'waiting_payment');

        return back()->with('success', 'Service marked as finished!');
    }

    // BUYER/REQUESTER SIDE TO MAKE PAKMENT
    public function buyerConfirmPayment(Request $request, ServiceRequest $serviceRequest)
    {
        // 1. Authorization
        $user = Auth::user();
        if ((int) $serviceRequest->hsr_requester_id !== (int) $user->hu_id) {
            abort(403);
        }

        // 2. Validate (File is optional, but if present must be an image/pdf)
        $request->validate([
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1024',
        ], [
            'payment_proof.max' => 'Payment proof must be 1MB or smaller.',
            'payment_proof.mimes' => 'Payment proof must be a JPG, PNG, or PDF file.',
        ]);

        // 3. Handle File Upload
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');

            try {
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
                $random = bin2hex(random_bytes(6));
                $filename = 'payment_'.$serviceRequest->hsr_id.'_'.now()->format('YmdHis').'_'.$random.'.'.$extension;

                $disk = Storage::disk('local');
                $directory = 'payment_proofs';
                $path = $directory.'/'.$filename;

                if (! $disk->exists($directory)) {
                    $disk->makeDirectory($directory);
                }

                $tmpPath = $file->getRealPath() ?: $file->getPathname();
                if (empty($tmpPath) || ! is_file($tmpPath)) {
                    return back()->withErrors([
                        'payment_proof' => 'Upload failed. Temporary upload file is missing. Please retry.',
                    ]);
                }

                $contents = @file_get_contents($tmpPath);
                if ($contents === false) {
                    return back()->withErrors([
                        'payment_proof' => 'Upload failed while reading the file. Please retry.',
                    ]);
                }

                $stored = $disk->put($path, $contents);
                if (! $stored || ! $disk->exists($path)) {
                    return back()->withErrors(['payment_proof' => 'Payment proof upload failed. Please try again.']);
                }

                $serviceRequest->update(['hsr_payment_proof' => $path]);
            } catch (\Throwable $e) {
                Log::error('Payment proof upload failed', [
                    'service_request_id' => $serviceRequest->hsr_id,
                    'user_id' => $user->hu_id,
                    'message' => $e->getMessage(),
                ]);

                return back()->withErrors([
                    'payment_proof' => 'Upload failed on this environment. Please retry or check server upload temp settings.',
                ]);
            }
        }

        // 4. Update Status
        $serviceRequest->update([
            'hsr_payment_status' => 'verification_status',
        ]);

        return back()->with('success', 'Payment confirmed! Waiting for seller verification.');
    }

    public function finalizeOrder(Request $request, ServiceRequest $serviceRequest)
    {
        // 1. Authorization
        $user = Auth::user();
        if ((int) $serviceRequest->hsr_provider_id !== (int) $user->hu_id) {
            abort(403, 'Unauthorized action.');
        }

        $outcome = $request->input('outcome'); // 'paid' or 'unpaid_problem'

        if ($outcome === 'paid') {
            // ✅ SCENARIO A: Success
            // This updates BOTH status columns at once
            $serviceRequest->update([
                'hsr_status' => 'completed',          // Moves order to History
                'hsr_payment_status' => 'paid',       // Marks payment as Green/Paid
                'hsr_completed_at' => now(),          // Record completion time
            ]);

            // Award points for completed service
            PointsController::awardPointsForCompletedService($serviceRequest); // Seller points
            PointsController::awardBuyerPointsForCompletedService($serviceRequest); // Buyer points

            $this->notifyRequesterOfStatus($serviceRequest, 'completed');

            return back()->with('success', 'Payment confirmed and Order marked as Completed! Both parties earned 1 point each.');
        } else {
            $serviceRequest->update([
                'hsr_status' => 'waiting_payment',          // We still close the order
                'hsr_payment_status' => 'unpaid', // But flag it as a problem
                'hsr_completed_at' => now(),
            ]);

            return back()->with('error', 'Order closed as Unpaid. Buyer reported.');
        }
    }

    // FOR BUYER REPORT
    public function reportIssue(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        if ((int) Auth::id() !== (int) $serviceRequest->hsr_requester_id) {
            return back()->with('error', 'Only the buyer can submit this report.');
        }

        if (!in_array($serviceRequest->hsr_status, ['in_progress', 'waiting_payment'], true)) {
            return back()->with('error', 'You can report only when the order is in progress or waiting payment.');
        }

        // Validate
        $request->validate([
            'dispute_reason' => 'required|string',
        ]);

        // Concatenate reason if notes exist
        $reason = $request->dispute_reason;
        if ($request->additional_notes) {
            $reason .= ' - Note: '.$request->additional_notes;
        }

        // Update status to 'disputed'
        $serviceRequest->update([
            'hsr_status' => 'disputed',
            'hsr_payment_status' => 'dispute',
            'hsr_dispute_reason' => $reason,
            'hsr_reported_by' => Auth::id(),
        ]);

        return back()->with('success', 'Report submitted. Admin will review the case.');
    }

    public function report(Request $request, ServiceRequest $serviceRequest)
    {
        if ((int) Auth::id() !== (int) $serviceRequest->hsr_provider_id) {
            return back()->with('error', 'Only the helper can submit this report.');
        }

        if (!in_array($serviceRequest->hsr_status, ['in_progress', 'waiting_payment'], true)) {
            return back()->with('error', 'You can report only when the order is in progress or waiting payment.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // 1. Update the Request Status
        $serviceRequest->update([
            'hsr_status' => 'disputed',
            'hsr_payment_status' => 'dispute',
            'hsr_dispute_reason' => $request->reason,
            'hsr_reported_by' => Auth::id(),
        ]);

        $buyerId = $serviceRequest->hsr_requester_id;

        if ($buyerId) {
            \App\Models\User::where('hu_id', $buyerId)->increment('hu_reports_count');
        }

        return back()->with('success', 'Report submitted. Buyer has been flagged.');
    }

    public function cancelDispute($id)
    {
        $request = ServiceRequest::findOrFail($id);

        if ((int) Auth::id() !== (int) $request->hsr_reported_by) {
            return back()->with('error', 'Only the user who reported this dispute can withdraw it.');
        }

        if ($request->hsr_status === 'disputed' || $request->hsr_payment_status === 'dispute') {
            $request->update([
                'hsr_status' => $this->resolveStatusAfterDispute($request),
                'hsr_payment_status' => 'unpaid',
                'hsr_dispute_reason' => null,
                'hsr_reported_by' => null,
            ]);

            return back()->with('success', 'Report withdrawn. Request resumed to normal flow.');
        }

        return back()->with('error', 'Cannot cancel report at this stage.');
    }

    private function resolveStatusAfterDispute(ServiceRequest $serviceRequest): string
    {
        if (!is_null($serviceRequest->hsr_finished_at)) {
            return 'waiting_payment';
        }

        if (!is_null($serviceRequest->hsr_started_at)) {
            return 'in_progress';
        }

        if (!is_null($serviceRequest->hsr_accepted_at)) {
            return 'accepted';
        }

        return 'accepted';
    }


    public function markAsPaid($id)
    {
        $request = ServiceRequest::findOrFail($id);

        if ((int) $request->hsr_provider_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->update(['hsr_payment_status' => 'paid']);

        return back()->with('success', 'Payment status updated.');
    }

    private function notifyRequesterOfStatus(ServiceRequest $serviceRequest, string $status): void
    {
        try {
            $serviceRequest->requester?->notify(new ServiceRequestStatusUpdated($serviceRequest, $status));
        } catch (\Throwable $e) {
            Log::warning('Service request status notification failed', [
                'service_request_id' => $serviceRequest->hsr_id,
                'status' => $status,
                'requester_id' => $serviceRequest->hsr_requester_id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showPaymentProof(ServiceRequest $serviceRequest)
    {
        if (
            (int) $serviceRequest->hsr_requester_id !== (int) Auth::id()
            && (int) $serviceRequest->hsr_provider_id !== (int) Auth::id()
        ) {
            abort(403, 'You are not authorized to view this payment proof.');
        }

        return $this->paymentProofResponse($serviceRequest);
    }

    private function paymentProofResponse(ServiceRequest $serviceRequest)
    {
        $proofPath = ltrim((string) $serviceRequest->hsr_payment_proof, '/');

        if ($proofPath === '') {
            abort(404, 'Payment proof not found.');
        }

        if (Storage::disk('local')->exists($proofPath)) {
            return response()->file(Storage::disk('local')->path($proofPath), $this->privateFileHeaders());
        }

        $publicPath = Str::startsWith($proofPath, 'storage/')
            ? Str::after($proofPath, 'storage/')
            : $proofPath;

        if (Storage::disk('public')->exists($publicPath)) {
            return response()->file(Storage::disk('public')->path($publicPath), $this->privateFileHeaders());
        }

        abort(404, 'Payment proof not found.');
    }

    private function privateFileHeaders(): array
    {
        return [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
    }

    public function cancel(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        // 1. Authorization
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to cancel this request.');
        }

        // 2. Block if Completed
        if ($serviceRequest->hsr_status === 'completed') {
            return back()->with('error', 'Completed requests cannot be cancelled.');
        }

        // 3. Block if In Progress (Seller started work)
        if ($serviceRequest->hsr_status === 'in_progress') {
            return back()->with('error', 'The seller has already started working on this request. Please contact the seller directly to discuss cancellation.');
        }

        // 4. Allow Cancellation (for 'pending' or 'accepted')
        $serviceRequest->update(['hsr_status' => 'cancelled']);

        return back()->with('success', 'Service request cancelled successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        // Validate that the user owns the request or is the provider
        if (Auth::id() !== $serviceRequest->hsr_requester_id && Auth::id() !== $serviceRequest->hsr_provider_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error' => 'Unauthorized',
            ], 403);
        }

        // Validate the new status
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected,in_progress,waiting_payment,completed,cancelled,disputed,approved',
        ]);

        // Update the status
        $serviceRequest->update([
            'hsr_status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated to '.$validated['status'],
        ]);
    }

    public function storeBuyerReview(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);

        // ensure only provider can review requester
        if ($serviceRequest->hsr_provider_id !== Auth::id()) {
            abort(403);
        }

        // prevent duplicate review
        if ($serviceRequest->reviewByHelper) {
            return back()->with('error', 'You already reviewed this client.');
        }

        Review::create([
            'hr_service_request_id' => $serviceRequest->hsr_id,
            'hr_reviewer_id' => Auth::id(),
            'hr_reviewee_id' => $serviceRequest->hsr_requester_id,
            'hr_rating' => $request->rating,
            'hr_comment' => $request->comment,
        ]);

        return back()->with('success', 'Review submitted!');
    }
}
