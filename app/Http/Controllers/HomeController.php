<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use App\Models\LegalPage;
use App\Models\User;
use App\Models\StudentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        try {
            $q = $request->query('q', ''); // get ?q= from URL, or empty string if not present
            $category_id = $request->query('category_id'); 
            $min_rating = $request->query('min_rating');
            $available = $request->query('available');

            $services = StudentService::with('category', 'user')
                        ->where('hss_is_active', true)
                        ->whereHas('user', function ($query) {
                            $query->where('hu_is_suspended', 0)
                                  ->where('hu_is_blacklisted', 0)
                                  ->where('hu_is_blocked', 0);
                        })
                        ->recommended()
                        ->get();        
                        
            $categories = Category::withCount(['services' => function ($q) {
                $q->where('hss_is_active', true);
            }])->get();

            //Display top provider
            $topStudents = \App\Models\User::where('hu_role', 'student')
            ->where('hu_is_suspended', 0)
            ->where('hu_is_blacklisted', 0)
            ->where('hu_is_blocked', 0)
            ->whereHas('services', function ($q) {
                $q->where('hss_is_active', true);
            })
            ->withCount(['services' => function ($q) {
                $q->where('hss_is_active', true);
            }])
            ->withAvg('reviewsReceived as average_rating', 'hr_rating')
            ->orderByDesc('services_count')
            ->take(6)
            ->get();

            $popularSearches = StudentService::query()
                ->where('hss_is_active', true)
                ->whereHas('user', function ($query) {
                    $query->where('hu_is_suspended', 0)
                        ->where('hu_is_blacklisted', 0)
                        ->where('hu_is_blocked', 0);
                })
                ->orderByDesc('hss_id')
                ->pluck('hss_title')
                ->filter(fn ($title) => is_string($title) && trim($title) !== '')
                ->unique()
                ->take(4)
                ->map(fn ($title) => [
                    'label' => Str::limit($title, 20),
                    'query' => $title,
                ])
                ->values();

            if ($popularSearches->isEmpty()) {
                $popularSearches = collect([
                    ['label' => 'Iron Baju', 'query' => 'iron baju'],
                    ['label' => 'Video Editing', 'query' => 'video editing'],
                    ['label' => 'Poster Design', 'query' => 'poster design'],
                    ['label' => 'Pickup Parcel', 'query' => 'pickup parcel'],
                ]);
            }

            return view('welcome', compact('services', 'categories','q', 'category_id', 'min_rating', 'available', 'topStudents', 'popularSearches'));
        } catch (\Exception $e) {
            // In case of any error, return a simple view with empty data
            Log::error('Error in HomeController@home: ' . $e->getMessage());
            
            return view('welcome', [
                'services' => collect([]),
                'categories' => collect([]),
                'q' => '',
                'category_id' => null,
                'min_rating' => null,
                'available' => null,
                'topStudents' => collect([]),
                'popularSearches' => collect([
                    ['label' => 'Iron Baju', 'query' => 'iron baju'],
                    ['label' => 'Video Editing', 'query' => 'video editing'],
                    ['label' => 'Poster Design', 'query' => 'poster design'],
                    ['label' => 'Pickup Parcel', 'query' => 'pickup parcel'],
                ])
            ]);
        }
    }

    public function services(Request $request): JsonResponse
{
    $q = $request->string('q')->toString();
    $categoryId = $request->integer('category_id');
    $minRating = $request->integer('min_rating');
    $availableOnly = $request->boolean('available_only', false);

    $query = StudentService::query()
        ->where('hss_is_active', true)
        ->with(['category', 'student' => function ($query) {
            $query->select(['hu_id', 'hu_name', 'hu_role', 'hu_is_available', 'hu_trust_badge', 'hu_average_rating']);
        }]);

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
        $query->whereHas('student', function ($sub) {
            $sub->where('hu_is_available', true);
        });
    }

    if ($minRating) {
        $query->whereRaw('(
            select avg(hr_rating)
            from h2u_reviews r
            where r.hr_reviewee_id = h2u_student_services.hss_user_id
        ) >= ?', [$minRating]);
    }

    $query->recommended();

    $services = $query->get();

    $result = $services->map(function ($svc) {
        $student = $svc->student;
        return [
            'id' => $svc->hss_id,
            'title' => $svc->hss_title,
            'description' => $svc->hss_description,
            'suggested_price' => $svc->hss_suggested_price,
            'category' => $svc->category,
            'student' => [
                'id' => $student->hu_id,
                'name' => $student->hu_name,
                'badge' => $student->hu_trust_badge,
                'is_available' => (bool) $student->hu_is_available,
                'average_rating' => $student->hu_average_rating,
            ],
        ];
    });

    return response()->json([
        'success' => true,
        'services' => $result,
    ], 200);
}

public function about()
    {
        // 1. Ambil statistik untuk dipaparkan di halaman About
        $totalUsers = User::count();
        
        $totalServices = StudentService::where('hss_is_active', true)->count();
        
        $totalSellers = User::where('hu_role', 'helper')
            ->whereHas('services', function ($q) {
                $q->where('hss_is_active', true);
            })->count();

        // 2. Ambil senarai kategori (jika anda mahu tunjukkan kepelbagaian servis di page About)
        $categories = Category::all();

        // 3. Return ke view 'about' (Pastikan anda ada fail resources/views/about.blade.php)
        return view('about', compact('totalUsers', 'totalServices', 'totalSellers', 'categories'));
    }

    public function terms()
    {
        $legalPage = LegalPage::where('hlp_slug', 'terms')->first();
        $isPublished = (bool) ($legalPage?->hlp_is_active ?? true);

        if (! $legalPage) {
            $legalPage = new LegalPage([
                'hlp_title' => 'Terms of Service',
                'hlp_content' => '1. Introduction' . "\n" . 'This page is currently being prepared.',
                'hlp_is_active' => true,
            ]);
            $isPublished = true;
        }

        return view('legal.terms', compact('legalPage', 'isPublished'));
    }

    public function privacy()
    {
        $legalPage = LegalPage::where('hlp_slug', 'privacy')->first();
        $isPublished = (bool) ($legalPage?->hlp_is_active ?? true);

        if (! $legalPage) {
            $legalPage = new LegalPage([
                'hlp_title' => 'Privacy Policy',
                'hlp_content' => '1. Policy Overview' . "\n" . 'This page is currently being prepared.',
                'hlp_is_active' => true,
            ]);
            $isPublished = true;
        }

        return view('legal.privacy', compact('legalPage', 'isPublished'));
    }
}
