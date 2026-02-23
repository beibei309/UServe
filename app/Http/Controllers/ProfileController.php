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
        
        $reviews = Review::where('reviewee_id', $user->id)
            ->with(['reviewer', 'studentService'])
            ->latest()
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

        return view('profile.show-public', compact('user', 'reviews', 'totalReviews', 'averageRating'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        // 1. Fetch reviews received by this user
        $reviews = Review::where('reviewee_id', $user->id)
                            ->with(['reviewer', 'studentService']) // Load relations
                            ->latest()
                            ->get();

        // 2. Calculate Statistics
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

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
        
        // Fill the user with validated data
        $user->fill($validated);

        // If email changed, reset verification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // If staff email changed, reset staff verification
        if ($user->isDirty('staff_email') && $user->staff_email) {
            $user->staff_verified_at = null;
            // TODO: Send verification email to staff_email
            session()->flash('staff-verification-sent', 'A verification email has been sent to ' . $user->staff_email);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {

    // Delete old image
    if ($user->profile_photo_path && file_exists(public_path($user->profile_photo_path))) {
        unlink(public_path($user->profile_photo_path));
    }

    $file = $request->file('profile_photo');
    $filename = time() . '_' . $file->getClientOriginalName();

    // Make sure folder exists
    if (!file_exists(public_path('profile-photos'))) {
        mkdir(public_path('profile-photos'), 0755, true);
    }

    // Move file
    $file->move(public_path('profile-photos'), $filename);

    // Save path to DB
    $user->profile_photo_path = 'profile-photos/' . $filename;
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



}
