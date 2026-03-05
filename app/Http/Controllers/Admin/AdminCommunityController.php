<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Mail\UserBlacklisted;
use App\Mail\UserUnblacklisted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AdminCommunityController extends Controller
{
private function resolveProfileImageUrl(?string $path): string
{
    if (!$path) {
        return asset('uploads/profile/default.png');
    }
    if (Str::startsWith($path, ['http://', 'https://'])) {
        return $path;
    }
    if (file_exists(public_path('storage/' . $path))) {
        return asset('storage/' . $path);
    }
    return asset($path);
}

   public function index(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');
    $ratingRange = $request->input('rating_range'); // 1. Get the range input

    $communityUsers = User::where('hu_role', 'community')
        // Calculate average rating (creates 'reviews_received_avg_rating')
        ->withAvg('reviewsReceived as reviews_received_avg_rating', 'hr_rating')
        ->withCount('reviewsReceived')
        ->with(['reviewsReceived' => function($query) {
            $query->latest()->limit(10)->with('reviewer'); 
        }])
        
        // Search Logic
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('hu_name', 'like', "%{$search}%")
                  ->orWhere('hu_email', 'like', "%{$search}%")
                  ->orWhere('hu_phone', 'like', "%{$search}%");
            });
        })

        // Status Logic
        ->when($status === 'active', fn($q) => $q->where('hu_is_blacklisted', 0)->where('hu_is_suspended', 0))
        ->when($status === 'suspended', fn($q) => $q->where('hu_is_suspended', 1)->where('hu_is_blacklisted', 0))
        ->when($status === 'blacklisted', fn($q) => $q->where('hu_is_blacklisted', 1))

        // 2. ADDED: Rating Range Logic
        ->when($ratingRange, function ($query, $range) {
            switch ($range) {
                case '4-5':
                    $query->having('reviews_received_avg_rating', '>=', 4.0)
                          ->having('reviews_received_avg_rating', '<=', 5.0);
                    break;
                case '3-4':
                    $query->having('reviews_received_avg_rating', '>=', 3.0)
                          ->having('reviews_received_avg_rating', '<', 4.0);
                    break;
                case '2-3':
                    $query->having('reviews_received_avg_rating', '>=', 2.0)
                          ->having('reviews_received_avg_rating', '<', 3.0);
                    break;
                case '1-2':
                    $query->having('reviews_received_avg_rating', '>=', 1.0)
                          ->having('reviews_received_avg_rating', '<', 2.0);
                    break;
                case '0-1':
                    // Includes users with 0 ratings or very low ratings
                    $query->havingRaw('(reviews_received_avg_rating >= 0 AND reviews_received_avg_rating < 1) OR reviews_received_avg_rating IS NULL');
                    break;
            }
        })

        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Keep params in URL
    $communityUsers->appends($request->only('search', 'status', 'rating_range'));
    $communityUsers->getCollection()->transform(function (User $user) {
        $user->profile_image_url = $this->resolveProfileImageUrl($user->hu_profile_photo_path);
        $user->status_label = $user->hu_is_blacklisted || $user->hu_is_suspended
            ? 'Suspended'
            : ($user->hu_verification_status === 'approved' ? 'Verified' : 'Not Verified');
        $user->status_badge_class = $user->hu_is_blacklisted || $user->hu_is_suspended
            ? 'bg-red-100 text-red-800 border-red-200'
            : ($user->hu_verification_status === 'approved'
                ? 'bg-green-100 text-green-800 border-green-200'
                : 'bg-yellow-100 text-yellow-800 border-yellow-200');
        $user->reviewsReceived->transform(function ($review) {
            $review->reviewer_image_url = $this->resolveProfileImageUrl(optional($review->reviewer)->hu_profile_photo_path);
            $review->replied_at_human = $review->hr_replied_at ? Carbon::parse($review->hr_replied_at)->diffForHumans() : null;
            return $review;
        });
        return $user;
    });

    // Stats
   $stats = [
            'total' => User::where('hu_role', 'community')->count(),
            'approved' => User::where('hu_role', 'community')->where('hu_verification_status', 'approved')->count(),
            'pending' => User::where('hu_role', 'community')->where('hu_verification_status', 'pending')->count(),
            'blacklisted' => User::where('hu_role', 'community')->where('hu_is_blacklisted', 1)->count(),
            'suspended' => User::where('hu_role', 'community')->where('hu_is_suspended', 1)->where('hu_is_blacklisted', 0)->count(),
        ];

    return view('admin.community.index', compact('communityUsers', 'stats'));
}


public function view($id)
{
    $user = User::where('hu_role', 'community')->findOrFail($id);
    $user->profile_image_url = $this->resolveProfileImageUrl($user->hu_profile_photo_path);
    $createdAt = $user->hu_created_at ?? $user->created_at;
    $updatedAt = $user->hu_updated_at ?? $user->updated_at;
    $user->captured_at_display = $createdAt ? Carbon::parse($createdAt)->format('d M Y, H:i A') : '-';
    $user->registered_at_display = $createdAt ? Carbon::parse($createdAt)->format('d M Y, h:i A') : '-';
    $user->updated_at_display = $updatedAt ? Carbon::parse($updatedAt)->format('d M Y, h:i A') : '-';
    return view('admin.community.view', compact('user'));
}

public function edit($id)
{
    $user = User::where('hu_role', 'community')->findOrFail($id);
    $user->profile_image_url = $this->resolveProfileImageUrl($user->hu_profile_photo_path);
    return view('admin.community.edit', compact('user'));
}

