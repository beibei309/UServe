<?php

namespace App\Http\Controllers\Admin;

use App\Mail\AccountBannedMail;
use App\Mail\AccountWarnedMail;
use App\Mail\SellerBlockedMail;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Notifications\AdminWarningNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            'status' => ['nullable', 'in:warning,banned,resolved,rejected'],
            'hrp_status' => ['nullable', 'in:warning,banned,resolved,rejected'],
            'action_taken' => ['nullable', 'string'],
        ]);

        $status = $data['status'] ?? $data['hrp_status'] ?? null;
        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'The status field is required.',
            ], 422);
        }

        $actionTaken = trim((string) ($data['action_taken'] ?? ''));
        $targetUser = User::query()->find($report->hrp_target_user_id);

        $report->update([
            'hrp_status' => $status,
            'hrp_action_taken' => $actionTaken !== '' ? $actionTaken : null,
            'hrp_resolved_at' => now(),
        ]);

        if ($targetUser && $status === 'warning') {
            $warningReason = $actionTaken !== '' ? $actionTaken : 'Warning issued after report review.';

            $targetUser->increment('hu_warning_count');

            Mail::to($targetUser->hu_email)->send(new AccountWarnedMail(
                $targetUser,
                $warningReason
            ));

            $targetUser->notify(new AdminWarningNotification((int) $targetUser->hu_warning_count, $warningReason));
        }

        if ($targetUser && $status === 'banned') {
            $reason = $actionTaken !== '' ? $actionTaken : 'Restricted via report resolution';

            if ($targetUser->hu_role === 'helper') {
                $targetUser->update([
                    'hu_is_blocked' => true,
                    'hu_is_suspended' => false,
                    'hu_is_blacklisted' => false,
                    'hu_blacklist_reason' => $reason,
                ]);

                Mail::to($targetUser->hu_email)->send(new SellerBlockedMail($targetUser, $reason));
            } elseif ($targetUser->hu_role === 'community') {
                $targetUser->update([
                    'hu_is_blacklisted' => true,
                    'hu_is_suspended' => false,
                    'hu_is_blocked' => false,
                    'hu_blacklist_reason' => $reason,
                ]);

                Mail::to($targetUser->hu_email)->send(new AccountBannedMail($targetUser, $reason));
            } else {
                $targetUser->update([
                    'hu_is_suspended' => true,
                    'hu_is_blocked' => false,
                    'hu_is_blacklisted' => false,
                    'hu_blacklist_reason' => $reason,
                ]);

                Mail::to($targetUser->hu_email)->send(new AccountBannedMail($targetUser, $reason));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Report resolved successfully.',
            'report' => $report,
        ]);
    }
}
