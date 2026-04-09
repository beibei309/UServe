<?php

namespace App\Http\Controllers;
use App\Models\User;
use Carbon\Carbon;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentsController extends Controller
{
   public function index(Request $request)
{
    $authUser = Auth::user();
    $user = $authUser instanceof User ? $authUser : null;
    if (!$user) {
        abort(403);
    }

    // Range handling (default: monthly)
    // Supported (UI): weekly, monthly, yearly
    // Semantics:
    // - weekly: current week (Mon-Sun)
    // - monthly: current month (1st - last day)
    // - yearly: current year (Jan - Dec)
    // Backward-compatible aliases: 30days -> monthly, 3months -> yearly (legacy)
    $range = (string) $request->get('range', 'monthly');
    $range = strtolower(trim($range));

    if ($range === '30days') {
        $range = 'monthly';
    } elseif ($range === '3months') {
        // No longer shown in UI; map to yearly to keep the dataset aggregation readable.
        $range = 'yearly';
    }

    $now = Carbon::now();

    if ($range === 'weekly') {
        $start = $now->copy()->startOfWeek(Carbon::MONDAY);
        $end = $now->copy()->endOfWeek(Carbon::SUNDAY);
        $interval = 'day';
    } elseif ($range === 'yearly') {
        // Always show Jan-Dec for the current year (12 months)
        $start = $now->copy()->startOfYear();
        $end = $now->copy()->endOfYear();
        $interval = 'month';
    } else {
        $range = 'monthly';
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();
        $interval = 'day';
    }

    // Build labels ($dates) depending on interval
    $labels = [];
    $period = [];

    if ($interval === 'day') {
        // Iterate calendar days inclusively to avoid off-by-one issues from time components
        $cursor = $start->copy()->startOfDay();
        $endDate = $end->copy()->startOfDay();

        while ($cursor->lte($endDate)) {
            if ($range === 'weekly') {
                $labels[] = $cursor->format('D'); // Mon, Tue, ...
            } else {
                $labels[] = $cursor->format('j'); // 1..31
            }
            $period[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }
    } else { // month
        if ($range === 'yearly') {
            $year = (int) $now->format('Y');
            for ($month = 1; $month <= 12; $month++) {
                $m = Carbon::create($year, $month, 1);
                $labels[] = $m->format('M'); // Jan, Feb, ...
                $period[] = $m->format('Y-m');
            }
        } else {
            $months = $start->diffInMonths($end);
            for ($i = 0; $i <= $months; $i++) {
                $m = $start->copy()->addMonths($i);
                $labels[] = $m->format('M Y'); // "Nov 2025"
                $period[] = $m->format('Y-m'); // use Y-m for grouping
            }
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
    $myServicesCount = $user->studentServices()->count();
    $quickActions = [
        [
            'title' => 'Create New Service',
            'icon' => 'fa-plus',
            'color' => 'text-indigo-600',
            'bg' => 'bg-indigo-50',
            'route' => route('services.create'),
        ],
        [
            'title' => 'Manage Services',
            'icon' => 'fa-list-check',
            'color' => 'text-blue-600',
            'bg' => 'bg-blue-50',
            'route' => route('services.manage'),
        ],
        [
            'title' => 'Edit Profile',
            'icon' => 'fa-user-pen',
            'color' => 'text-gray-600',
            'bg' => 'bg-gray-50',
            'route' => route('students.edit'),
        ],
    ];

    return view('students.index', compact(
        'user',
        'averageRating',
        'completedOrders',
        'myServicesCount',
        'quickActions',
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
            'work_experience_file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:1024',
        ]);

        $user = Auth::user(); // logged-in user

        // Profile photo upload (normalized with app convention: public/profile-photos)
        if ($request->hasFile('profile_photo')) {
            $oldPath = ltrim((string) $user->hu_profile_photo_path, '/');

            if ($oldPath !== '') {
                if (Str::startsWith($oldPath, 'storage/')) {
                    Storage::disk('public')->delete(Str::after($oldPath, 'storage/'));
                } else {
                    Storage::disk('public')->delete($oldPath);

                    if (file_exists(public_path($oldPath))) {
                        @unlink(public_path($oldPath));
                    }
                }
            }

            $storedPath = $request->file('profile_photo')->store('profile-photos', 'public');
            if (! $storedPath) {
                return back()->withInput()->withErrors(['profile_photo' => 'Profile photo upload failed. Please try again.']);
            }

            $user->hu_profile_photo_path = 'storage/' . $storedPath;
        }

        // Update basic fields
        if ($request->filled('faculty')) {
            $incomingFaculty = trim((string) $validated['faculty']);
            $user->hu_faculty = $this->normalizeFacultyName($incomingFaculty);
        }

        if ($request->filled('course')) {
            $user->hu_course = trim((string) $validated['course']);
        }

        if ($request->filled('bio')) {
            $user->hu_bio = trim((string) $validated['bio']);
        }
        $user->skills = $validated['skills'] ?? $user->skills;
        $user->hu_work_experience_message = $validated['work_experience_message'] ?? $user->hu_work_experience_message;

        // Handle work experience file
        if ($request->hasFile('work_experience_file')) {
            $file = $request->file('work_experience_file');
            try {
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
                $filename = 'workexp_' . $user->hu_id . '_' . now()->format('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;

                $disk = Storage::disk('public');
                $directory = 'uploads/work_experience';
                $storedPath = $directory . '/' . $filename;

                if (!$disk->exists($directory)) {
                    $disk->makeDirectory($directory);
                }

                $tmpPath = $file->getRealPath() ?: $file->getPathname();
                if (empty($tmpPath) || !is_file($tmpPath)) {
                    return back()
                        ->withInput()
                        ->withErrors(['work_experience_file' => 'Upload failed. Temporary upload file is missing. Please retry.']);
                }

                $contents = @file_get_contents($tmpPath);
                if ($contents === false) {
                    return back()
                        ->withInput()
                        ->withErrors(['work_experience_file' => 'Upload failed while reading the file. Please retry.']);
                }

                $stored = $disk->put($storedPath, $contents);
                if (!$stored || !$disk->exists($storedPath)) {
                    return back()
                        ->withInput()
                        ->withErrors(['work_experience_file' => 'Resume upload failed. Please try again.']);
                }

                $user->hu_work_experience_file = $storedPath;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Work experience upload failed (store)', [
                    'user_id' => $user->hu_id,
                    'message' => $e->getMessage(),
                ]);

                return back()
                    ->withInput()
                    ->withErrors(['work_experience_file' => 'Upload failed on this environment. Please retry or check server upload temp settings.']);
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
        $authUser = Auth::user();
        $user = $authUser instanceof User ? $authUser : null;
        if (!$user) {
            abort(403);
        }

        $facultyMap = $this->facultyCanonicalMap();
        $facultyOptions = array_values($facultyMap);
        $selectedFaculty = old('faculty', $this->normalizeFacultyName($user->hu_faculty ?? $user->faculty));

        return view('students.edit-profile', compact('user', 'facultyOptions', 'selectedFaculty'));

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
        'work_experience_file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:1024',
        'profile_photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
    ]);

    // 2. Update Basic Information
    $user->hu_name = $validated['name'];

    $incomingFaculty = trim((string) $request->input('faculty', ''));
    $user->hu_faculty = $incomingFaculty !== ''
        ? $this->normalizeFacultyName($incomingFaculty)
        : null;

    $user->hu_course = trim((string) $request->input('course', '')) ?: null;

    if ($request->has('bio')) {
        $user->hu_bio = $validated['bio'];
    }

    if ($request->has('skills')) {
        $user->skills = $validated['skills'];
    }

    if ($request->has('work_experience_message')) {
        $incomingExperience = trim((string) ($validated['work_experience_message'] ?? ''));
        $user->hu_work_experience_message = $incomingExperience !== '' ? $incomingExperience : $user->hu_work_experience_message;
    }

    // 3. Handle Work Experience File Upload
    if ($request->hasFile('work_experience_file')) {
        $file = $request->file('work_experience_file');

        // Delete old file (new + legacy paths)
        if ($user->hu_work_experience_file) {
            Storage::disk('public')->delete($user->hu_work_experience_file);

            $legacyPath = public_path('storage/' . $user->hu_work_experience_file);
            if (file_exists($legacyPath)) {
                unlink($legacyPath);
            }
        }

        try {
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
            $filename = 'workexp_' . $user->hu_id . '_' . now()->format('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;

            $disk = Storage::disk('public');
            $directory = 'uploads/work_experience';
            $storedPath = $directory . '/' . $filename;

            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $tmpPath = $file->getRealPath() ?: $file->getPathname();
            if (empty($tmpPath) || !is_file($tmpPath)) {
                return back()
                    ->withInput()
                    ->withErrors(['work_experience_file' => 'Upload failed. Temporary upload file is missing. Please retry.']);
            }

            $contents = @file_get_contents($tmpPath);
            if ($contents === false) {
                return back()
                    ->withInput()
                    ->withErrors(['work_experience_file' => 'Upload failed while reading the file. Please retry.']);
            }

            $stored = $disk->put($storedPath, $contents);
            if (!$stored || !$disk->exists($storedPath)) {
                return back()
                    ->withInput()
                    ->withErrors(['work_experience_file' => 'Resume upload failed. Please try again.']);
            }

            $user->hu_work_experience_file = $storedPath;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Work experience upload failed (update)', [
                'user_id' => $user->hu_id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['work_experience_file' => 'Upload failed on this environment. Please retry or check server upload temp settings.']);
        }
    }

    // 4. Handle Profile Photo Upload
    if ($request->hasFile('profile_photo_path')) {
        $oldPath = ltrim((string) $user->hu_profile_photo_path, '/');

        if ($oldPath !== '') {
            if (Str::startsWith($oldPath, 'storage/')) {
                Storage::disk('public')->delete(Str::after($oldPath, 'storage/'));
            } else {
                Storage::disk('public')->delete($oldPath);

                if (file_exists(public_path($oldPath))) {
                    @unlink(public_path($oldPath));
                }
            }
        }

        $storedPath = $request->file('profile_photo_path')->store('profile-photos', 'public');
        if (! $storedPath) {
            return back()->withInput()->withErrors(['profile_photo_path' => 'Profile photo upload failed. Please try again.']);
        }

        $user->hu_profile_photo_path = 'storage/' . $storedPath;
    }

    $user->save();

    return redirect()
        ->route('students.edit')
        ->with('success', 'Profile updated successfully!')
        ->with('status', 'profile-updated');
}

private function facultyCanonicalMap(): array
{
    return [
        'FKMT' => 'Fakulti Komputeran dan Meta-Teknologi',
        'FBK' => 'Fakulti Bahasa dan Komunikasi',
        'FPM' => 'Fakulti Pembangunan Manusia',
        'FSMT' => 'Fakulti Sains dan Matematik',
        'FPE' => 'Fakulti Pengurusan dan Ekonomi',
        'FSKIK' => 'Fakulti Seni, Komputeran dan Industri Kreatif',
        'FMUP' => 'Fakulti Muzik dan Seni Persembahan',
        'FSSKJ' => 'Fakulti Sains Sukan dan Kejurulatihan',
        'FTV' => 'Fakulti Teknikal dan Vokasional',
        'FSK' => 'Fakulti Sains Kemanusiaan',
    ];
}

private function normalizeFacultyName(?string $faculty): ?string
{
    $value = trim((string) $faculty);
    if ($value === '') {
        return null;
    }

    $aliases = [
        'Fakulti Komputeran & Meta-Teknologi' => 'Fakulti Komputeran dan Meta-Teknologi',
        'Fakulti Pengurusan & Ekonomi' => 'Fakulti Pengurusan dan Ekonomi',
        'Fakulti Sains & Matematik' => 'Fakulti Sains dan Matematik',
        'Fakulti Bahasa & Komunikasi' => 'Fakulti Bahasa dan Komunikasi',
        'Fakulti Muzik & Seni Persembahan' => 'Fakulti Muzik dan Seni Persembahan',
        'Fakulti Sains Sukan & Kejurulatihan' => 'Fakulti Sains Sukan dan Kejurulatihan',
        'Fakulti Teknikal & Vokasional' => 'Fakulti Teknikal dan Vokasional',
        'Fakulti Seni, Kelestarian & Industri Kreatif' => 'Fakulti Seni, Komputeran dan Industri Kreatif',
    ];

    if (isset($aliases[$value])) {
        return $aliases[$value];
    }

    $map = $this->facultyCanonicalMap();
    return $map[$value] ?? $value;
}

private function facultyShortLabel(?string $faculty): string
{
    $value = trim((string) $faculty);
    if ($value === '') {
        return 'Faculty of Computing';
    }

    $normalized = $this->normalizeFacultyName($value) ?? $value;
    $shortMap = [
        'Fakulti Komputeran dan Meta-Teknologi' => 'FKMT',
        'Fakulti Bahasa dan Komunikasi' => 'FBK',
        'Fakulti Pembangunan Manusia' => 'FPM',
        'Fakulti Sains dan Matematik' => 'FSM',
        'Fakulti Pengurusan dan Ekonomi' => 'FPE',
        'Fakulti Seni, Komputeran dan Industri Kreatif' => 'FSKIK',
        'Fakulti Seni, Kelestarian dan Industri Kreatif' => 'FSKIK',
        'Fakulti Muzik dan Seni Persembahan' => 'FMSP',
        'Fakulti Sains Sukan dan Kejurulatihan' => 'FSSKJ',
        'Fakulti Teknikal dan Vokasional' => 'FTV',
        'Fakulti Sains Kemanusiaan' => 'FSK',
    ];

    return $shortMap[$normalized] ?? $normalized;
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
        $viewer = Auth::user();
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
        $services->transform(function ($service) {
            $service->ui_image_url = $this->resolveProfileServiceImageUrl($service->hss_image_path);
            return $service;
        });

        $averageRating = (float) ($user->reviewsReceived()->avg('hr_rating') ?? 0);
        $reportsAgainstUser = ServiceRequest::where('hsr_requester_id', $user->hu_id)
            ->whereNotNull('hsr_dispute_reason')
            ->whereNotNull('hsr_reported_by')
            ->where('hsr_reported_by', '!=', $user->hu_id);
        $reportCount = max((int) ($user->hu_reports_count ?? 0), (clone $reportsAgainstUser)->count());
        $latestReportReason = (clone $reportsAgainstUser)
            ->orderByDesc('updated_at')
            ->value('hsr_dispute_reason');
        $canShowContactCta = $viewer && $viewer->hu_id !== $user->hu_id;
        $profileWhatsappUrl = $this->buildProfileWhatsappUrl($user);
        $profileHasPhone = !empty($profileWhatsappUrl);
        $reviews = $user->reviewsReceived->map(function ($review) {
            $review->ui_replied_ago = $review->hr_replied_at ? Carbon::parse($review->hr_replied_at)->diffForHumans() : null;
            $review->ui_reviewer_initial = Str::substr($review->reviewer->hu_name ?? 'A', 0, 1);
            $review->ui_created_human = optional($review->created_at)->diffForHumans() ?? 'Recently';
            return $review;
        });
        $services = $services->map(function ($service) {
            $service->ui_basic_price_display = number_format((float) ($service->hss_basic_price ?? 0), 0);
            $service->ui_description_preview = Str::limit(strip_tags($service->hss_description), 80);
            $service->ui_reviews_avg_rating_display = number_format((float) ($service->reviews_avg_rating ?? 0), 1);
            return $service;
        });
        $memberSinceSource = $user->created_at ?? $user->hu_created_at;
        $profileUi = [
            'initial' => Str::substr($user->hu_name ?? 'U', 0, 1),
            'average_rating_display' => number_format($averageRating, 1),
            'average_rating_rounded' => (int) round($averageRating),
            'latest_report_reason_short' => $latestReportReason ? Str::limit($latestReportReason, 140) : null,
            'member_since_display' => optional($memberSinceSource)->format('M Y') ?? 'N/A',
            'faculty_short_display' => $this->facultyShortLabel($user->hu_faculty ?? $user->faculty),
            'skills_list' => $this->parseSkillsListForDisplay($user->skills ?? $user->hu_skills),
        ];

        return view('students.profile', [
            'user' => $user,
            'services' => $services,
            'reviews' => $reviews,
            'averageRating' => round($averageRating, 1),
            'reportCount' => $reportCount,
            'latestReportReason' => $latestReportReason,
            'canShowContactCta' => $canShowContactCta,
            'profileWhatsappUrl' => $profileWhatsappUrl,
            'profileHasPhone' => $profileHasPhone,
            'profileUi' => $profileUi,
        ]);
    }

    private function resolveProfileServiceImageUrl(?string $path): string
    {
        if (!$path) {
            return '';
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }

        return asset($path);
    }

    private function buildProfileWhatsappUrl(User $user): ?string
    {
        $rawPhone = $user->hu_phone_number ?? ($user->hu_phone ?? '');
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = '60' . substr($cleanPhone, 1);
        }
        if (empty($cleanPhone)) {
            return null;
        }

        return "https://wa.me/{$cleanPhone}?text=Hi " . urlencode($user->hu_name) . ', I saw your profile on S2U.';
    }

    private function parseSkillsListForDisplay(?string $rawSkills): array
    {
        $value = trim((string) $rawSkills);
        if ($value === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value)), static fn ($skill) => $skill !== ''));
    }
}
