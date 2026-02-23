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
            ->where('status', 'open')
            ->with(['reporter:id,name', 'target:id,name,is_blacklisted'])
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
            'status' => $data['status'],
            'action_taken' => $data['action_taken'] ?? null,
            'resolved_at' => now(),
        ]);

        // If banned, set target user's blacklist
        if ($data['status'] === 'banned') {
            $user = User::find($report->target_user_id);
            if ($user) {
                $user->is_blacklisted = true;
                $user->blacklist_reason = $data['action_taken'] ?? 'Banned via report resolution';
                $user->save();
            }
        }

        return response()->json(['report' => $report]);
    }
}