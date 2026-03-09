<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\StudentService;
use App\Models\User;
use App\Services\ServiceImageUrlResolver;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentServiceController extends Controller
{
    public function __construct(private readonly ServiceImageUrlResolver $serviceImageUrlResolver) {}

    public function index(Request $request)
    {
        // --- 1. Get and Sanitize Inputs ---
        $q = $request->string('q')->toString();
        $category_id = $request->category_id;
        $sort = $request->sort ?? 'newest';
        $available_only = $request->available_only;

        $currentUserId = Auth::id();


        $query = StudentService::with(['student', 'category'])
            ->withCount(['reviews' => function ($query) {
                $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
            }])
            ->withAvg(['reviews as reviews_avg_rating' => function ($query) {
                $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
            }], 'hr_rating')
            ->where('hss_approval_status', 'approved')
            // START MODIFICATION
            ->whereHas('student', function ($q) {
                $q->where('hu_role', 'helper')
                    ->where('hu_is_suspended', 0)     // Exclude suspended users
                    ->where('hu_is_blacklisted', 0)   // Exclude blacklisted users
                    ->where('hu_is_blocked', 0);      // Exclude blocked users from seller listings
            });

        if ($currentUserId) {
            $query->where('hss_user_id', '!=', $currentUserId);
        }

        if ($available_only === '1') {
            // Jika user nak cari yang Available sahaja
            $query->where('hss_status', 'available');
        } elseif ($available_only === '0') {
            // Jika user nak cari yang Busy (Unavailable) sahaja
            $query->where('hss_status', 'unavailable');
        }
        // Jika $available_only null/kosong, ia akan tunjuk KEDUA-DUA (Available & Busy)

        // --- 4. Search filter ---
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('hss_title', 'like', "%$q%")
                    ->orWhere('hss_description', 'like', "%$q%");
            });
        }

        // --- 5. Category filter ---
        if ($category_id) {
            $query->where('hss_category_id', $category_id);
        }

        // --- 6. Sorting ---
        if ($sort == 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort == 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort == 'price_low') {
            $query->orderBy('hss_basic_price', 'asc');
        } elseif ($sort == 'price_high') {
            $query->orderBy('hss_basic_price', 'desc');
        }

        $services = $query->paginate(5);
        $isAuthenticated = Auth::check();
        $services->getCollection()->transform(function (StudentService $service) use ($isAuthenticated) {
            $service->ui_image_url = $this->resolveServiceCardImageUrl($service->hss_image_path, $service->hss_title);
            $service->ui_image_fallback = 'https://ui-avatars.com/api/?name='.urlencode($service->hss_title ?? 'Service');
            $service->ui_details_url = route('services.details', $service->hss_id);

            $service->ui_seller_avatar_url = $service->user->hu_profile_photo_path
                ? asset($service->user->hu_profile_photo_path)
                : 'https://ui-avatars.com/api/?name='.urlencode($isAuthenticated ? $service->user->hu_name : substr((string) $service->user->hu_name, 0, 1)).'&background=random';
            $service->ui_seller_display_name = $isAuthenticated
                ? $service->user->hu_name
                : Str::limit($service->user->hu_name, 1, '****');
            $service->ui_profile_url = $isAuthenticated
                ? route('students.profile', $service->user)
                : route('login');
            $service->ui_created_human = $service->created_at->diffForHumans();

            return $service;
        });

        return view('services.index', [
            'services' => $services,
            'categories' => Category::all(),
            'category_id' => $category_id,
            'sort' => $sort,
        ]);
    }

    private function resolveServiceCardImageUrl(?string $path, ?string $serviceTitle): string
    {
        return $this->serviceImageUrlResolver->resolveCardImageUrl($path, $serviceTitle);
    }

    public function edit(StudentService $service)
    {
        $user = Auth::user();
        if (! $user || $user->hu_id != $service->hss_user_id) {
            abort(403, 'You may only edit your own services.');
        }

        $categories = \App\Models\Category::all();
        $defaultSchedule = [
            'mon' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'tue' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'wed' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'thu' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'fri' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'sat' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
            'sun' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
        ];
        $scheduleData = $service->hss_operating_hours ?? $defaultSchedule;
        foreach ($defaultSchedule as $key => $val) {
            if (! isset($scheduleData[$key])) {
                $scheduleData[$key] = $val;
            }
        }
        $serviceImageUrl = $this->resolveServiceImageUrl($service->hss_image_path);

        $bookedSlots = \App\Models\ServiceRequest::where('hsr_student_service_id', $service->hss_id)
            ->whereIn('hsr_status', ['accepted', 'approved', 'in_progress'])
            ->get()
            ->map(function ($appointment) {
                // 1. Define $date
                $rawDate = $appointment->hsr_selected_dates;
                $date = is_array($rawDate) ? ($rawDate[0] ?? null) : $rawDate;

                // 2. Define $time (Take first 5 chars: "14:00:00" -> "14:00")
                $time = substr((string) $appointment->hsr_start_time, 0, 5);

                // 3. Return combined string
                return $date.' '.$time;
            });

        return view('services.edit', compact('service', 'categories', 'bookedSlots', 'scheduleData', 'serviceImageUrl'));
    }

    public function update(Request $request, StudentService $service): JsonResponse
    {
        // 1. Authorization
        $user = $request->user();
        if (! $user || $user->hu_id != $service->hss_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You may only update your own services.',
                'error' => 'You may only update your own services.',
            ], 403);
        }

        // 2. Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:h2u_categories,hc_id',
            'image' => 'nullable|image|max:2048',
            'template_image' => 'nullable|string',
            'description' => 'nullable|string',
            'blocked_slots' => 'nullable|string',
            'is_unavailable' => 'nullable',
            // Packages
            'packages' => 'nullable|array',
            'packages.*.duration' => 'nullable|string',
            'packages.*.frequency' => 'nullable|string',
            'packages.*.price' => 'nullable|numeric|min:0',
            'packages.*.description' => 'nullable|string',
            'offer_packages' => 'nullable',

            // Schedule & Availability
            'operating_hours' => 'nullable|array',
            'session_duration' => 'nullable|string',
            'unavailable_dates' => 'nullable|string',
            'is_session_based' => 'nullable', // 🟢 Added this so validation allows it
        ]);

        // 3. Handle Image
        if ($request->hasFile('image')) {

            // Delete old image if exists
            if ($service->hss_image_path && file_exists(public_path($service->hss_image_path))) {
                unlink(public_path($service->hss_image_path));
            }

            $file = $request->file('image');
            $filename = $file->hashName();

            // Make sure folder exists
            if (! file_exists(public_path('storage/services'))) {
                mkdir(public_path('storage/services'), 0755, true);
            }

            // Move new file
            $file->move(public_path('storage/services'), $filename);

            $service->hss_image_path = 'storage/services/'.$filename;

        } elseif ($request->filled('template_image') && ! $service->hss_image_path) {

            $service->hss_image_path = $request->input('template_image');
        }

        if ($request->filled('blocked_slots')) {
            // Decode the JSON string coming from frontend and re-encode to ensure valid JSON
            $service->hss_blocked_slots = json_decode($request->blocked_slots, true);
        } else {
            $service->hss_blocked_slots = [];
        }

        $isUnavailable = $request->has('is_unavailable'); // Check checkbox status

        if ($isUnavailable) {
            // Jika checkbox "Unavailable" ditanda
            $service->hss_is_active = false;      // 0
            $service->hss_status = 'unavailable'; // Set text column
        } else {
            // Jika checkbox "Unavailable" TIDAK ditanda (Available)
            $service->hss_is_active = true;       // 1
            $service->hss_status = 'available';   // Set text column
        }
        // 4. Update Basic Info
        $service->hss_title = $this->sanitizeInput($validated['title']);
        $service->hss_category_id = $validated['category_id'];
        $service->hss_description = $validated['description'] ?? '';

        // 5. Handle Packages (Same as before)
        $packages = $request->input('packages', []);
        $service->hss_basic_duration = $this->sanitizeInput($packages[0]['duration'] ?? null);
        $service->hss_basic_frequency = $this->sanitizeInput($packages[0]['frequency'] ?? null);
        $service->hss_basic_price = $packages[0]['price'] ?? null;
        $service->hss_basic_description = $packages[0]['description'] ?? null;

        if ($request->has('offer_packages')) {
            $service->hss_standard_duration = $this->sanitizeInput($packages[1]['duration'] ?? null);
            $service->hss_standard_frequency = $this->sanitizeInput($packages[1]['frequency'] ?? null);
            $service->hss_standard_price = $packages[1]['price'] ?? null;
            $service->hss_standard_description = $packages[1]['description'] ?? null;

            $service->hss_premium_duration = $this->sanitizeInput($packages[2]['duration'] ?? null);
            $service->hss_premium_frequency = $this->sanitizeInput($packages[2]['frequency'] ?? null);
            $service->hss_premium_price = $packages[2]['price'] ?? null;
            $service->hss_premium_description = $packages[2]['description'] ?? null;
        } else {
            $service->hss_standard_duration = null;
            $service->hss_standard_frequency = null;
            $service->hss_standard_price = null;
            $service->hss_standard_description = null;
            $service->hss_premium_duration = null;
            $service->hss_premium_frequency = null;
            $service->hss_premium_price = null;
            $service->hss_premium_description = null;
        }

        // 6. Handle Weekly Schedule (Same as before)
        $inputHours = $request->input('operating_hours', []);
        $cleanSchedule = [];
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        foreach ($days as $day) {
            $dayData = $inputHours[$day] ?? [];
            $cleanSchedule[$day] = [
                'enabled' => isset($dayData['enabled']) && $dayData['enabled'] == '1',
                'start' => $dayData['start'] ?? '09:00',
                'end' => $dayData['end'] ?? '17:00',
            ];
        }
        $service->hss_operating_hours = $cleanSchedule;

        // 7. Handle Block Dates 🟢 FIXED: Added json_encode
        $rawDates = $request->input('unavailable_dates');
        if ($rawDates) {
            $datesArray = array_values(array_filter(array_map('trim', explode(',', $rawDates))));
            // 🟢 FIX: You must encode the array to JSON before saving
            $service->hss_unavailable_dates = $datesArray;
        } else {
            $service->hss_unavailable_dates = [];
        }

        // 8. Session Duration 🟢 Logic is correct here
        if ($request->input('is_session_based') == '1') {
            $service->hss_session_duration = $request->input('session_duration', 60);
        } else {
            $service->hss_session_duration = null;
        }

        // 9. Save & Return
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully!',
            'service' => $service,
        ]);
    }

    public function destroy(Request $request, StudentService $service): JsonResponse
    {
        $user = $request->user();
        if (! $user || $user->hu_id != $service->hss_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You may only delete your own services.',
                'error' => 'You may only delete your own services.',
            ], 403);
        }

        // Hard delete
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
            'service' => $service,
        ], 200);
    }

    public function storefront(User $user): JsonResponse
    {
        if ($user->hu_role !== 'helper') {
            return response()->json([
                'success' => false,
                'message' => 'User is not a helper.',
                'error' => 'User is not a helper.',
            ], 422);
        }

        if ($user->hu_is_suspended == 1 || $user->hu_is_blacklisted == 1 || $user->hu_is_blocked == 1) {
            return response()->json([
                'success' => false,
                'message' => 'This user is currently unavailable.',
                'error' => 'This user is currently unavailable.',
            ], 404);
        }

        $services = StudentService::query()
            ->where('hss_user_id', $user->hu_id)
            ->where('hss_is_active', true)
            ->with('category')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'student' => [
                'id' => $user->hu_id,
                'name' => $user->hu_name,
                'badge' => $user->hu_trust_badge,
                'is_available' => $user->hu_is_available,
                'average_rating' => $user->hu_average_rating,
            ],
            'services' => $services,
        ], 200);
    }

    public function show(StudentService $service)
    {
        $service->loadMissing(['category', 'user']);
        $provider = $service->user;
        $authUser = Auth::user();
        $viewer = $authUser instanceof User ? $authUser : null;

        $canContactProvider = $viewer
            && $viewer->hu_id !== $provider->hu_id
            && $viewer->isCommunity()
            && $provider->isStudent();

        $isProviderFavorited = $viewer
            ? $viewer->favorites()->where('hf_favorited_user_id', $provider->hu_id)->exists()
            : false;

        return view('services.show', compact(
            'service',
            'provider',
            'viewer',
            'canContactProvider',
            'isProviderFavorited'
        ));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->hu_role !== 'helper') {
            abort(403, 'Only students can create services.');
        }

        // Get categories for the form
        $categories = \App\Models\Category::all();
        $defaultSchedule = [
            'mon' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'tue' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'wed' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'thu' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'fri' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'sat' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
            'sun' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
        ];

        return view('services.create', compact('categories', 'defaultSchedule'));
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();

            // 1. Authorization
            if (! $user || $user->hu_role !== 'helper') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only helpers can create services.',
                    'error' => 'Only helpers can create services.',
                ], 403);
            }

            if ($serviceId) {
                $service = StudentService::findOrFail($serviceId);
                if ($service->hss_user_id != $user->hu_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You may only edit your own services.',
                        'error' => 'You may only edit your own services.',
                    ], 403);
                }
            } else {
                $service = new StudentService;
                $service->hss_user_id = $user->hu_id;
                $service->hss_approval_status = 'pending';
                $service->hss_status = 'available';
                $service->hss_is_active = true;
            }

            // 3. Validation
            $rules = [
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:h2u_categories,hc_id',
                'image' => 'nullable|image|max:10240',
                'template_image' => 'nullable|string',
                'description' => 'required|string',
                'unavailable_dates' => 'nullable|string',
                'is_session_based' => 'nullable',
                'session_duration' => 'nullable|integer',

                // Packages...
                'packages.0.price' => 'required|numeric|min:0',
                'packages.0.duration' => 'nullable|string',
                'packages.0.frequency' => 'nullable|string',
                'packages.0.description' => 'nullable|string',
            ];

            $validated = $request->validate($rules);

            // 4. Save Overview
            $service->hss_title = $this->sanitizeInput($validated['title']);
            $service->hss_category_id = $validated['category_id'];
            $service->hss_description = $validated['description'];

            // 5. Image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = $file->hashName();

                if (! file_exists(public_path('storage/services'))) {
                    mkdir(public_path('storage/services'), 0755, true);
                }

                $file->move(public_path('storage/services'), $filename);
                $service->hss_image_path = 'storage/services/'.$filename;
            } elseif ($request->filled('template_image')) {
                $service->hss_image_path = $request->input('template_image');
            }

            // 6. Availability (Block Dates)
            $dates = $request->input('unavailable_dates');
            if ($dates) {
                $dateArray = array_filter(array_map('trim', explode(',', $dates)));
                $service->hss_unavailable_dates = array_values($dateArray);
            } else {
                $service->hss_unavailable_dates = [];
            }

            // 7. Packages
            $packages = $request->input('packages', []);
            $service->hss_basic_price = $packages[0]['price'] ?? 0;
            $service->hss_basic_duration = $this->sanitizeInput($packages[0]['duration'] ?? null);
            $service->hss_basic_frequency = $this->sanitizeInput($packages[0]['frequency'] ?? null);
            $service->hss_basic_description = $packages[0]['description'] ?? null;

            if (! empty($packages[1]['price'])) {
                $service->hss_standard_price = $packages[1]['price'];
                $service->hss_standard_duration = $this->sanitizeInput($packages[1]['duration'] ?? null);
                $service->hss_standard_frequency = $this->sanitizeInput($packages[1]['frequency'] ?? null);
                $service->hss_standard_description = $packages[1]['description'] ?? null;
            } else {
                $service->hss_standard_price = null;
            }

            if (! empty($packages[2]['price'])) {
                $service->hss_premium_price = $packages[2]['price'];
                $service->hss_premium_duration = $this->sanitizeInput($packages[2]['duration'] ?? null);
                $service->hss_premium_frequency = $this->sanitizeInput($packages[2]['frequency'] ?? null);
                $service->hss_premium_description = $packages[2]['description'] ?? null;
            } else {
                $service->hss_premium_price = null;
            }

            // 8. Handle Weekly Schedule
            $inputHours = $request->input('operating_hours', []);
            $cleanSchedule = [];
            $daysList = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

            foreach ($daysList as $day) {
                $dayData = $inputHours[$day] ?? [];
                $cleanSchedule[$day] = [
                    'enabled' => isset($dayData['enabled']) && ($dayData['enabled'] == '1' || $dayData['enabled'] === true),
                    'start' => $dayData['start'] ?? '09:00',
                    'end' => $dayData['end'] ?? '17:00',
                ];
            }
            $service->hss_operating_hours = $cleanSchedule;

            // 9. Session Duration Logic
            if ($request->input('is_session_based') == '1') {
                $service->hss_session_duration = $request->input('session_duration', 60);
            } else {
                $service->hss_session_duration = null;
            }

            // 10. Save
            $service->save();

            return response()->json([
                'success' => true,
                'message' => 'Service published successfully!',
                'service' => $service,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Service Store Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sanitize input strings to remove or replace special characters
     * that might cause SQL encoding errors (e.g., non-breaking hyphens, multiplication signs).
     */
    private function sanitizeInput($text)
    {
        if (is_null($text)) {
            return null;
        }

        $replacements = [
            "\xE2\x80\x91" => '-', // Non-breaking hyphen (the one in the error)
            "\xE2\x80\x90" => '-', // Hyphen
            "\xE2\x80\x92" => '-', // Figure dash
            "\xE2\x80\x93" => '-', // En dash
            "\xE2\x80\x94" => '-', // Em dash
            "\xC3\x97" => 'x', // Multiplication sign
            "\xE2\x80\x98" => "'", // Left single quote
            "\xE2\x80\x99" => "'", // Right single quote
            "\xE2\x80\x9C" => '"', // Left double quote
            "\xE2\x80\x9D" => '"', // Right double quote
        ];

        return strtr($text, $replacements);
    }

    public function manage(Request $request)
    {
        $user = $request->user();

        if (! $user || $user->hu_role !== 'helper') {
            abort(403, 'Only student helpers can manage services.');
        }

        $query = StudentService::query()
            ->where('hss_user_id', $user->hu_id)
            ->with('category');

        // Filter Search (Nama Service)
        if ($request->filled('search')) {
            $query->where('hss_title', 'like', '%'.$request->search.'%');
        }

        // Filter Category (Hanya yang ada dalam senarai student service user ini)
        if ($request->filled('category')) {
            $query->where('hss_category_id', $request->category);
        }

        $services = $query->orderByDesc('created_at')->get();
        $services->transform(function (StudentService $service) {
            $status = strtolower((string) $service->hss_approval_status);
            $service->ui_is_suspended = $status === 'suspended';
            $service->ui_image_url = $this->resolveServiceImageUrl($service->hss_image_path);
            $service->ui_badge_class = match ($status) {
                'pending' => 'bg-amber-500 text-white',
                'rejected' => 'bg-red-500 text-white',
                default => 'bg-gray-500 text-white',
            };
            $service->ui_badge_icon = match ($status) {
                'pending' => '<i class="fa-solid fa-clock mr-1"></i>',
                'rejected' => '<i class="fa-solid fa-circle-xmark mr-1"></i>',
                default => '',
            };

            return $service;
        });

        $servicesByStatus = [
            'all' => $services,
            'pending' => $services->where('hss_approval_status', 'pending')->values(),
            'approved' => $services->where('hss_approval_status', 'approved')->values(),
            'rejected' => $services->where('hss_approval_status', 'rejected')->values(),
        ];

        // Dapatkan senarai kategori unik daripada servis milik user ini sahaja untuk filter dropdown
        $categories = $user->studentServices() // Pastikan ada relationship 'studentServices' di User model
            ->with('category')
            ->get()
            ->pluck('category')
            ->unique('hc_id')
            ->filter();

        return view('services.manage', compact('services', 'categories', 'servicesByStatus'));
    }

    private function resolveServiceImageUrl(?string $path, ?string $fallback = null): string
    {
        return $this->serviceImageUrlResolver->resolveGeneralImageUrl($path, $fallback);
    }


    public function approve(StudentService $service)
    {
        $user = Auth::user();
        if ($user->hu_role !== 'admin') {
            abort(403, 'You are not authorized to approve services.');
        }

        $service->hss_approval_status = 'approved';
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service approved.',
        ]);
    }

    public function reject(StudentService $service)
    {
        $user = Auth::user();
        if ($user->hu_role !== 'admin') {
            abort(403, 'You are not authorized to reject services.');
        }

        $service->hss_approval_status = 'rejected';
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service rejected.',
        ]);
    }

    public function details(Request $request, $id)
    {
        $service = StudentService::with(['user', 'category', 'orders'])->findOrFail($id);
        if ($service->user->hu_is_suspended == 1 || $service->user->hu_is_blacklisted == 1 || $service->user->hu_is_blocked == 1) {
            // Return 404 Not Found so no info is displayed
            abort(404);
        }
        $viewer = $request->user();

        // Fetch orders for this service (Stats logic)
        $orders = ServiceRequest::where('hsr_student_service_id', $service->hss_id)
            ->whereIn('hsr_status', ['completed', 'accepted'])
            ->get();

        $service->min_price = $orders->min('hsr_offered_price') ?? 0;
        $service->max_price = $orders->max('hsr_offered_price') ?? 0;

        // Completed orders count
        $service->completed_orders = $service->orders()
            ->whereIn('hsr_status', ['completed', 'accepted'])
            ->count();

        // Fetch Reviews
        $reviews = Review::where('hr_student_service_id', $service->hss_id)
            ->where('hr_reviewee_id', $service->hss_user_id)
            ->with('reviewer')
            ->latest()
            ->get();
        $isAuthenticatedViewer = (bool) $viewer;
        $reviews = $reviews->map(function ($review) use ($isAuthenticatedViewer) {
            $reviewerName = $review->reviewer->hu_name ?? 'User';
            $review->ui_reviewer_role = $review->reviewer->hu_role ?? 'student';
            $review->ui_replied_ago = $review->hr_replied_at ? Carbon::parse($review->hr_replied_at)->diffForHumans() : null;
            $review->ui_reviewer_initial = Str::substr($reviewerName, 0, 1);
            $review->ui_reviewer_display_name = $isAuthenticatedViewer
                ? $reviewerName
                : Str::substr($reviewerName, 0, 1).'****';
            $review->ui_created_human = optional($review->hr_created_at)->diffForHumans() ?? 'Recently';

            return $review;
        });

        $service->hss_rating = $reviews->count() > 0 ? round($reviews->avg('hr_rating'), 1) : 0;

        // Optional: calculate average delivery time
        $service->avg_days = $orders->avg(function ($order) {
            $rawDate = $order->hsr_selected_dates;
            $date = is_array($rawDate) ? ($rawDate[0] ?? now()->toDateString()) : $rawDate;
            return Carbon::parse($date)->diffInDays(now());
        }) ?? 0;

        $manualBlocks = $service->hss_blocked_slots;
        if (is_string($manualBlocks)) {
            $manualBlocks = json_decode($manualBlocks, true);
        }
        // Fallback if null
        $manualBlocks = $manualBlocks ?? [];
        $bookedAppointments = ServiceRequest::where('hsr_student_service_id', $service->hss_id)
            ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress', 'approved']) // statuses that block the calendar
            ->get()
            ->map(function ($appointment) {
                $rawDate = $appointment->hsr_selected_dates;

                return [
                    // Ensure date is Y-m-d string
                    'date' => is_array($rawDate) ? ($rawDate[0] ?? null) : $rawDate,
                    'start_time' => substr((string) $appointment->hsr_start_time, 0, 5), // Format HH:MM
                    'end_time' => substr((string) $appointment->hsr_end_time, 0, 5),   // Format HH:MM
                ];
            });

        $hasActiveRequest = false;
        if ($viewer) {
            $hasActiveRequest = ServiceRequest::where('hsr_requester_id', $viewer->hu_id)
                ->where('hsr_student_service_id', $service->hss_id)
                ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress'])
                ->exists();
        }

        $detailsHolidays = $service->hss_unavailable_dates;
        if (is_string($detailsHolidays)) {
            $decodedHolidays = json_decode($detailsHolidays, true);
            $detailsHolidays = is_array($decodedHolidays) ? $decodedHolidays : [];
        }
        $detailsHolidays = is_array($detailsHolidays) ? $detailsHolidays : [];

        $detailsSchedule = is_array($service->hss_operating_hours) ? $service->hss_operating_hours : [];
        $detailsPackages = [
            'basic' => [
                'price' => $service->hss_basic_price ?? 0,
                'description' => $service->hss_basic_description ?? '',
                'duration' => $service->hss_basic_duration ?? '',
                'frequency' => $service->hss_basic_frequency ?? '',
            ],
            'standard' => [
                'price' => $service->hss_standard_price ?? 0,
                'description' => $service->hss_standard_description ?? '',
                'duration' => $service->hss_standard_duration ?? '',
                'frequency' => $service->hss_standard_frequency ?? '',
            ],
            'premium' => [
                'price' => $service->hss_premium_price ?? 0,
                'description' => $service->hss_premium_description ?? '',
                'duration' => $service->hss_premium_duration ?? '',
                'frequency' => $service->hss_premium_frequency ?? '',
            ],
        ];
        $detailsCurrentPackage = $service->hss_basic_price ? 'basic' : ($service->hss_standard_price ? 'standard' : 'premium');
        $detailsSessionDuration = (int) ($service->hss_session_duration ?? 60);
        $detailsImagePlaceholder = 'https://ui-avatars.com/api/?name='.urlencode($service->hss_title ?? 'Service');
        $detailsImageUrl = ! empty($service->hss_image_path) ? $this->resolveServiceImageUrl($service->hss_image_path, $detailsImagePlaceholder) : '';
        $detailsWhatsappUrl = $this->buildServiceWhatsappUrl($service);
        $detailsHasPhone = ! empty($detailsWhatsappUrl);
        $providerName = $service->user->hu_name ?? 'User';
        $providerMaskedName = Str::substr($providerName, 0, 1).'****';
        $isFavouritedByViewer = $isAuthenticatedViewer && (bool) ($service->is_favourited ?? false);
        $daysMap = [
            'mon' => 'Monday',
            'tue' => 'Tuesday',
            'wed' => 'Wednesday',
            'thu' => 'Thursday',
            'fri' => 'Friday',
            'sat' => 'Saturday',
            'sun' => 'Sunday',
        ];
        $detailsOperatingDays = [];
        foreach ($daysMap as $key => $dayName) {
            $d = $detailsSchedule[$key] ?? [];
            $isOpen = isset($d['enabled']) && $d['enabled'] == true;
            $detailsOperatingDays[] = [
                'name' => $dayName,
                'is_open' => $isOpen,
                'start' => $d['start'] ?? '09:00',
                'end' => $d['end'] ?? '17:00',
                'is_today' => strtolower(now()->format('D')) == strtolower(substr($dayName, 0, 3)),
            ];
        }

        $hasActiveRequest = false;
        if ($viewer) {
            $hasActiveRequest = ServiceRequest::where('hsr_requester_id', $viewer->hu_id)
                ->where('hsr_student_service_id', $service->hss_id)
                ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress'])
                ->exists();
        }

        $detailsHolidays = $service->hss_unavailable_dates;
        if (is_string($detailsHolidays)) {
            $decodedHolidays = json_decode($detailsHolidays, true);
            $detailsHolidays = is_array($decodedHolidays) ? $decodedHolidays : [];
        }
        $detailsHolidays = is_array($detailsHolidays) ? $detailsHolidays : [];

        $detailsSchedule = is_array($service->hss_operating_hours) ? $service->hss_operating_hours : [];
        $detailsPackages = [
            'basic' => [
                'price' => $service->hss_basic_price ?? 0,
                'description' => $service->hss_basic_description ?? '',
                'duration' => $service->hss_basic_duration ?? '',
                'frequency' => $service->hss_basic_frequency ?? '',
            ],
            'standard' => [
                'price' => $service->hss_standard_price ?? 0,
                'description' => $service->hss_standard_description ?? '',
                'duration' => $service->hss_standard_duration ?? '',
                'frequency' => $service->hss_standard_frequency ?? '',
            ],
            'premium' => [
                'price' => $service->hss_premium_price ?? 0,
                'description' => $service->hss_premium_description ?? '',
                'duration' => $service->hss_premium_duration ?? '',
                'frequency' => $service->hss_premium_frequency ?? '',
            ],
        ];
        $detailsCurrentPackage = $service->hss_basic_price ? 'basic' : ($service->hss_standard_price ? 'standard' : 'premium');
        $detailsSessionDuration = (int) ($service->hss_session_duration ?? 60);
        $detailsImagePlaceholder = 'https://ui-avatars.com/api/?name=' . urlencode($service->hss_title ?? 'Service');
        $detailsImageUrl = !empty($service->hss_image_path) ? $this->resolveServiceImageUrl($service->hss_image_path, $detailsImagePlaceholder) : '';
        $detailsWhatsappUrl = $this->buildServiceWhatsappUrl($service);
        $detailsHasPhone = !empty($detailsWhatsappUrl);
        $providerName = $service->user->hu_name ?? 'User';
        $providerMaskedName = Str::substr($providerName, 0, 1) . '****';
        $isFavouritedByViewer = $isAuthenticatedViewer && (bool) ($service->is_favourited ?? false);
        $daysMap = [
            'mon' => 'Monday',
            'tue' => 'Tuesday',
            'wed' => 'Wednesday',
            'thu' => 'Thursday',
            'fri' => 'Friday',
            'sat' => 'Saturday',
            'sun' => 'Sunday',
        ];
        $detailsOperatingDays = [];
        foreach ($daysMap as $key => $dayName) {
            $d = $detailsSchedule[$key] ?? [];
            $isOpen = isset($d['enabled']) && $d['enabled'] == true;
            $detailsOperatingDays[] = [
                'name' => $dayName,
                'is_open' => $isOpen,
                'start' => $d['start'] ?? '09:00',
                'end' => $d['end'] ?? '17:00',
                'is_today' => strtolower(now()->format('D')) == strtolower(substr($dayName, 0, 3)),
            ];
        }

        return view('services.details', [
            'service' => $service,
            'provider' => $service->user,
            'viewer' => $viewer,
            'reviews' => $reviews,
            'manualBlocks' => $manualBlocks,
            'bookedAppointments' => $bookedAppointments,
            'hasActiveRequest' => $hasActiveRequest,
            'detailsHolidays' => $detailsHolidays,
            'detailsSchedule' => $detailsSchedule,
            'detailsPackages' => $detailsPackages,
            'detailsCurrentPackage' => $detailsCurrentPackage,
            'detailsSessionDuration' => $detailsSessionDuration,
            'detailsImageUrl' => $detailsImageUrl,
            'detailsImagePlaceholder' => $detailsImagePlaceholder,
            'detailsWhatsappUrl' => $detailsWhatsappUrl,
            'detailsHasPhone' => $detailsHasPhone,
            'detailsOperatingDays' => $detailsOperatingDays,
            'detailsUi' => [
                'is_authenticated' => $isAuthenticatedViewer,
                'provider_display_name' => $isAuthenticatedViewer ? $providerName : $providerMaskedName,
                'provider_masked_name' => $providerMaskedName,
                'provider_initial_upper' => Str::upper(Str::substr($providerName, 0, 1)),
                'rating_display' => number_format((float) ($service->hss_rating ?? 0), 1),
                'favourite_button_class' => $isFavouritedByViewer
                    ? 'bg-red-50 text-red-500 border-red-100'
                    : 'text-gray-600 hover:bg-gray-50 hover:border-gray-300',
                'favourite_icon_class' => $isFavouritedByViewer ? 'fas' : 'far',
                'favourite_text' => $isFavouritedByViewer ? 'Saved' : 'Save',
            ],
        ]);
    }

    private function buildServiceWhatsappUrl(StudentService $service): ?string
    {
        $rawPhone = $service->user->hu_phone_number ?? ($service->user->hu_phone ?? '');
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = '60'.substr($cleanPhone, 1);
        }
        if (empty($cleanPhone)) {
            return null;
        }
        return "https://wa.me/{$cleanPhone}?text=Hi, I am interested in your service: ".urlencode($service->hss_title);
    }
}
