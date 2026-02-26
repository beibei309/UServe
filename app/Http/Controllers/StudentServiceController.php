<?php

namespace App\Http\Controllers;

use App\Models\StudentService;
use App\Models\User;
use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentServiceController extends Controller
{

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
            $query->whereColumn('reviews.reviewee_id', 'student_services.user_id');
        }])
        ->withAvg(['reviews' => function ($query) {
            $query->whereColumn('reviews.reviewee_id', 'student_services.user_id');
        }], 'rating')
        ->where('approval_status', 'approved')
        // START MODIFICATION
        ->whereHas('student', function ($q) {
            $q->where('role', 'helper')
              ->where('is_suspended', 0)     // Exclude suspended users
              ->where('is_blacklisted', 0);  // Exclude blacklisted  users
        });

    if ($currentUserId) {
        $query->where('user_id', '!=', $currentUserId);
    }

    
    if ($available_only === '1') {
        // Jika user nak cari yang Available sahaja
        $query->where('status', 'available');
    } elseif ($available_only === '0') {
        // Jika user nak cari yang Busy (Unavailable) sahaja
        $query->where('status', 'unavailable');
    }
    // Jika $available_only null/kosong, ia akan tunjuk KEDUA-DUA (Available & Busy)

    // --- 4. Search filter ---
    if ($q) {
        $query->where(function ($sub) use ($q) {
            $sub->where('title', 'like', "%$q%")
                ->orWhere('description', 'like', "%$q%");
        });
    }

    // --- 5. Category filter ---
    if ($category_id) {
        $query->where('category_id', $category_id);
    }

    // --- 6. Sorting ---
    if ($sort == 'newest') {
        $query->orderBy('created_at', 'desc');
    } elseif ($sort == 'oldest') {
        $query->orderBy('created_at', 'asc');
    } elseif ($sort == 'price_low') {
        $query->orderBy('basic_price', 'asc'); 
    } elseif ($sort == 'price_high') {
        $query->orderBy('basic_price', 'desc');
    }

    $services = $query->paginate(5);

    return view('services.index', [
        'services' => $services,
        'categories' => Category::all(),
        'category_id' => $category_id,
        'sort' => $sort,
    ]);
}

  public function edit(StudentService $service)
{
        $user = Auth::user();
    if (!$user || $user->id != $service->user_id) {
        abort(403, 'You may only edit your own services.');
    }

    $categories = \App\Models\Category::all();

    $bookedSlots = \App\Models\ServiceRequest::where('student_service_id', $service->id)
        ->whereIn('status', ['accepted', 'approved', 'in_progress']) 
        ->get()
        ->map(function ($appointment) {
            // 1. Define $date
            $date = $appointment->selected_dates instanceof \Carbon\Carbon 
                ? $appointment->selected_dates->format('Y-m-d') 
                : $appointment->selected_dates;
            
            // 2. Define $time (Take first 5 chars: "14:00:00" -> "14:00")
            $time = substr($appointment->start_time, 0, 5);
            
            // 3. Return combined string
            return $date . ' ' . $time;
        });

    return view('services.edit', compact('service', 'categories', 'bookedSlots'));
}

public function update(Request $request, StudentService $service): JsonResponse
{
    // 1. Authorization
    $user = $request->user();
   if (!$user || $user->id != $service->user_id) {
        return response()->json(['error' => 'You may only update your own services.'], 403);
    }

    // 2. Validation
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
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
    if ($service->image_path && file_exists(public_path($service->image_path))) {
        unlink(public_path($service->image_path));
    }

    $file = $request->file('image');
    $filename = $file->hashName();

    // Make sure folder exists
    if (!file_exists(public_path('storage/services'))) {
        mkdir(public_path('storage/services'), 0755, true);
    }

    // Move new file
    $file->move(public_path('storage/services'), $filename);

    $service->image_path = 'storage/services/' . $filename;

} elseif ($request->filled('template_image') && !$service->image_path) {

    $service->image_path = $request->input('template_image');
}


    if ($request->filled('blocked_slots')) {
        // Decode the JSON string coming from frontend and re-encode to ensure valid JSON
        $service->blocked_slots = json_decode($request->blocked_slots);
    } else {
        $service->blocked_slots = [];
    }

