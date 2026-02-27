<?php

namespace App\Http\Controllers;
use App\Models\StudentService;
use App\Models\User;
use Carbon\Carbon;

use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Auth;

class StudentsController extends Controller
{
   public function index(Request $request)
{
    $user = Auth::user();

    // Range handling (default last 30 days)
    $range = $request->get('range', '30days'); // possible: 30days, 3months, yearly

    if ($range === '3months') {
        $start = Carbon::now()->subMonths(3);
        $interval = 'day'; // still show daily within range
    } elseif ($range === 'yearly') {
        $start = Carbon::now()->subYear();
        $interval = 'month'; // for year show monthly aggregation (more practical)
    } else {
        $start = Carbon::now()->subDays(29); // last 30 days inclusive
        $interval = 'day';
    }

    // Build labels ($dates) depending on interval
    $labels = [];
    $period = [];

    if ($interval === 'day') {
        $days = $start->diffInDays(Carbon::now());
        for ($i = 0; $i <= $days; $i++) {
            $d = $start->copy()->addDays($i);
            $labels[] = $d->format('M j, Y'); // "Nov 15, 2025"
            $period[] = $d->format('Y-m-d');
        }
    } else { // month
        $months = $start->diffInMonths(Carbon::now());
        for ($i = 0; $i <= $months; $i++) {
            $m = $start->copy()->addMonths($i);
            $labels[] = $m->format('M Y'); // "Nov 2025"
            $period[] = $m->format('Y-m'); // use Y-m for grouping
        }
    }

    // Initialize arrays
    $sales = [];
    $cancelled = [];
    $completedDaily = [];
    $newOrders = [];

    // Fill arrays by iterating period buckets
    foreach ($period as $p) {
        if ($interval === 'day') {
            // sales = sum price for completed on that date
            $sales[] = ServiceRequest::whereDate('created_at', $p)
                        ->where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'completed')
                        ->sum('hsr_offered_price'); // or 'price' as your column

            $cancelled[] = ServiceRequest::whereDate('created_at', $p)
                        ->where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'cancelled')
                        ->sum('hsr_offered_price');

            $completedDaily[] = ServiceRequest::whereDate('created_at', $p)
                        ->where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'completed')
                        ->count();

