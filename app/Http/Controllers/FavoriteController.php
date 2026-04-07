<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentService;
use App\Services\ServiceImageUrlResolver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FavoriteController extends Controller
{
    public function __construct(private readonly ServiceImageUrlResolver $serviceImageUrlResolver) {}

   public function toggleService(Request $request): JsonResponse
{
    try {
        $request->validate([
            'service_id' => 'required|exists:h2u_student_services,hss_id',
        ]);

        $user = Auth::user();
        $serviceId = $request->service_id;
        $service = StudentService::findOrFail($serviceId);
        $favoritedUserId = $service->hss_user_id;

        $exists = DB::table('h2u_favorites')
            ->where('hf_user_id', $user->hu_id)
            ->where('hf_favorited_user_id', $favoritedUserId)
            ->where('hf_service_id', $serviceId)
            ->exists();

        if ($exists) {
            DB::table('h2u_favorites')
                ->where('hf_user_id', $user->hu_id)
                ->where('hf_favorited_user_id', $favoritedUserId)
                ->where('hf_service_id', $serviceId)
                ->delete();

            return response()->json([
                'success' => true,
                'favorited' => false,
            ]);
        }

        DB::table('h2u_favorites')->insert([
            'hf_user_id' => $user->hu_id,
            'hf_favorited_user_id' => $favoritedUserId,
            'hf_service_id' => $serviceId,
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
        $favourites = StudentService::whereIn('hss_id', function ($q) {
                        $q->select('hf_service_id')
                            ->from('h2u_favorites')
                            ->where('hf_user_id', Auth::id())
                            ->whereNotNull('hf_service_id');
        })
        // Load the relations
        ->with(['user', 'category']) 
        
        // --- FIXED LOGIC START (Copied from Services Controller) ---
        // 1. count reviews where reviewee_id matches the service provider
        ->withCount(['reviews' => function ($query) {
            $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
        }])
        // 2. avg rating where reviewee_id matches the service provider
        ->withAvg(['reviews as reviews_avg_rating' => function ($query) {
            $query->whereColumn('h2u_reviews.hr_reviewee_id', 'h2u_student_services.hss_user_id');
        }], 'hr_rating')
        // --- FIXED LOGIC END ---

        ->where('hss_approval_status', 'approved')
        ->orderBy('created_at', 'desc')
        ->paginate(12);

    $favourites->getCollection()->transform(function (StudentService $service) {
        $service->ui_image_url = $this->serviceImageUrlResolver->resolveGeneralImageUrl(
            $service->hss_image_path,
            'https://via.placeholder.com/800x600?text=No+Image'
        );

        $service->ui_image_fallback = 'https://via.placeholder.com/800x600?text=No+Image';

        $sellerName = (string) ($service->user->hu_name ?? 'User');
        $sellerAvatarFallback = 'https://ui-avatars.com/api/?name=' . urlencode($sellerName);

        $sellerPhotoPath = (string) ($service->user->hu_profile_photo_path ?? '');
        if ($sellerPhotoPath !== '') {
            if (Str::startsWith($sellerPhotoPath, ['http://', 'https://'])) {
                $service->ui_seller_avatar_url = $sellerPhotoPath;
            } elseif (Str::startsWith($sellerPhotoPath, 'storage/')) {
                $service->ui_seller_avatar_url = asset($sellerPhotoPath);
            } else {
                $service->ui_seller_avatar_url = asset('storage/' . ltrim($sellerPhotoPath, '/'));
            }
        } else {
            $service->ui_seller_avatar_url = $sellerAvatarFallback;
        }

        $service->ui_seller_avatar_fallback = $sellerAvatarFallback;

        return $service;
    });

    return view('favorites.index', compact('favourites'));
}
}
