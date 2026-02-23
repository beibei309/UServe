<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Mail\UserBlacklisted;
use App\Mail\UserUnblacklisted;
use Illuminate\Support\Facades\Mail;

class AdminCommunityController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');
    $ratingRange = $request->input('rating_range'); // 1. Get the range input

    $communityUsers = User::where('role', 'community')
        // Calculate average rating (creates 'reviews_received_avg_rating')
        ->withAvg('reviewsReceived', 'rating')
        ->withCount('reviewsReceived')
        ->with(['reviewsReceived' => function($query) {
            $query->latest()->limit(10)->with('reviewer'); 
        }])
        
        // Search Logic
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        })

        // Status Logic
        ->when($status === 'active', fn($q) => $q->where('is_blacklisted', 0)->where('is_suspended', 0))
            ->when($status === 'suspended', fn($q) => $q->where(function($sub) {
                $sub->where('is_blacklisted', 1)
                    ->orWhere('is_suspended', 1);
            }))

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

    // Stats
   $stats = [
            'total' => User::where('role', 'community')->count(),
            'approved' => User::where('role', 'community')->where('verification_status', 'approved')->count(),
            'pending' => User::where('role', 'community')->where('verification_status', 'pending')->count(),
            'blacklisted' => User::where('role', 'community')
                                 ->where(fn($q) => $q->where('is_blacklisted', 1)->orWhere('is_suspended', 1))
                                 ->count(),
        ];

    return view('admin.community.index', compact('communityUsers', 'stats'));
}


public function view($id)
{
    $user = User::where('role', 'community')->findOrFail($id);
    return view('admin.community.view', compact('user'));
}

public function edit($id)
{
    $user = User::where('role', 'community')->findOrFail($id);
    return view('admin.community.edit', compact('user'));
}

public function update(Request $request, $id)
{
    $user = User::where('role', 'community')->findOrFail($id);

    // 🔒 Prevent reverting approved users back to pending
    if (
        $user->verification_status === 'approved' &&
        $request->verification_status === 'pending'
    ) {
        return back()->with('error', 'Verified users cannot be reverted to pending.');
    }

    // Basic info
    $user->name  = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->bio   = $request->bio;

    // Now it is SAFE to update verification status
    $user->verification_status = $request->verification_status;

    // Upload new profile photo
    if ($request->hasFile('profile_photo')) {

        // Delete old photo if exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $file = $request->file('profile_photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads/profile', $filename, 'public');

        $user->profile_photo_path = $path;
    }

    // Blacklist / Unblacklist
   if ($request->remove_blacklist) {
            $user->is_blacklisted = 0;
            $user->is_suspended = 0; // Sync suspended
            $user->blacklist_reason = null;
        } 
        elseif ($request->blacklist_reason) {
            $user->is_blacklisted = 1;
            $user->is_suspended = 1; // Sync suspended
            $user->blacklist_reason = $request->blacklist_reason;
        }

    $user->save();

    return redirect()
        ->route('admin.community.view', $user->id)
        ->with('success', 'User updated successfully!');
}


public function blacklist(Request $request, $id)
{
    $request->validate([
        'blacklist_reason' => 'required|string|max:255'
    ]);

    $user = User::where('role', 'community')->findOrFail($id);

    $user->is_blacklisted = 1;
        $user->is_suspended = 1; 
        $user->blacklist_reason = $request->blacklist_reason;
        $user->save();

    Mail::to($user->email)->send(new UserBlacklisted($user, $request->blacklist_reason));

    return redirect()->route('admin.community.index')
                     ->with('success', 'User has been blacklisted.');
}

public function unblacklist($id)
{
    $user = User::where('role', 'community')->findOrFail($id);

    $user->is_blacklisted = 0;
        $user->is_suspended = 0; 
        $user->blacklist_reason = null;
        $user->save();

    Mail::to($user->email)->send(new UserUnblacklisted($user));

    return redirect()->route('admin.community.index')
                     ->with('success', 'Blacklist removed.');
}

public function delete($id)
{
    $user = User::where('role', 'community')->findOrFail($id);

    // Optional: delete profile photo
    if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
        Storage::disk('public')->delete($user->profile_photo_path);
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
    $query->where('role', 'community');

    // Apply filters
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%'.$request->search.'%')
              ->orWhere('email', 'like', '%'.$request->search.'%')
              ->orWhere('phone', 'like', '%'.$request->search.'%');
        });
    }

    if ($request->filled('status')) {
        if ($request->status == 'active') {
            $query->where('is_blacklisted', false);
        } elseif ($request->status == 'blacklisted') {
            $query->where('is_blacklisted', true);
        }
    }

    $users = $query->get();

    // Prepare CSV
    $csvData = $users->map(function ($user) {
            // [UPDATED] Check both for Status string
            $isBlacklisted = $user->is_blacklisted || $user->is_suspended;
            
            return [
                'Name' => $user->name,
                'Email' => $user->email,
                'Phone' => $user->phone,
                'Status' => $isBlacklisted ? 'Blacklisted/Suspended' : ($user->verification_status == 'approved' ? 'Verified' : 'Not Verified'),
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