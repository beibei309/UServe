<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
                'error' => 'Authentication required.',
            ], 401);
        }

        $data = $request->validate([
            'target_user_id' => ['required', 'exists:h2u_users,hu_id'],
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
        ]);

        // Prevent self-report
        if ((int) $data['target_user_id'] === (int) $user->hu_id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot report yourself.',
                'error' => 'You cannot report yourself.',
            ], 422);
        }

        $report = Report::create([
            'hrp_reporter_id' => $user->hu_id,
            'hrp_target_user_id' => $data['target_user_id'],
            'hrp_reason' => $data['reason'],
            'hrp_details' => $data['details'] ?? null,
            'hrp_status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report submitted successfully.',
            'report' => $report,
        ], 201);
    }
}