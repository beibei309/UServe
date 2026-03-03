<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
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

        return view('profile.show-public', compact('user', 'reviews', 'totalReviews', 'averageRating'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        // 1. Fetch reviews received by this user
        $reviews = Review::where('hr_reviewee_id', $user->hu_id)
                            ->with(['reviewer', 'studentService']) // Load relations
                            ->latest()
                            ->get();

        // 2. Calculate Statistics
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('hr_rating') : 0;

        // 3. Pass data to view
        return view('profile.edit', [
            'user' => $user,
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating
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

    public function create()
{
    return view('students.create'); // Return the view where the student can fill their profile
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
