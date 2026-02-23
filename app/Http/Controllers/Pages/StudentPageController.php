<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentService;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class StudentPageController extends Controller
{
    public function profile(User $user)
    {
        // Active services for this user (providers only)
        $services = StudentService::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        $servicesActiveCount = $services->count();

        // Load relationships and counts needed for profile (services and reviews)
        $user->load(['services', 'favorites'])
             ->loadCount(['services', 'reviewsReceived']);

        // Latest reviews for this user (as reviewee)
        $reviews = \App\Models\Review::with(['reviewer'])
            ->where('reviewee_id', $user->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $canChatOrReport = Auth::check() && Auth::id() !== $user->id;

        // Completed count depends on role
        if ($user->isStudent()) {
            // Completed requests where this user was the provider
            $completedCount = ServiceRequest::where('provider_id', $user->id)
                ->where('status', 'completed')
                ->count();
        } else {
            // Completed requests where this user was the requester (community/staff)
            $completedCount = ServiceRequest::where('requester_id', $user->id)
                ->where('status', 'completed')
                ->count();
        }

        // Favorites count (how many users this user has favorited)
        $favoritesCount = $user->favorites->count();

        return view('students.profile', [
            'user' => $user,
            'student' => $user,
            'services' => $services,
            'reviews' => $reviews,
            'canActions' => $canChatOrReport,
            'servicesActiveCount' => $servicesActiveCount,
            'completedCount' => $completedCount,
            'favoritesCount' => $favoritesCount,
        ]);
    }
}