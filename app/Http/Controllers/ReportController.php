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
            return response()->json(['error' => 'Authentication required.'], 401);
        }

        $data = $request->validate([
            'target_user_id' => ['required', 'exists:users,id'],
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
        ]);

        // Prevent self-report
        if ((int) $data['target_user_id'] === (int) $user->id) {
            return response()->json(['error' => 'You cannot report yourself.'], 422);
        }

        $report = Report::create([
            'reporter_id' => $user->id,
            'target_user_id' => $data['target_user_id'],
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'open',
        ]);

        return response()->json(['report' => $report], 201);
    }
}