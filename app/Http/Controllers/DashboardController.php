<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Category;
use App\Models\StudentService;
use Illuminate\Http\JsonResponse; // Added for return type
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{

public function index(Request $request)
{
    // --- 1. Get Inputs ---
    $q = $request->input('q'); 
    $category_id = $request->input('category_id'); 
    $available = $request->input('available', '1');
    $currentUserId = Auth::id(); 
    
    // --- 2. Initialize Query ---
    // We use the logic you provided to link reviews to the USER, not just the service
    $query = StudentService::with(['category', 'user']) 
        ->withCount(['reviews' => function ($query) {
            // Count reviews where the person reviewed is the service owner
            $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
        }])
        ->withAvg(['reviews as reviews_avg_rating' => function ($query) {
            // Calculate average rating where the person reviewed is the service owner
            $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
        }], 'hr_rating')
        ->where('hss_approval_status', 'approved');

    // --- 3. Apply Filters ---
    
    if ($currentUserId) {
        $query->where('hss_user_id', '!=', $currentUserId);
    }

    if ($q) {
        $query->where(function ($sub) use ($q) {
            $sub->where('hss_title', 'like', "%{$q}%")
                ->orWhere('hss_description', 'like', "%{$q}%");
        });
    }

    if ($category_id) {
        $query->where('hss_category_id', $category_id);
    }

    if ($available === '1') {
        $query->where('hss_status', 'available');
    } elseif ($available === '0') { // Optional: Allows you to specifically see unavailable ones via URL ?available=0
        $query->where('hss_status', 'unavailable');
    }

    $services = $query->latest()->take(6)->get();
    $serviceCards = $services->map(function (StudentService $service) {
        return $this->mapDashboardServiceCard($service);
    })->values();

    // ... (Keep the rest of your code for $categories and $topStudents same as before) ...
    $categories = Category::withCount(['services' => function ($q) {
        $q->where('hss_approval_status', 'approved');
    }])->get();

    $topStudents = User::where('hu_role', 'helper')
        ->when($currentUserId, function ($query) use ($currentUserId) {
            return $query->where('hu_id', '!=', $currentUserId);
        })
        ->whereHas('services', function ($q) {
            $q->where('hss_approval_status', 'approved');
        })
        ->withCount('reviewsReceived') 
        ->withAvg('reviewsReceived as reviews_received_avg_rating', 'hr_rating') 
        ->orderByDesc('reviews_received_avg_rating') 
        ->take(10)
        ->get();
    $availableHelpers = $topStudents
        ->filter(fn(User $student) => (bool) $student->hu_is_available)
        ->map(fn(User $student) => $this->mapDashboardHelperCard($student))
        ->values();

    $dashboardUi = [
        'search_query' => $q ?? '',
        'welcome_name' => $request->user()?->hu_name ?? 'User',
        'popular_searches' => [
            ['label' => 'Iron Baju', 'query' => 'iron baju'],
            ['label' => 'Video Editing', 'query' => 'video editing'],
            ['label' => 'Poster Design', 'query' => 'poster design'],
            ['label' => 'Pickup Parcel', 'query' => 'pickup'],
        ],
    ];

    return view('dashboard', compact('serviceCards', 'categories', 'availableHelpers', 'q', 'category_id', 'dashboardUi'));
}
    public function services(Request $request): JsonResponse
    {
        $q = $request->string('q')->toString();
        $categoryId = $request->integer('category_id');
        $minRating = $request->integer('min_rating');
        $availableOnly = $request->boolean('available_only', true);
        $query = StudentService::query()
            ->with(['category', 'user']) // Use 'user' relation standard
            ->withAvg('reviews as reviews_avg_rating', 'hr_rating') // Use Service specific rating
            ->where('hss_approval_status', 'approved');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('hss_title', 'like', "%$q%")
                    ->orWhere('hss_description', 'like', "%$q%");
            });
        }

        if ($categoryId) {
            $query->where('hss_category_id', $categoryId);
        }

        if ($availableOnly) {
            $query->where('hss_status', 'available');
        }

        // Filter by Service Rating (Not user rating)
        if ($minRating) {
            $query->having('reviews_avg_rating', '>=', $minRating);
        }

        $services = $query->latest()->get();

        $result = $services->map(function ($svc) {
            return [
                'id' => $svc->hss_id,
                'title' => $svc->hss_title,
                'description' => $svc->hss_description,
                'basic_price' => $svc->hss_basic_price,
                'category' => $svc->category,
                'rating' => round($svc->reviews_avg_rating, 1), // Service Rating
                'student' => [
                    'id' => $svc->user->hu_id,
                    'name' => $svc->user->hu_name,
                    'badge' => $svc->user->hu_trust_badge,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'services' => $result,
        ], 200);
    }

    public function switchMode(Request $request)
    {
        $user = Auth::user();

        // Only helpers can switch modes
        if ($user->hu_role !== 'helper') {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($user->hu_is_blocked) {
            session(['view_mode' => 'buyer']);
            return redirect()->route('dashboard')->with('error', 'Your account is blocked from seller actions. You can continue using buyer features.');
        }

<<<<<<< HEAD
        // Get current mode (default to 'seller' for helpers if not set)
        $currentMode = session('view_mode', 'seller');
=======
        // Get current mode (default to buyer when not set to avoid first-click inversion)
        $currentMode = session('view_mode', 'buyer');
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)

        if ($currentMode === 'seller') {
            // Switch to Buying Mode
            session(['view_mode' => 'buyer']);
            return redirect()->route('dashboard'); // Redirect to Browse Services/Home
        } else {
            // Switch to Selling Mode
            session(['view_mode' => 'seller']);
            return redirect()->route('students.index'); // Redirect to Helper Dashboard
        }
    }

    private function resolveDashboardServiceImageUrl(?string $path, ?string $title): string
    {
        $fallback = 'https://ui-avatars.com/api/?name=' . urlencode($title ?? 'Service');
        if (!$path) {
            return $fallback;
        }
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }
        return asset('storage/' . ltrim($path, '/'));
    }

    private function resolveUserAvatarUrl(?string $path, ?string $name): string
    {
        if (!empty($path)) {
            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }
            return asset($path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($name ?? 'User');
    }

    private function mapDashboardServiceCard(StudentService $service): array
    {
        $rating = (float) ($service->reviews_avg_rating ?? 0);
        return [
            'details_url' => route('services.details', $service),
            'title' => $service->hss_title,
            'description_html' => $service->hss_description,
            'description_preview' => Str::limit(strip_tags($service->hss_description ?? ''), 140),
            'image_url' => $this->resolveDashboardServiceImageUrl($service->hss_image_path, $service->hss_title),
            'category_name' => $service->category?->hc_name,
            'category_color' => $service->category?->hc_color,
            'seller_name_short' => Str::limit($service->user?->hu_name ?? 'User', 15),
            'seller_avatar_url' => $this->resolveUserAvatarUrl($service->user?->hu_profile_photo_path, $service->user?->hu_name),
            'seller_has_trust_badge' => (bool) ($service->user?->hu_trust_badge ?? false),
            'rating_display' => number_format($rating, 1),
            'rating_stars_filled' => (int) round($rating),
            'reviews_count_display' => (int) ($service->reviews_count ?? 0),
            'price_display' => number_format((float) ($service->hss_basic_price ?? 0), 0),
        ];
    }

    private function mapDashboardHelperCard(User $student): array
    {
        $rating = (float) ($student->reviews_received_avg_rating ?? 0);
        return [
            'profile_url' => route('students.profile', $student),
            'name' => $student->hu_name,
            'initial' => Str::substr($student->hu_name ?? 'U', 0, 1),
            'avatar_url' => $student->hu_profile_photo_path ? asset($student->hu_profile_photo_path) : null,
            'faculty_display' => $student->hu_faculty ?: 'Student Seller',
            'rating_display' => number_format($rating, 1),
            'reviews_count' => (int) ($student->reviews_received_count ?? 0),
        ];
    }
}
