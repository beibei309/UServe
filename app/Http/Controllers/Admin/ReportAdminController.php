<?php

namespace App\Http\Controllers\Admin;

use App\Mail\AccountBannedMail;
use App\Mail\AccountWarnedMail;
use App\Mail\SellerBlockedMail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\SendsAdminNotifications;
use App\Models\Report;
use App\Models\User;
use App\Notifications\AdminWarningNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportAdminController extends Controller
{
    use SendsAdminNotifications;

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

            $this->sendAdminMailSafely($targetUser->hu_email, new AccountWarnedMail(
                $targetUser,
                $warningReason
            ), 'report_warning', [
                'report_id' => $report->hrp_id,
                'user_id' => $targetUser->hu_id,
            ]);

            $this->notifyAdminUserSafely($targetUser, new AdminWarningNotification((int) $targetUser->hu_warning_count, $warningReason), 'report_warning_notification', [
                'report_id' => $report->hrp_id,
                'user_id' => $targetUser->hu_id,
            ]);
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

                $this->sendAdminMailSafely($targetUser->hu_email, new SellerBlockedMail($targetUser, $reason), 'report_helper_block', [
                    'report_id' => $report->hrp_id,
                    'user_id' => $targetUser->hu_id,
                ]);
            } elseif ($targetUser->hu_role === 'community') {
                $targetUser->update([
                    'hu_is_blacklisted' => true,
                    'hu_is_suspended' => false,
                    'hu_is_blocked' => false,
                    'hu_blacklist_reason' => $reason,
                ]);

                $this->sendAdminMailSafely($targetUser->hu_email, new AccountBannedMail($targetUser, $reason), 'report_community_blacklist', [
                    'report_id' => $report->hrp_id,
                    'user_id' => $targetUser->hu_id,
                ]);
            } else {
                $targetUser->update([
                    'hu_is_suspended' => true,
                    'hu_is_blocked' => false,
                    'hu_is_blacklisted' => false,
                    'hu_blacklist_reason' => $reason,
                ]);

                $this->sendAdminMailSafely($targetUser->hu_email, new AccountBannedMail($targetUser, $reason), 'report_user_suspend', [
                    'report_id' => $report->hrp_id,
                    'user_id' => $targetUser->hu_id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Report resolved successfully.',
            'report' => $report,
        ]);
    }
}