            $newOrders[] = ServiceRequest::whereDate('created_at', $p)
                        ->where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'pending')
                        ->count();
        } else { // month aggregation
            $sales[] = ServiceRequest::whereYear('created_at', substr($p,0,4))
                        ->whereMonth('created_at', substr($p,5,2))
                        ->where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'completed')
                        ->sum('hsr_offered_price');

            $cancelled[] = ServiceRequest::whereYear('created_at', substr($p,0,4))
                        ->whereMonth('created_at', substr($p,5,2))
                        ->where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'cancelled')
                        ->sum('hsr_offered_price');

            $completedDaily[] = ServiceRequest::whereYear('created_at', substr($p,0,4))
                        ->whereMonth('created_at', substr($p,5,2))
                        ->where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'completed')
                        ->count();

            $newOrders[] = ServiceRequest::whereYear('created_at', substr($p,0,4))
                        ->whereMonth('created_at', substr($p,5,2))
                        ->where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'pending')
                        ->count();
        }
    }

    // Other small stats you already had
    $averageRating = $user->reviewsReceived()->avg('hr_rating');
    $averageRating = $averageRating ? round($averageRating, 1) : '-';

    $completedOrders = ServiceRequest::where('hsr_provider_id', $user->hu_id)
                        ->where('hsr_status', 'completed')
                        ->count();

    return view('students.index', compact(
        'user',
        'averageRating',
        'completedOrders',
        'labels',
        'sales',
        'cancelled',
        'completedDaily',
        'newOrders',
        'range'
    ));
}

     public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'profile_photo' => 'nullable|image|max:4096',
            'faculty' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|string|max:500',
            'work_experience_message' => 'nullable|string|max:1000',
            'work_experience_file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:4096',
        ]);

        $user = Auth::user(); // logged-in user

        // Profile photo upload (normalized with app convention: public/profile-photos)
        if ($request->hasFile('profile_photo')) {
            if ($user->hu_profile_photo_path && file_exists(public_path($user->hu_profile_photo_path))) {
                unlink(public_path($user->hu_profile_photo_path));
            }

            $file = $request->file('profile_photo');
            $filename = $file->hashName();

            if (!file_exists(public_path('profile-photos'))) {
                mkdir(public_path('profile-photos'), 0755, true);
            }

            $file->move(public_path('profile-photos'), $filename);
            $user->hu_profile_photo_path = 'profile-photos/' . $filename;
        }

        // Update basic fields
        $user->hu_faculty = $validated['faculty'] ?? $user->hu_faculty;
        $user->hu_course = $validated['course'] ?? $user->hu_course;
        $user->hu_bio = $validated['bio'] ?? $user->hu_bio;
        $user->skills = $validated['skills'] ?? $user->skills;
        $user->hu_work_experience_message = $validated['work_experience_message'] ?? $user->hu_work_experience_message;

        // Handle work experience file
        if ($request->hasFile('work_experience_file')) {
            $file = $request->file('work_experience_file');
            $filename = $file->hashName();
            $storedPath = Storage::disk('public')->putFileAs('uploads/work_experience', $file, $filename);

            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                $user->hu_work_experience_file = $storedPath;
            } else {
                return back()
                    ->withInput()
                    ->withErrors(['work_experience_file' => 'Resume upload failed. Please try again.']);
            }
        }

        // **Mark user as helper**
        $user->hu_role = 'helper'; // or $user->status = 'helper', depending on your DB column
        $user->save();

        return redirect()->route('students.create')
                        ->with('status', 'Profile updated successfully!')
                        ->with('ready_to_help', true);
    }

    public function edit()
    {
        $user = Auth::user();
        return view('students.edit-profile', compact('user'));
        
    }


    public function update(Request $request)
{
    $user = Auth::user();

    // 1. Validate inputs
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'faculty' => 'nullable|string|max:255',
        'course' => 'nullable|string|max:255',
        'bio' => 'nullable|string|max:1000',
        'skills' => 'nullable|string|max:500',
        'work_experience_message' => 'nullable|string|max:1000',
        'work_experience_file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240', // 10MB
        'profile_photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096', 
    ]);

    // 2. Update Basic Information
    $user->hu_name = $validated['name'];
    $user->hu_faculty = $validated['faculty'] ?? $user->hu_faculty;
    $user->hu_course = $validated['course'] ?? $user->hu_course;
    $user->hu_bio = $validated['bio'];
    $user->skills = $validated['skills'];
    $user->hu_work_experience_message = $validated['work_experience_message'] ?? null;

    // 3. Handle Work Experience File Upload
    if ($request->hasFile('work_experience_file')) {
        $file = $request->file('work_experience_file');
        $filename = $file->hashName();

        // Delete old file (new + legacy paths)
        if ($user->hu_work_experience_file) {
            Storage::disk('public')->delete($user->hu_work_experience_file);

            $legacyPath = public_path('storage/' . $user->hu_work_experience_file);
            if (file_exists($legacyPath)) {
                unlink($legacyPath);
            }
        }

        $storedPath = Storage::disk('public')->putFileAs('uploads/work_experience', $file, $filename);

        if (!$storedPath || !Storage::disk('public')->exists($storedPath)) {
            return back()
                ->withInput()
                ->withErrors(['work_experience_file' => 'Resume upload failed. Please try again.']);
        }

        $user->hu_work_experience_file = $storedPath;
    }

    // 4. Handle Profile Photo Upload
    if ($request->hasFile('profile_photo_path')) {
        
        if ($user->hu_profile_photo_path && file_exists(public_path($user->hu_profile_photo_path))) {
            unlink(public_path($user->hu_profile_photo_path));
        }

        $file = $request->file('profile_photo_path');
        $filename = $file->hashName();

        if (!file_exists(public_path('profile-photos'))) {
            mkdir(public_path('profile-photos'), 0755, true);
        }

        $file->move(public_path('profile-photos'), $filename);

        $user->hu_profile_photo_path = 'profile-photos/' . $filename;
    }

    $user->save();

    return redirect()
        ->route('students.index', $user->hu_id)
        ->with('success', 'Profile updated successfully!');
}

public function deleteWorkExperienceFile()
{
    $user = Auth::user();

    if ($user->hu_work_experience_file) {
        // Define the path exactly how you defined it in the update method
        $filePath = public_path('storage/' . $user->hu_work_experience_file);

        // 1. Delete the physical file if it exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // 2. Remove the path from the database
        $user->hu_work_experience_file = null;
        $user->save();

        return back()->with('success', 'File deleted successfully.');
    }

    return back()->with('error', 'No file to delete.');
}
    public function profile(User $user)
    {
        $user->load([
            'reviewsReceived' => function($query) {
                $query->latest(); 
            },
            'reviewsReceived.reviewer', 
            'reviewsReceived.service' 
        ]);
        

        // AMBIL SERVIS: Tukar 'is_available' kepada 'is_active'
        $services = $user->student_services()
                        ->withCount('reviews')
                        ->withAvg('reviews as reviews_avg_rating', 'hr_rating') 
                        ->where('hss_is_active', true) // <--- PASTIKAN GUNA NAMA COLUMN YANG BETUL (is_active)
                        ->where('hss_approval_status', 'approved')
                        ->latest()
                        ->get();

        return view('students.profile', [
            'user' => $user,
            'services' => $services,
            'reviews' => $user->reviewsReceived
        ]);
    }
}