public function update(Request $request, $id)
{
    $user = User::where('hu_role', 'community')->findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'bio' => 'nullable|string|max:1000',
        'verification_status' => 'required|in:pending,approved,rejected',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        'blacklist_reason' => 'nullable|string|max:255',
        'remove_blacklist' => 'nullable|in:1',
    ]);

    // 🔒 Prevent reverting approved users back to pending
    if (
        $user->hu_verification_status === 'approved' &&
        $validated['verification_status'] === 'pending'
    ) {
        return back()->with('error', 'Verified users cannot be reverted to pending.');
    }

    // Basic info
    $user->hu_name  = $validated['name'];
    $user->hu_email = $validated['email'];

    if ($request->filled('phone')) {
        $user->hu_phone = trim((string) $validated['phone']);
    }

    if ($request->filled('bio')) {
        $user->hu_bio = trim((string) $validated['bio']);
    }

    // Now it is SAFE to update verification status
    $user->hu_verification_status = $validated['verification_status'];

    // Upload new profile photo
    if ($request->hasFile('profile_photo')) {

        // Delete old photo if exists (support both legacy and current paths)
        if ($user->hu_profile_photo_path) {
            if (Storage::disk('public')->exists($user->hu_profile_photo_path)) {
                Storage::disk('public')->delete($user->hu_profile_photo_path);
            }

            if (file_exists(public_path($user->hu_profile_photo_path))) {
                unlink(public_path($user->hu_profile_photo_path));
            }
        }

        $file = $request->file('profile_photo');
        $filename = $file->hashName();

        if (!file_exists(public_path('profile-photos'))) {
            mkdir(public_path('profile-photos'), 0755, true);
        }

        $file->move(public_path('profile-photos'), $filename);

        if (!file_exists(public_path('profile-photos/' . $filename))) {
            return back()->withErrors(['profile_photo' => 'Profile photo upload failed. Please try again.']);
        }

        $user->hu_profile_photo_path = 'profile-photos/' . $filename;
    }

    // Blacklist / Unblacklist
   if ($request->remove_blacklist) {
            $user->hu_is_blacklisted = 0;
            $user->hu_blacklist_reason = null;
        } 
        elseif ($request->filled('blacklist_reason')) {
            $user->hu_is_blacklisted = 1;
            $user->hu_is_blocked = 0;
            $user->hu_blacklist_reason = trim((string) $validated['blacklist_reason']);
        }

    $user->save();

    return redirect()
        ->route('admin.community.view', $user->hu_id)
        ->with('success', 'User updated successfully!');
}


public function blacklist(Request $request, $id)
{
    $request->validate([
        'blacklist_reason' => 'required|string|max:255'
    ]);

    $user = User::where('hu_role', 'community')->findOrFail($id);

    $user->hu_is_blacklisted = 1;
        $user->hu_is_blocked = 0;
        $user->hu_blacklist_reason = $request->blacklist_reason;
        $user->save();

    Mail::to($user->hu_email)->send(new UserBlacklisted($user, $request->blacklist_reason));

    return redirect()->route('admin.community.index')
                     ->with('success', 'User has been blacklisted.');
}

public function unblacklist($id)
{
    $user = User::where('hu_role', 'community')->findOrFail($id);

    $user->hu_is_blacklisted = 0;
        $user->hu_is_blocked = 0;
        $user->hu_blacklist_reason = null;
        $user->save();

    Mail::to($user->hu_email)->send(new UserUnblacklisted($user));

    return redirect()->route('admin.community.index')
                     ->with('success', 'Blacklist removed.');
}

public function delete($id)
{
    $user = User::where('hu_role', 'community')->findOrFail($id);

    // Optional: delete profile photo (support both legacy and current paths)
    if ($user->hu_profile_photo_path) {
        if (Storage::disk('public')->exists($user->hu_profile_photo_path)) {
            Storage::disk('public')->delete($user->hu_profile_photo_path);
        }

        if (file_exists(public_path($user->hu_profile_photo_path))) {
            unlink(public_path($user->hu_profile_photo_path));
        }
    }

    $user->delete();

    return redirect()
        ->route('admin.community.index')
        ->with('success', 'Community user deleted successfully.');
}

public function export(Request $request)
{
    $query = User::query();

    // Only community users
    $query->where('hu_role', 'community');

    // Apply filters
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
                        $q->where('hu_name', 'like', '%'.$request->search.'%')
                            ->orWhere('hu_email', 'like', '%'.$request->search.'%')
                            ->orWhere('hu_phone', 'like', '%'.$request->search.'%');
        });
    }

    if ($request->filled('status')) {
        if ($request->status == 'active') {
            $query->where('hu_is_blacklisted', false)->where('hu_is_suspended', false);
        } elseif ($request->status == 'blacklisted') {
            $query->where('hu_is_blacklisted', true);
        } elseif ($request->status == 'suspended') {
            $query->where('hu_is_suspended', true)->where('hu_is_blacklisted', false);
        }
    }

    $users = $query->get();

    // Prepare CSV
    $csvData = $users->map(function ($user) {
            // [UPDATED] Check both for Status string
            return [
                'Name' => $user->hu_name,
                'Email' => $user->hu_email,
                'Phone' => $user->hu_phone,
                'Status' => ucfirst($user->moderationStatusKey()),
            ];
        });

    return response()->streamDownload(function() use ($csvData) {
        $output = fopen('php://output', 'w');
        fputcsv($output, array_keys($csvData->first()));
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    }, 'community_users.csv');
}


}
