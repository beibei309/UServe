<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Category;
use App\Models\StudentService;
use Illuminate\Http\JsonResponse; // Added for return type
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $query->whereColumn('reviews.reviewee_id', 'student_services.user_id');
        }])
        ->withAvg(['reviews' => function ($query) {
            // Calculate average rating where the person reviewed is the service owner
            $query->whereColumn('reviews.reviewee_id', 'student_services.user_id');
        }], 'rating')
        ->where('approval_status', 'approved');

    // --- 3. Apply Filters ---
    
    if ($currentUserId) {
        $query->where('user_id', '!=', $currentUserId);
    }

    if ($q) {
        $query->where(function ($sub) use ($q) {
            $sub->where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%");
        });
    }

    if ($category_id) {
        $query->where('category_id', $category_id);
    }

    if ($available === '1') {
        $query->where('status', 'available');
    } elseif ($available === '0') { // Optional: Allows you to specifically see unavailable ones via URL ?available=0
        $query->where('status', 'unavailable');
    }

    $services = $query->latest()->take(6)->get();

    // ... (Keep the rest of your code for $categories and $topStudents same as before) ...
    $categories = Category::withCount(['services' => function ($q) {
        $q->where('approval_status', 'approved');
    }])->get();

    $topStudents = User::where('role', 'helper')
        ->when($currentUserId, function ($query) use ($currentUserId) {
            return $query->where('id', '!=', $currentUserId);
        })
        ->whereHas('services', function ($q) {
            $q->where('approval_status', 'approved');
        })
        ->withCount('reviewsReceived') 
        ->withAvg('reviewsReceived', 'rating') 
        ->orderByDesc('reviews_received_avg_rating') 
        ->take(10)
        ->get();

    return view('dashboard', compact('services', 'categories', 'topStudents', 'q', 'category_id'));
}
    public function services(Request $request): JsonResponse
    {
        $q = $request->string('q')->toString();
        $categoryId = $request->integer('category_id');
        $minRating = $request->integer('min_rating');
        $availableOnly = $request->boolean('available_only', true);
        $query = StudentService::query()
            ->with(['category', 'user']) // Use 'user' relation standard
            ->withAvg('reviews', 'rating') // Use Service specific rating
            ->where('approval_status', 'approved');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($availableOnly) {
            $query->where('status', 'available');
        }

        // Filter by Service Rating (Not user rating)
        if ($minRating) {
            $query->having('reviews_avg_rating', '>=', $minRating);
        }

        $services = $query->latest()->get();

        $result = $services->map(function ($svc) {
            return [
                'id' => $svc->id,
                'title' => $svc->title,
                'description' => $svc->description,
                'basic_price' => $svc->basic_price,
                'category' => $svc->category,
                'rating' => round($svc->reviews_avg_rating, 1), // Service Rating
                'student' => [
                    'id' => $svc->user->id,
                    'name' => $svc->user->name,
                    'badge' => $svc->user->trust_badge,
                ],
            ];
        });

        return response()->json(['services' => $result], 200);
    }

    public function switchMode(Request $request)
    {
        $user = Auth::user();

        // Only helpers can switch modes
        if ($user->role !== 'helper') {
            return back()->with('error', 'Unauthorized action.');
        }

        // Get current mode (default to 'seller' for helpers if not set)
        $currentMode = session('view_mode', 'seller');

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
}