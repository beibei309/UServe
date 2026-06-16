<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class AdminPageController extends Controller
{
    public function verifications()
    {
        // Only allow staff/admin roles (Check 'admin' guard)
        $user = Auth::guard('admin')->user();
        if (!$user) { // Role check simplified as middleware handles auth, but good to keep double check if needed
             abort(Response::HTTP_FORBIDDEN);
        }

        $pending = User::query()
            ->where('hu_role', 'community')
            ->where('hu_verification_status', 'pending')
            ->orderBy('created_at')
            ->paginate(20);

        return view('admin.verifications.index', [
            'pending' => $pending,
        ]);
    }

    public function exportVerifications()
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $pending = User::query()
            ->where('hu_role', 'community')
            ->where('hu_verification_status', 'pending')
            ->orderBy('created_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pending_verifications_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($pending) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User ID', 'Name', 'Email', 'Phone', 'Verification Status', 'Has Profile Photo', 'Has Selfie', 'Has Document', 'Submitted At']);

            foreach ($pending as $communityUser) {
                fputcsv($file, [
                    $communityUser->hu_id,
                    $communityUser->hu_name,
                    $communityUser->hu_email,
                    $communityUser->hu_phone,
                    $communityUser->hu_verification_status,
                    $communityUser->hu_profile_photo_path ? 'Yes' : 'No',
                    $communityUser->hu_selfie_media_path ? 'Yes' : 'No',
                    $communityUser->hu_verification_document_path ? 'Yes' : 'No',
                    optional($communityUser->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    public function reports()
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $reports = Report::query()
            ->where('hrp_status', 'open')
            ->with(['reporter', 'target'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reports.index', [
            'reports' => $reports,
        ]);
    }
}
