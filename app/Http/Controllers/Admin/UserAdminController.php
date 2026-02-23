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

        $user->is_blacklisted = true;
        $user->blacklist_reason = $data['reason'] ?? 'Banned by admin';
        $user->save();

        return response()->json(['user' => $user]);
    }

    public function unban(User $user): JsonResponse
    {
        $user->is_blacklisted = false;
        $user->blacklist_reason = null;
        $user->save();

        return response()->json(['user' => $user]);
    }

    public function suspend(User $user): JsonResponse
    {
        $user->is_suspended = true;
        $user->save();
        return response()->json(['user' => $user]);
    }

    public function unsuspend(User $user): JsonResponse
    {
        $user->is_suspended = false;
        $user->save();
        return response()->json(['user' => $user]);
    }
}