<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use App\Models\User;
use App\Models\StudentService;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $q = $request->query('q'); // get ?q= from URL, or null if not present

        $category_id = $request->query('category_id'); 
        $min_rating = $request->query('min_rating');
        $available = $request->query('available');

        $services = StudentService::with('category', 'user')
                    ->where('is_active', true)
                    ->latest()
                    ->get();        
                    
        $categories = Category::withCount(['services' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        //Display top provider
        $topStudents = \App\Models\User::where('role', 'student')
        ->whereHas('services', function ($q) {
            $q->where('is_active', true);
        })
        ->withCount(['services' => function ($q) {
            $q->where('is_active', true);
        }])
        ->withAvg('reviewsReceived as average_rating', 'rating')
        ->orderByDesc('services_count')
        ->take(6)
        ->get();


        return view('welcome', compact('services', 'categories','q', 'category_id', 'min_rating', 'available', 'topStudents'));
    }

    public function services(Request $request): JsonResponse
{
    $q = $request->string('q')->toString();
    $categoryId = $request->integer('category_id');
    $minRating = $request->integer('min_rating');
    $availableOnly = $request->boolean('available_only', false);

    $query = StudentService::query()
        ->where('is_active', true)
        ->with(['category', 'student' => function ($query) {
            $query->select(['id', 'name', 'role', 'is_available']);
        }]);

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
        $query->whereHas('student', function ($sub) {
            $sub->where('is_available', true);
        });
    }

    if ($minRating) {
        $query->whereRaw('(
            select avg(rating)
            from reviews r
            where r.reviewee_id = student_services.user_id
        ) >= ?', [$minRating]);
    }

    $query->orderByDesc('id');

    $services = $query->get();

    $result = $services->map(function ($svc) {
        $student = $svc->student;
        return [
            'id' => $svc->id,
            'title' => $svc->title,
            'description' => $svc->description,
            'suggested_price' => $svc->suggested_price,
            'category' => $svc->category,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'badge' => $student->trust_badge,
                'is_available' => (bool) $student->is_available,
                'average_rating' => $student->average_rating,
            ],
        ];
    });

    return response()->json(['services' => $result], 200);
}

public function about()
    {
        // 1. Ambil statistik untuk dipaparkan di halaman About
        $totalUsers = User::count();
        
        $totalServices = StudentService::where('is_active', true)->count();
        
        $totalSellers = User::where('role', 'helper')
            ->whereHas('services', function ($q) {
                $q->where('is_active', true);
            })->count();

        // 2. Ambil senarai kategori (jika anda mahu tunjukkan kepelbagaian servis di page About)
        $categories = Category::all();

        // 3. Return ke view 'about' (Pastikan anda ada fail resources/views/about.blade.php)
        return view('about', compact('totalUsers', 'totalServices', 'totalSellers', 'categories'));
    }

}