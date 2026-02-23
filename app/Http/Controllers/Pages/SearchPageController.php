<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentService;
use App\Models\User;
use App\Models\Category;

class SearchPageController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $categoryId = $request->input('category_id');
        $minRating = $request->input('min_rating');
        $availableOnly = $request->boolean('available', true);
        $sortBy = $request->input('sort', 'newest');

        $query = StudentService::query()->where('is_active', true);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Preload user and category for rating/availability filtering and display
        $query->with(['user', 'category']);
        
        // Apply sorting
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('suggested_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('suggested_price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }
        
        $services = $query->paginate(12)->withQueryString();

        // Filter by availability and min rating at collection level (average_rating is an appended attribute)
        $filtered = $services->getCollection()->filter(function ($service) use ($availableOnly, $minRating) {
            /** @var StudentService $service */
            $user = $service->user;
            if (!$user) return false;
            if ($availableOnly && !$user->is_available) return false;
            if ($minRating && is_numeric($minRating)) {
                $avg = $user->average_rating ?? 0;
                if ($avg < (float) $minRating) return false;
            }
            return true;
        });
        $services->setCollection($filtered->values());

        // Get categories for filter dropdown
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('search.index', [
            'services' => $services,
            'categories' => $categories,
            'q' => $q,
            'category_id' => $categoryId,
            'min_rating' => $minRating,
            'available' => $availableOnly,
            'sort' => $sortBy,
        ]);
    }
}