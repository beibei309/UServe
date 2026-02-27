<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportAdminController extends Controller
{
    public function index()
    {
        $reports = Report::query()
            ->where('hrp_status', 'open')
            ->with(['reporter:hu_id,hu_name', 'target:hu_id,hu_name,hu_is_blacklisted'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    public function resolve(Request $request, Report $report): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:warning,banned,resolved,rejected'],
            'action_taken' => ['nullable', 'string'],
        ]);

        $report->update([
            'hrp_status' => $data['status'],
            'hrp_action_taken' => $data['action_taken'] ?? null,
            'hrp_resolved_at' => now(),
        ]);

        // If banned, set target user's blacklist
        if ($data['status'] === 'banned') {
            User::where('hu_id', $report->hrp_target_user_id)->update([
                'hu_is_blacklisted' => true,
                'hu_blacklist_reason' => $data['action_taken'] ?? 'Banned via report resolution',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Report resolved successfully.',
            'report' => $report,
        ]);
    }
}