$isUnavailable = $request->has('is_unavailable'); // Check checkbox status

    if ($isUnavailable) {
        // Jika checkbox "Unavailable" ditanda
        $service->is_active = false;      // 0
        $service->status = 'unavailable'; // Set text column
    } else {
        // Jika checkbox "Unavailable" TIDAK ditanda (Available)
        $service->is_active = true;       // 1
        $service->status = 'available';   // Set text column
    }
    // 4. Update Basic Info
    $service->title = $validated['title'];
    $service->category_id = $validated['category_id'];
    $service->description = $validated['description'] ?? '';

    // 5. Handle Packages (Same as before)
    $packages = $request->input('packages', []);
    $service->basic_duration    = $packages[0]['duration'] ?? null;
    $service->basic_frequency   = $packages[0]['frequency'] ?? null;
    $service->basic_price       = $packages[0]['price'] ?? null;
    $service->basic_description = $packages[0]['description'] ?? null;

    if ($request->has('offer_packages')) {
        $service->standard_duration    = $packages[1]['duration'] ?? null;
        $service->standard_frequency   = $packages[1]['frequency'] ?? null;
        $service->standard_price       = $packages[1]['price'] ?? null;
        $service->standard_description = $packages[1]['description'] ?? null;

        $service->premium_duration     = $packages[2]['duration'] ?? null;
        $service->premium_frequency    = $packages[2]['frequency'] ?? null;
        $service->premium_price        = $packages[2]['price'] ?? null;
        $service->premium_description  = $packages[2]['description'] ?? null;
    } else {
        $service->standard_duration = null; $service->standard_frequency = null;
        $service->standard_price = null;    $service->standard_description = null;
        $service->premium_duration = null;  $service->premium_frequency = null;
        $service->premium_price = null;     $service->premium_description = null;
    }

    // 6. Handle Weekly Schedule (Same as before)
    $inputHours = $request->input('operating_hours', []);
    $cleanSchedule = [];
    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    foreach ($days as $day) {
        $dayData = $inputHours[$day] ?? [];
        $cleanSchedule[$day] = [
            'enabled' => isset($dayData['enabled']) && $dayData['enabled'] == '1',
            'start'   => $dayData['start'] ?? '09:00',
            'end'     => $dayData['end'] ?? '17:00',
        ];
    }
    $service->operating_hours = $cleanSchedule; 

    // 7. Handle Block Dates 🟢 FIXED: Added json_encode
    $rawDates = $request->input('unavailable_dates');
    if ($rawDates) {
        $datesArray = array_values(array_filter(array_map('trim', explode(',', $rawDates))));
        // 🟢 FIX: You must encode the array to JSON before saving
        $service->unavailable_dates = json_encode($datesArray); 
    } else {
        $service->unavailable_dates = json_encode([]);
    }

    // 8. Session Duration 🟢 Logic is correct here
    if ($request->input('is_session_based') == '1') {
        $service->session_duration = $request->input('session_duration', 60);
    } else {
        $service->session_duration = null;
    }

    // 9. Save & Return
    $service->save();

    return response()->json([
        'success' => true,
        'message' => 'Service updated successfully!',
        'service' => $service
    ]);
}   


    public function destroy(Request $request, StudentService $service): JsonResponse
    {
        $user = $request->user();
        if (!$user || $user->id != $service->user_id) {
            return response()->json(['error' => 'You may only delete your own services.'], 403);
        }

        // Hard delete
        $service->delete();

        return response()->json(['service' => $service], 200);
    }

    public function storefront(User $user): JsonResponse
    {
        if ($user->role !== 'helper') {
            return response()->json(['error' => 'User is not a student.'], 422);
        }

	if ($user->is_suspended == 1 || $user->is_blacklisted  == 1) {
        return response()->json(['error' => 'This user is currently unavailable.'], 404);
    }

        $services = StudentService::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with('category')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'student' => [
                'id' => $user->id,
                'name' => $user->name,
                'badge' => $user->trust_badge,
                'is_available' => $user->is_available,
                'average_rating' => $user->average_rating,
            ],
            'services' => $services,
        ], 200);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'helper') {
            abort(403, 'Only students can create services.');
        }

        // Get categories for the form
        $categories = \App\Models\Category::all();

        return view('services.create', compact('categories'));
    }

   public function store(Request $request)
{
    $user = $request->user();

    // 1. Authorization
    if (!$user || $user->role !== 'helper') {
        abort(403, 'Only helpers can create services.');
    }

    // 2. Determine if Creating or Updating
    $serviceId = $request->input('service_id');

    if ($serviceId) {
        $service = StudentService::findOrFail($serviceId);
        if ($service->user_id != $user->id) {
            return response()->json(['error' => 'You may only edit your own services.'], 403);
        }
    } else {
        $service = new StudentService();
        $service->user_id = $user->id;
        $service->approval_status = 'pending';
        $service->status = 'available';
        $service->is_active = true;
    }

    // 3. Validation
    $rules = [
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:10240',
        'template_image' => 'nullable|string',
        'description' => 'required|string',
        'unavailable_dates' => 'nullable|string',
        'is_session_based' => 'nullable',
        'session_duration' => 'nullable|integer', // Added validation for this

        // Packages...
        'packages.0.price' => 'required|numeric|min:0',
        'packages.0.duration' => 'nullable|string',
        'packages.0.frequency' => 'nullable|string',
        'packages.0.description' => 'nullable|string',
        'packages.1.price' => 'nullable|numeric|min:0',
        'packages.1.duration' => 'nullable|string',
        'packages.1.frequency' => 'nullable|string',
        'packages.1.description' => 'nullable|string',
        'packages.2.price' => 'nullable|numeric|min:0',
        'packages.2.duration' => 'nullable|string',
        'packages.2.frequency' => 'nullable|string',
        'packages.2.description' => 'nullable|string',
    ];

    $validated = $request->validate($rules);

    // 4. Save Overview
    $service->title = $validated['title'];
    $service->category_id = $validated['category_id'];
    $service->description = $validated['description'];

    // 5. Image
    if ($request->hasFile('image')) {

    $file = $request->file('image');
    $filename = $file->hashName();

    // Make sure folder exists
    if (!file_exists(public_path('storage/services'))) {
        mkdir(public_path('storage/services'), 0755, true);
    }

    // Move file to public/storage/services
    $file->move(public_path('storage/services'), $filename);

    // Save path in DB
    $service->image_path = 'storage/services/' . $filename;

} elseif ($request->filled('template_image')) {

    $service->image_path = $request->input('template_image');
}

    // 6. Availability (Block Dates)
    $dates = $request->input('unavailable_dates');
    if ($dates) {
        $dateArray = array_map('trim', explode(',', $dates));
        $service->unavailable_dates = json_encode(array_values($dateArray));
    } else {
        $service->unavailable_dates = json_encode([]);
    }

    // 7. Packages
    $packages = $request->input('packages', []);
    $service->basic_price       = $packages[0]['price'] ?? 0;
    $service->basic_duration    = $packages[0]['duration'] ?? null;
    $service->basic_frequency   = $packages[0]['frequency'] ?? null;
    $service->basic_description = $packages[0]['description'] ?? null;

    if (!empty($packages[1]['price'])) {
        $service->standard_price       = $packages[1]['price'];
        $service->standard_duration    = $packages[1]['duration'] ?? null;
        $service->standard_frequency   = $packages[1]['frequency'] ?? null;
        $service->standard_description = $packages[1]['description'] ?? null;
    }
    if (!empty($packages[2]['price'])) {
        $service->premium_price       = $packages[2]['price'];
        $service->premium_duration    = $packages[2]['duration'] ?? null;
        $service->premium_frequency   = $packages[2]['frequency'] ?? null;
        $service->premium_description = $packages[2]['description'] ?? null;
    }

    // 🟢 8. Handle Weekly Schedule (THIS WAS MISSING)
    $inputHours = $request->input('operating_hours', []);
    $cleanSchedule = [];
    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    foreach ($days as $day) {
        $dayData = $inputHours[$day] ?? [];
        $cleanSchedule[$day] = [
            'enabled' => isset($dayData['enabled']) && $dayData['enabled'] == '1',
            'start'   => $dayData['start'] ?? '09:00',
            'end'     => $dayData['end'] ?? '17:00',
        ];
    }
    $service->operating_hours = $cleanSchedule; // Laravel casts this to JSON automatically if model is set up

    // 9. Session Duration Logic
    // If user selected "One-off Task", this sets duration to NULL.
    if ($request->input('is_session_based') == '1') {
        $service->session_duration = $request->input('session_duration', 60);
    } else {
        $service->session_duration = null;
    }

    // 10. Save
    $service->save();

    return response()->json([
        'success' => true,
        'message' => 'Service published successfully!',
        'service' => $service
    ]);
}

   public function manage(Request $request)
{
    $user = $request->user();

    if (!$user || $user->role !== 'helper') {
        abort(403, 'Only student helpers can manage services.');
    }

    $query = StudentService::query()
        ->where('user_id', $user->id)
        ->with('category');

    // Filter Search (Nama Service)
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filter Category (Hanya yang ada dalam senarai student service user ini)
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    $services = $query->orderByDesc('created_at')->get();

    // Dapatkan senarai kategori unik daripada servis milik user ini sahaja untuk filter dropdown
    $categories = $user->studentServices() // Pastikan ada relationship 'studentServices' di User model
        ->with('category')
        ->get()
        ->pluck('category')
        ->unique('id')
        ->filter();

    return view('services.manage', compact('services', 'categories'));
}

    public function approve(StudentService $service)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'You are not authorized to approve services.');
        }

        $service->approval_status = 'approved';
        $service->save();

        return response()->json(['success' => 'Service approved.']);
    }

    public function reject(StudentService $service)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'You are not authorized to reject services.');
        }

        $service->approval_status = 'rejected';
        $service->save();

        return response()->json(['success' => 'Service rejected.']);
    }

    public function details(Request $request, $id)
    {
        $service = StudentService::with(['user', 'category', 'orders'])->findOrFail($id);
	if ($service->user->is_suspended == 1 || $service->user->is_blacklisted == 1) {
        // Return 404 Not Found so no info is displayed
        abort(404); 
    }
        $viewer = $request->user(); 

        // Fetch orders for this service (Stats logic)
        $orders = ServiceRequest::where('student_service_id', $service->id)
                    ->whereIn('status', ['completed', 'accepted'])
                    ->get();

        $service->min_price = $orders->min('offered_price') ?? 0;
        $service->max_price = $orders->max('offered_price') ?? 0;

        // Completed orders count
        $service->completed_orders = $service->orders()
            ->whereIn('status', ['completed', 'accepted'])
            ->count();

        // Fetch Reviews
       $reviews = Review::where('student_service_id', $service->id)
            ->where('reviewee_id', $service->user_id)
            ->with('reviewer') 
            ->latest()
            ->get();

        $service->rating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;

        // Optional: calculate average delivery time
        $service->avg_days = $orders->avg(function($order) {
            return \Carbon\Carbon::parse($order->selected_dates)->diffInDays(now());
        }) ?? 0;

        $manualBlocks = $service->blocked_slots;
        if (is_string($manualBlocks)) {
            $manualBlocks = json_decode($manualBlocks, true);
        }
        // Fallback if null
        $manualBlocks = $manualBlocks ?? [];
        
        $bookedAppointments = ServiceRequest::where('student_service_id', $service->id)
        ->whereIn('status', ['pending', 'accepted', 'in_progress', 'approved']) // statuses that block the calendar
        ->get()
        ->map(function ($appointment) {
            return [
                // Ensure date is Y-m-d string
                'date'       => $appointment->selected_dates instanceof \Carbon\Carbon 
                                ? $appointment->selected_dates->format('Y-m-d') 
                                : $appointment->selected_dates, 
                'start_time' => substr($appointment->start_time, 0, 5), // Format HH:MM
                'end_time'   => substr($appointment->end_time, 0, 5),   // Format HH:MM
            ];
        });

        return view('services.details', [
            'service' => $service,
            'provider' => $service->user,
            'viewer' => $viewer,
            'reviews' => $reviews,
            'manualBlocks' => $manualBlocks,
            'bookedAppointments' => $bookedAppointments, 
        ]);
    }

    

    


}