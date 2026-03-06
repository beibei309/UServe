<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function ban(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string']
        ]);

        $user->hu_is_blacklisted = true;
        $user->hu_is_suspended = false;
        $user->hu_blacklist_reason = $data['reason'] ?? 'Banned by admin';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User banned successfully.',
            'user' => $user,
        ]);
    }

    public function unban(User $user): JsonResponse
    {
        $user->hu_is_blacklisted = false;
        $user->hu_is_suspended = false;
        $user->hu_blacklist_reason = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User unbanned successfully.',
            'user' => $user,
        ]);
    }

    public function suspend(User $user): JsonResponse
    {
        $user->hu_is_suspended = true;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'User suspended successfully.',
            'user' => $user,
        ]);
    }

    public function unsuspend(User $user): JsonResponse
    {
        $user->hu_is_suspended = false;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'User unsuspended successfully.',
            'user' => $user,
        ]);
    }
}
