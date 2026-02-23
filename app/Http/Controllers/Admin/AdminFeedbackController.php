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
        $query = User::where('role', 'student')
            ->has('reviewsReceived') // Filter: User mesti ada review
            ->withAvg('reviewsReceived', 'rating') // Auto-calculate purata rating
            ->withCount('reviewsReceived') // Auto-calculate jumlah review
            ->orderByDesc('reviews_received_avg_rating'); // Sort rating tertinggi ke terendah

        // 2. Search Logic (Nama atau Email)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // 3. Paginate
        $usersWithReviews = $query->paginate(10);

        // 4. Transform Data (Supaya mudah dibaca di View)
        $usersWithReviews->getCollection()->transform(function ($user) {
            // Mapping field calculator Laravel ke nama yang kita guna di View
            $user->average_rating = $user->reviews_received_avg_rating ?? 0.0;
            $user->total_reviews = $user->reviews_received_count;

            // Pastikan field warning dan blocked wujud (default value)
            $user->warning_count = $user->warning_count ?? 0;
            $user->is_blocked = (bool) $user->is_blocked;

            return $user;
        });

        // 5. Hantar ke View
        return view('admin.feedback.index', [
            'usersWithReviews' => $usersWithReviews
        ]);
    }

    /**
     * Logic hantar warning (Notify)
     */
    public function sendWarning(User $user)
    {
        if ($user->is_blocked) {
            return back()->with('error', 'User is already blocked.');
        }

        // Tambah warning count
        $user->increment('warning_count');

        // Logic check count
        if ($user->warning_count >= 2) {
            $message = "User has received their second warning. Block option is now available.";
        } else {
            $message = "Warning sent to {$user->name}. Current warning count: {$user->warning_count}.";
        }

        return back()->with('success', $message);
    }

    /**
     * Logic Block User
     */
    public function blockUser(User $user)
    {
        if ($user->warning_count < 2) {
            return back()->with('error', 'User must receive 2 warnings before blocking.');
        }

        // Set status blocked
        $user->is_blocked = true;
        $user->save();

        // Redirect ikut role
        if ($user->role === 'student') {
             return redirect()->route('admin.students.index')->with('success', "Student {$user->name} has been blocked.");
        }
        
        return redirect()->route('admin.community.index')->with('success', "User {$user->name} has been blocked.");
    }
}