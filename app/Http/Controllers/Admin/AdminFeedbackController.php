<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    /**
     * Paparkan senarai student yang ada review.
     */
    public function index(Request $request)
    {
        // 1. Query: Cari user (Student) yang ada sekurang-kurangnya 1 review
        // Pastikan kau dah tambah function reviewsReceived() dalam User.php!
        $query = User::where('hu_role', 'student')
            ->has('reviewsReceived') // Filter: User mesti ada review
            ->withAvg('reviewsReceived as reviews_received_avg_rating', 'hr_rating') // Auto-calculate purata rating
            ->withCount('reviewsReceived') // Auto-calculate jumlah review
            ->orderByDesc('reviews_received_avg_rating'); // Sort rating tertinggi ke terendah

        // 2. Search Logic (Nama atau Email)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('hu_name', 'like', '%' . $search . '%')
                                    ->orWhere('hu_email', 'like', '%' . $search . '%');
            });
        }

        // 3. Paginate
        $usersWithReviews = $query->paginate(10);

        // 4. Hantar ke View
        return view('admin.feedback.index', [
            'usersWithReviews' => $usersWithReviews
        ]);
    }

    /**
     * Logic hantar warning (Notify)
     */
    public function sendWarning(User $user)
    {
        if ($user->hu_is_blocked) {
            return back()->with('error', 'User is already blocked.');
        }

        // Tambah warning count
        $user->increment('hu_warning_count');

        // Logic check count
        if ($user->hu_warning_count >= 2) {
            $message = "User has received their second warning. Block option is now available.";
        } else {
            $message = "Warning sent to {$user->hu_name}. Current warning count: {$user->hu_warning_count}.";
        }

        return back()->with('success', $message);
    }

    /**
     * Logic Block User
     */
    public function blockUser(User $user)
    {
        if ($user->hu_warning_count < 2) {
            return back()->with('error', 'User must receive 2 warnings before blocking.');
        }

        // Synchronize moderation flags for legacy/new checks
           $user->hu_is_blocked = true;
           $user->hu_is_suspended = true;
        $user->save();

        // Redirect ikut role
           if ($user->hu_role === 'student') {
               return redirect()->route('admin.students.index')->with('success', "Student {$user->hu_name} has been blocked.");
        }
        
           return redirect()->route('admin.community.index')->with('success', "User {$user->hu_name} has been blocked.");
    }
}