<?php

namespace App\Http\Controllers;

use App\Models\SellerPoint;
use App\Models\CertificateRedemption;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

class PointsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the points dashboard for the authenticated user
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        
        // Get user's points data
        $totalPoints = $user->getTotalSellerPoints();
        $recentPoints = $user->sellerPoints()
                             ->with('serviceRequest')
                             ->orderBy('created_at', 'desc')
                             ->take(10)
                             ->get();
        
        // Get certificate redemptions
        $certificates = $user->certificateRedemptions()
                             ->orderBy('created_at', 'desc')
                             ->get();
        
        // Check if user can redeem certificate
        $canRedeemCertificate = $user->canRedeemCertificate(1);
        
        // Check if user already has a certificate achievement
        $hasCertificateAchievement = $user->certificateRedemptions()
                                          ->whereIn('hcr_status', ['pending', 'issued'])
                                          ->exists();
        
        // Get points needed for next certificate
        $pointsNeededForCertificate = max(0, 1 - $totalPoints);

        return view('points.dashboard', compact(
            'totalPoints',
            'recentPoints',
            'certificates',
            'canRedeemCertificate',
            'hasCertificateAchievement',
            'pointsNeededForCertificate'
        ));
    }

    /**
     * Award points to a seller for a completed service
     * This method should be called when a service request is completed
     */
    public static function awardPointsForCompletedService(ServiceRequest $serviceRequest, int $points = 1): ?SellerPoint
    {
        // Ensure we have a provider (seller)
        if (!$serviceRequest->hsr_provider_id) {
            return null;
        }

        // Check if points have already been awarded for this service request
        $existingPoints = SellerPoint::where('hsp_service_request_id', $serviceRequest->hsr_id)
                                   ->where('hsp_user_id', $serviceRequest->hsr_provider_id)
                                   ->first();

        if ($existingPoints) {
            return $existingPoints; // Points already awarded
        }

        // Create new seller point record
        return SellerPoint::create([
            'hsp_user_id' => $serviceRequest->hsr_provider_id,
            'hsp_service_request_id' => $serviceRequest->hsr_id,
            'hsp_points_earned' => $points,
            'hsp_status' => 'earned',
            'hsp_description' => 'Points earned for completed service: ' . ($serviceRequest->studentService->hss_title ?? 'Service'),
        ]);
    }

    /**
     * Redeem certificate using points (AJAX)
     */
    public function redeemCertificateAjax(Request $request)
    {
        $user = Auth::user();
        $requiredPoints = 1;

        // Check if user has enough points
        if (!$user->canRedeemCertificate($requiredPoints)) {
            return response()->json([
                'success' => false,
                'message' => "You need at least {$requiredPoints} point to unlock the certificate achievement."
            ]);
        }

        // Check if user already has a certificate (achievement can only be earned once)
        $existingCertificate = $user->certificateRedemptions()
                                   ->whereIn('hcr_status', ['pending', 'issued'])
                                   ->first();

        if ($existingCertificate) {
            return response()->json([
                'success' => false,
                'message' => 'You have already earned your certificate achievement!'
            ]);
        }

        try {
            DB::beginTransaction();

            // Create certificate redemption record
            $certificateNumber = CertificateRedemption::generateCertificateNumber();
            
            $redemption = CertificateRedemption::create([
                'hcr_user_id' => $user->hu_id,
                'hcr_points_used' => 0, // No points deducted - this is an achievement
                'hcr_certificate_number' => $certificateNumber,
                'hcr_status' => 'issued',
                'hcr_notes' => 'Certificate achievement unlocked with ' . $user->getTotalSellerPoints() . ' seller points',
            ]);

            // Create achievement record instead of deducting points
            SellerPoint::create([
                'hsp_user_id' => $user->hu_id,
                'hsp_service_request_id' => null,
                'hsp_points_earned' => 0, // No points earned or lost
                'hsp_status' => 'earned',
                'hsp_description' => "Certificate achievement unlocked: {$certificateNumber}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Congratulations! You have unlocked your certificate achievement!',
                'certificate_number' => $certificateNumber,
                'certificate_url' => route('points.certificate', ['redemption' => $redemption->hcr_id])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your redemption. Please try again.'
            ]);
        }
    }

    /**
     * Redeem certificate using points
     */
    public function redeemCertificate(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $requiredPoints = 1;

        // Check if user has enough points
        if (!$user->canRedeemCertificate($requiredPoints)) {
            return back()->with('error', "You need at least {$requiredPoints} point to unlock the certificate achievement.");
        }

        // Check if user already has a certificate (achievement can only be earned once)
        $existingCertificate = $user->certificateRedemptions()
                                   ->whereIn('hcr_status', ['pending', 'issued'])
                                   ->first();

        if ($existingCertificate) {
            return back()->with('error', 'You have already earned your certificate achievement!');
        }

        try {
            DB::beginTransaction();

            // Create certificate redemption record
            $certificateNumber = CertificateRedemption::generateCertificateNumber();
            
            $redemption = CertificateRedemption::create([
                'hcr_user_id' => $user->hu_id,
                'hcr_points_used' => 0, // No points deducted - this is an achievement
                'hcr_certificate_number' => $certificateNumber,
                'hcr_status' => 'issued',
                'hcr_notes' => 'Certificate achievement unlocked with ' . $user->getTotalSellerPoints() . ' seller points',
            ]);

            // Create achievement record instead of deducting points
            SellerPoint::create([
                'hsp_user_id' => $user->hu_id,
                'hsp_service_request_id' => null,
                'hsp_points_earned' => 0, // No points earned or lost
                'hsp_status' => 'earned',
                'hsp_description' => "Certificate achievement unlocked: {$certificateNumber}",
            ]);

            DB::commit();

            return back()->with('success', "Congratulations! Certificate achievement unlocked: {$certificateNumber}");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while processing your redemption. Please try again.');
        }
    }

    /**
     * Cancel a pending certificate redemption
     */
    public function cancelRedemption(CertificateRedemption $redemption): RedirectResponse
    {
        $user = Auth::user();

        // Authorization check
        if ($redemption->hcr_user_id !== $user->hu_id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Status check
        if (!$redemption->canBeCancelled()) {
            return back()->with('error', 'This redemption cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Update redemption status
            $redemption->update([
                'hcr_status' => 'cancelled',
                'hcr_notes' => 'Cancelled by user on ' . now()->format('Y-m-d H:i:s'),
            ]);

            // Refund points by creating positive point entry
            SellerPoint::create([
                'hsp_user_id' => $user->hu_id,
                'hsp_service_request_id' => null,
                'hsp_points_earned' => $redemption->hcr_points_used,
                'hsp_status' => 'earned',
                'hsp_description' => "Points refunded from cancelled certificate redemption: {$redemption->hcr_certificate_number}",
            ]);

            DB::commit();

            return back()->with('success', 'Certificate redemption cancelled successfully. Points have been refunded.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while cancelling the redemption. Please try again.');
        }
    }

    /**
     * View detailed points history
     */
    public function history(): View
    {
        $user = Auth::user();
        
        $pointsHistory = $user->sellerPoints()
                              ->with('serviceRequest.studentService')
                              ->orderBy('created_at', 'desc')
                              ->paginate(20);

        return view('points.history', compact('pointsHistory'));
    }

    /**
     * View certificate details
     */
    public function certificate(CertificateRedemption $redemption): View
    {
        $user = Auth::user();

        // Authorization check
        if ($redemption->hcr_user_id !== $user->hu_id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        return view('points.certificate', compact('redemption'));
    }
}