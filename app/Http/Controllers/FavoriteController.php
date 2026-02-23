<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
   public function toggleService(Request $request): JsonResponse
{
    try {
        $request->validate([
            'service_id' => 'required|exists:student_services,id',
        ]);

        $user = Auth::user();
        $serviceId = $request->service_id;

        $exists = \DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('service_id', $serviceId)
            ->exists();

        if ($exists) {
            \DB::table('favorites')
                ->where('user_id', $user->id)
                ->where('service_id', $serviceId)
                ->delete();

            return response()->json([
                'success' => true,
                'favorited' => false,
            ]);
        }

        \DB::table('favorites')->insert([
            'user_id' => $user->id,
            'service_id' => $serviceId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'favorited' => true,
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function index()
{
    $favourites = StudentService::whereIn('id', function ($q) {
            $q->select('service_id')
              ->from('favorites')
              ->where('user_id', Auth::id())
              ->whereNotNull('service_id');
        })
        // Load the relations
        ->with(['user', 'category']) 
        
        // --- FIXED LOGIC START (Copied from Services Controller) ---
        // 1. count reviews where reviewee_id matches the service provider
        ->withCount(['reviews' => function ($query) {
            $query->whereColumn('reviews.reviewee_id', 'student_services.user_id');
        }])
        // 2. avg rating where reviewee_id matches the service provider
        ->withAvg(['reviews' => function ($query) {
            $query->whereColumn('reviews.reviewee_id', 'student_services.user_id');
        }], 'rating')
        // --- FIXED LOGIC END ---

        ->where('approval_status', 'approved')
        ->orderBy('created_at', 'desc')
        ->paginate(12);

    return view('favorites.index', compact('favourites'));
}
}
