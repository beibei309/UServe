<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
   

    public function showPublic(User $user)
    {
        $reviews = Review::where('hr_reviewee_id', $user->hu_id)
            ->with(['reviewer', 'studentService'])
            ->latest()
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('hr_rating') : 0;
        $reportCount = (int) ($user->hu_reports_count ?? 0);
        $latestReportReason = ServiceRequest::where('hsr_requester_id', $user->hu_id)
            ->whereNotNull('hsr_dispute_reason')
            ->orderByDesc('updated_at')
            ->value('hsr_dispute_reason');

        return view('profile.show-public', compact('user', 'reviews', 'totalReviews', 'averageRating', 'reportCount', 'latestReportReason'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        $reviews = Review::where('hr_reviewee_id', $user->hu_id)
            ->with(['reviewer', 'studentService'])
            ->latest()
            ->get();

        $reviews = $reviews->map(function ($review) {
            $reviewerPhoto = $review->reviewer->hu_profile_photo_path ?? null;
            if (empty($reviewerPhoto)) {
                $review->ui_reviewer_photo_url = null;
                return $review;
            }
            if (Str::startsWith($reviewerPhoto, ['http://', 'https://'])) {
                $review->ui_reviewer_photo_url = $reviewerPhoto;
                return $review;
            }
            $review->ui_reviewer_photo_url = asset($reviewerPhoto);
            return $review;
        });

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('hr_rating') : 0;
        $ratingDisplay = number_format($averageRating, 1);
        $ratingStarsFilled = (int) round($averageRating);
        $profilePhotoUrl = $user->hu_profile_photo_path ? asset($user->hu_profile_photo_path) : null;
        $profileInitial = Str::substr($user->hu_name ?? 'U', 0, 1);

        $reviewsView = $reviews->map(function ($review) {
            $reviewerName = $review->reviewer->hu_name ?? 'Deleted User';
            $serviceTitle = $review->studentService?->hss_title;
            return [
                'reviewer_name' => $reviewerName,
                'reviewer_initial' => Str::substr($reviewerName, 0, 1),
                'reviewer_photo_url' => $review->ui_reviewer_photo_url,
                'created_human' => optional($review->hr_created_at)->diffForHumans() ?? 'Recently',
                'rating' => (int) ($review->hr_rating ?? 0),
                'comment' => $review->hr_comment,
                'has_comment' => filled($review->hr_comment),
                'has_service' => filled($serviceTitle),
                'service_title_short' => filled($serviceTitle) ? Str::limit($serviceTitle, 100) : null,
                'has_reply' => filled($review->hr_reply),
                'reply' => $review->hr_reply,
            ];
        })->values();

        $profileEditUi = [
            'initial_tab' => session('status') === 'password-updated' ? 'password' : 'profile',
            'profile_updated' => session('status') === 'profile-updated',
            'password_updated' => session('status') === 'password-updated',
            'rating_display' => $ratingDisplay,
            'rating_stars_filled' => $ratingStarsFilled,
            'profile_photo_url' => $profilePhotoUrl,
            'profile_initial' => $profileInitial,
            'can_edit_email' => in_array($user->hu_role, ['admin', 'superadmin'], true),
            'has_reviews' => $totalReviews > 0,
        ];

        return view('profile.edit', [
            'user' => $user,
            'reviewsView' => $reviewsView,
            'totalReviews' => $totalReviews,
            'profileEditUi' => $profileEditUi,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $user->fill($this->mapLegacyUserPayload($validated));

        // If email changed, reset verification
        if ($user->isDirty('hu_email')) {
            $user->hu_email_verified_at = null;
        }

        // If staff email changed, reset staff verification
        if ($user->isDirty('hu_staff_email') && $user->hu_staff_email) {
            $user->hu_staff_verified_at = null;
            // TODO: Send verification email to staff_email
            session()->flash('staff-verification-sent', 'A verification email has been sent to ' . $user->hu_staff_email);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {

    // Delete old image
    if ($user->hu_profile_photo_path && file_exists(public_path($user->hu_profile_photo_path))) {
        unlink(public_path($user->hu_profile_photo_path));
    }

    $file = $request->file('profile_photo');
    $filename = $file->hashName();

    // Make sure folder exists
    if (!file_exists(public_path('profile-photos'))) {
        mkdir(public_path('profile-photos'), 0755, true);
    }

    // Move file
    $file->move(public_path('profile-photos'), $filename);

    if (!file_exists(public_path('profile-photos/' . $filename))) {
        return Redirect::back()->withErrors(['profile_photo' => 'Profile photo upload failed. Please try again.']);
    }

    // Save path to DB
    $user->hu_profile_photo_path = 'profile-photos/' . $filename;
}

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $studentsCreateUi = [
            'profile_photo_preview_url' => $user->hu_profile_photo_path
                ? asset($user->hu_profile_photo_path)
                : 'https://ui-avatars.com/api/?name=' . urlencode($user->hu_name ?? 'User'),
            'default_bio' => $user->hu_bio,
            'default_faculty' => $user->hu_faculty,
            'default_course' => $user->hu_course,
            'default_skills' => $user->hu_skills,
            'default_work_experience_message' => $user->hu_work_experience_message ?? '',
        ];
        return view('students.create', ['studentsCreateUi' => $studentsCreateUi]);
    }

        private function mapLegacyUserPayload(array $validated): array
        {
            $map = [
                'name' => 'hu_name',
                'phone' => 'hu_phone',
                'staff_email' => 'hu_staff_email',
                'bio' => 'hu_bio',
                'faculty' => 'hu_faculty',
                'course' => 'hu_course',
                'address' => 'address',
                'skills' => 'skills',
                'latitude' => 'hu_latitude',
                'longitude' => 'hu_longitude',
                'work_experience_message' => 'hu_work_experience_message',
            ];

            // Only allow email mapping for admins/superadmins
            if (Auth::user()->hu_role === 'admin' || Auth::user()->hu_role === 'superadmin') {
                $map['email'] = 'hu_email';
            }

            $result = [];
            foreach ($validated as $key => $value) {
                $target = $map[$key] ?? $key;
                $result[$target] = $value;
            }

            return $result;
        }



}
