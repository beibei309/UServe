<?php

namespace App\Http\Controllers;

use App\Models\SellerPoint;
use App\Models\BuyerPoint;
use App\Models\CertificateRedemption;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PointsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the seller points dashboard for the authenticated user
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $this->ensureSellerAccess($user);

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
        $progressPercentage = ($totalPoints / 1) * 100;
        $displayProgressWidth = $totalPoints > 0 ? max(15, $progressPercentage) : 0;

        return view('points.dashboard', compact(
            'totalPoints',
            'recentPoints',
            'certificates',
            'canRedeemCertificate',
            'hasCertificateAchievement',
            'pointsNeededForCertificate',
            'displayProgressWidth'
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
     * Award buyer points for a completed service request
     * This method should be called when a service request is completed
     */
    public static function awardBuyerPointsForCompletedService(ServiceRequest $serviceRequest, int $points = 1): ?BuyerPoint
    {
        // Ensure we have a requester (buyer)
        if (!$serviceRequest->hsr_requester_id) {
            return null;
        }

        // Check if buyer points have already been awarded for this service request
        $existingPoints = BuyerPoint::where('hbp_service_request_id', $serviceRequest->hsr_id)
                                   ->where('hbp_user_id', $serviceRequest->hsr_requester_id)
                                   ->first();

        if ($existingPoints) {
            return $existingPoints; // Points already awarded
        }

        // Create new buyer point record
        return BuyerPoint::create([
            'hbp_user_id' => $serviceRequest->hsr_requester_id,
            'hbp_service_request_id' => $serviceRequest->hsr_id,
            'hbp_points_earned' => $points,
            'hbp_status' => 'earned',
            'hbp_description' => 'Points earned for completing service request: ' . ($serviceRequest->studentService->hss_title ?? 'Service'),
        ]);
    }

    /**
     * Redeem certificate using points (AJAX)
     */
    public function redeemCertificateAjax(Request $request)
    {
        $user = Auth::user();
        $this->ensureSellerAccess($user);
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
        $this->ensureSellerAccess($user);
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
        $this->ensureSellerAccess($user);

        // Authorization check
        if ((int) $redemption->hcr_user_id !== (int) $user->hu_id) {
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
        $this->ensureSellerAccess($user);

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
        $this->ensureSellerAccess($user);

        // Authorization check
        if ((int) $redemption->hcr_user_id !== (int) $user->hu_id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        return view('points.certificate', compact('redemption'));
    }

    /**
     * Redeem a reward using points
     */
    public function redeemReward(Request $request): RedirectResponse
    {
        $request->validate([
            'reward_id' => 'required|exists:h2u_rewards,hr_id'
        ]);

        $user = Auth::user();
        $reward = Reward::findOrFail($request->reward_id);

        // Check if user can redeem this reward
        if (!$reward->canUserRedeem($user)) {
            return back()->with('error', 'You cannot redeem this reward at this time.');
        }

        try {
            DB::beginTransaction();

            // Calculate expiry date (30 days from redemption)
            $expiresAt = now()->addDays(30);

            // Generate redemption code
            $redemptionCode = RewardRedemption::generateRedemptionCode($reward->hr_code_prefix);

            // Create redemption record
            $redemption = RewardRedemption::create([
                'hrr_user_id' => $user->hu_id,
                'hrr_reward_id' => $reward->hr_id,
                'hrr_points_used' => $reward->hr_points_cost,
                'hrr_redemption_code' => $redemptionCode,
                'hrr_status' => 'active',
                'hrr_redeemed_at' => now(),
                'hrr_expires_at' => $expiresAt,
            ]);

            // Deduct points by creating negative point entry in buyer points
            // (since rewards are for buyers/requesters)
            BuyerPoint::create([
                'hbp_user_id' => $user->hu_id,
                'hbp_service_request_id' => null,
                'hbp_points_earned' => -$reward->hr_points_cost,
                'hbp_status' => 'earned',
                'hbp_description' => "Points used for reward: {$reward->hr_title} (Code: {$redemptionCode})",
            ]);

            DB::commit();

            return back()->with('success', "Reward redeemed successfully! Your code is: {$redemptionCode}");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while redeeming the reward. Please try again.');
        }
    }

    /**
     * Get available rewards for dashboard
     */
    public function getAvailableRewards()
    {
        return Reward::active()
                    ->orderBy('hr_points_cost')
                    ->get();
    }

    /**
     * Get user's reward redemptions
     */
    public function getUserRewardRedemptions($user, int $limit = 5)
    {
        return $user->rewardRedemptions()
                   ->with('reward')
                   ->orderBy('hrr_redeemed_at', 'desc')
                   ->take($limit)
                   ->get();
    }

    /**
     * Display the buyer points dashboard for the authenticated user
     */
    public function buyerDashboard(): View
    {
        $user = Auth::user();

        // Get buyer points data
        $buyerPoints = $user->getTotalBuyerPoints();
        $totalEarnedPoints = $user->buyerPoints()
                                 ->where('hbp_status', 'earned')
                                 ->where('hbp_points_earned', '>', 0) // Only positive points (earned, not spent)
                                 ->sum('hbp_points_earned');
        
        $recentBuyerPoints = $user->buyerPoints()
                                 ->with('serviceRequest.studentService')
                                 ->where('hbp_points_earned', '>', 0) // Only earned points
                                 ->orderBy('created_at', 'desc')
                                 ->take(10)
                                 ->get();

        // Get available rewards
        $availableRewards = $this->getAvailableRewards();

        $userRedemptionCounts = RewardRedemption::where('hrr_user_id', $user->hu_id)
            ->whereIn('hrr_status', ['active', 'used'])
            ->selectRaw('hrr_reward_id, COUNT(*) as total')
            ->groupBy('hrr_reward_id')
            ->pluck('total', 'hrr_reward_id');

        $availableRewards = $availableRewards->map(function ($reward) use ($user, $userRedemptionCounts) {
            if ($reward->hr_type === 'discount') {
                $reward->ui_card_classes = 'border rounded-lg p-4 sm:p-6 hover:shadow-md transition-shadow bg-purple-50 border-purple-200';
                $reward->ui_badge_classes = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800';
                $reward->ui_price_classes = 'text-lg font-bold mb-3 text-purple-600';
                $reward->ui_button_classes = 'w-full px-3 py-2 rounded-lg text-sm font-medium text-white transition-colors bg-purple-600 hover:bg-purple-700';
            } elseif ($reward->hr_type === 'service_credit') {
                $reward->ui_card_classes = 'border rounded-lg p-4 sm:p-6 hover:shadow-md transition-shadow bg-blue-50 border-blue-200';
                $reward->ui_badge_classes = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                $reward->ui_price_classes = 'text-lg font-bold mb-3 text-blue-600';
                $reward->ui_button_classes = 'w-full px-3 py-2 rounded-lg text-sm font-medium text-white transition-colors bg-blue-600 hover:bg-blue-700';
            } else {
                $reward->ui_card_classes = 'border rounded-lg p-4 sm:p-6 hover:shadow-md transition-shadow bg-green-50 border-green-200';
                $reward->ui_badge_classes = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                $reward->ui_price_classes = 'text-lg font-bold mb-3 text-green-600';
                $reward->ui_button_classes = 'w-full px-3 py-2 rounded-lg text-sm font-medium text-white transition-colors bg-green-600 hover:bg-green-700';
            }

            $reward->ui_user_redemptions_count = (int) ($userRedemptionCounts[$reward->hr_id] ?? 0);
            $reward->ui_can_redeem = $reward->canUserRedeem($user);

            return $reward;
        });

        // Get user's reward redemptions
        $rewardRedemptions = $this->getUserRewardRedemptions($user, 5);

        $rewardRedemptions = $rewardRedemptions->map(function ($redemption) {
            if ($redemption->hrr_status === 'active') {
                $redemption->ui_status_classes = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
            } elseif ($redemption->hrr_status === 'used') {
                $redemption->ui_status_classes = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
            } elseif ($redemption->hrr_status === 'expired') {
                $redemption->ui_status_classes = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
            } else {
                $redemption->ui_status_classes = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
            }

            return $redemption;
        });

        return view('points.buyer-dashboard', compact(
            'buyerPoints',
            'totalEarnedPoints',
            'recentBuyerPoints',
            'availableRewards',
            'rewardRedemptions'
        ));
    }

    /**
     * Display buyer points history
     */
    public function buyerHistory(): View
    {
        $user = Auth::user();

        $buyerPointsHistory = $user->buyerPoints()
                                  ->with('serviceRequest.studentService')
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(20);

        return view('points.buyer-history', compact('buyerPointsHistory'));
    }

    /**
     * Display leaderboard page based on user role
     */
    public function leaderboard(): View
    {
        $user = Auth::user();

        // Get both leaderboards for all users (view-only for community)
        $sellerLeaderboard = $this->getSellerLeaderboard(10);
        $buyerLeaderboard = $this->getBuyerLeaderboard(10);
        $sellerRankedCount = $this->getSellerLeaderboardCount();
        $buyerRankedCount = $this->getBuyerLeaderboardCount();
        $userSellerRank = $user->canAccessSellerFeatures() ? $this->getUserSellerRank($user->hu_id) : null;
        $userBuyerRank = $this->getUserBuyerRank($user->hu_id);
        $canViewSellerStanding = $user->hu_role !== 'community';

        return view('points.leaderboard', compact(
            'sellerLeaderboard',
            'buyerLeaderboard',
            'sellerRankedCount',
            'buyerRankedCount',
            'userSellerRank',
            'userBuyerRank',
            'canViewSellerStanding'
        ));
    }

    /**
     * Get seller leaderboard data
     */
    public function getSellerLeaderboard(int $limit = 20)
    {
        return User::select(
                       'h2u_users.hu_id',
                       'h2u_users.hu_name', 
                       'h2u_users.hu_email',
                       'h2u_users.hu_role',
                       'h2u_users.hu_profile_photo_path',
                       'h2u_users.created_at',
                       'h2u_users.updated_at'
                   )
                   ->selectRaw('COALESCE(SUM(h2u_seller_points.hsp_points_earned), 0) as seller_points_sum_hsp_points_earned')
                   ->where('hu_role', 'helper')
                   ->join('h2u_seller_points', 'h2u_users.hu_id', '=', 'h2u_seller_points.hsp_user_id')
                   ->where('h2u_seller_points.hsp_status', 'earned')
                   ->groupBy(
                       'h2u_users.hu_id',
                       'h2u_users.hu_name', 
                       'h2u_users.hu_email',
                       'h2u_users.hu_role',
                       'h2u_users.hu_profile_photo_path',
                       'h2u_users.created_at',
                       'h2u_users.updated_at'
                   )
                   ->havingRaw('COALESCE(SUM(h2u_seller_points.hsp_points_earned), 0) > 0')
                   ->orderByRaw('COALESCE(SUM(h2u_seller_points.hsp_points_earned), 0) DESC')
                   ->take($limit)
                   ->get();
    }

    /**
     * Get buyer leaderboard data
     */
    public function getBuyerLeaderboard(int $limit = 20)
    {
        return User::select(
                       'h2u_users.hu_id',
                       'h2u_users.hu_name', 
                       'h2u_users.hu_email',
                       'h2u_users.hu_role',
                       'h2u_users.hu_profile_photo_path',
                       'h2u_users.created_at',
                       'h2u_users.updated_at'
                   )
                   ->selectRaw('COALESCE(SUM(h2u_buyer_points.hbp_points_earned), 0) as buyer_points_sum_hbp_points_earned')
                   ->join('h2u_buyer_points', 'h2u_users.hu_id', '=', 'h2u_buyer_points.hbp_user_id')
                   ->where('h2u_buyer_points.hbp_status', 'earned')
                   ->groupBy(
                       'h2u_users.hu_id',
                       'h2u_users.hu_name', 
                       'h2u_users.hu_email',
                       'h2u_users.hu_role',
                       'h2u_users.hu_profile_photo_path',
                       'h2u_users.created_at',
                       'h2u_users.updated_at'
                   )
                   ->havingRaw('COALESCE(SUM(h2u_buyer_points.hbp_points_earned), 0) > 0')
                   ->orderByRaw('COALESCE(SUM(h2u_buyer_points.hbp_points_earned), 0) DESC')
                   ->take($limit)
                   ->get();
    }

    /**
     * Get seller leaderboard only
     */
    public function sellerLeaderboard(): View
    {
        $user = Auth::user();

        $sellerLeaderboard = $this->getSellerLeaderboard(50);
        // Only calculate rank for students (who can actually be sellers)
        $userRank = $user->canAccessSellerFeatures() ? $this->getUserSellerRank($user->hu_id) : null;

        return view('points.seller-leaderboard', compact(
            'sellerLeaderboard',
            'userRank'
        ));
    }

    /**
     * Get buyer leaderboard only
     */
    public function buyerLeaderboard(): View
    {
        $buyerLeaderboard = $this->getBuyerLeaderboard(50);
        $userRank = $this->getUserBuyerRank(Auth::id());

        $availableRewards = Reward::active()
            ->orderBy('hr_points_cost')
            ->take(3)
            ->get()
            ->map(function ($reward) {
                if ($reward->hr_type === 'discount') {
                    $reward->ui_card_classes = 'bg-green-50 border border-green-200';
                    $reward->ui_icon = 'fas fa-percentage';
                    $reward->ui_icon_classes = 'text-green-600';
                } elseif ($reward->hr_type === 'service_credit') {
                    $reward->ui_card_classes = 'bg-blue-50 border border-blue-200';
                    $reward->ui_icon = 'fas fa-money-bill-wave';
                    $reward->ui_icon_classes = 'text-blue-600';
                } else {
                    $reward->ui_card_classes = 'bg-yellow-50 border border-yellow-200';
                    $reward->ui_icon = 'fas fa-star';
                    $reward->ui_icon_classes = 'text-yellow-600';
                }

                return $reward;
            });

        return view('points.buyer-leaderboard', compact('buyerLeaderboard', 'userRank', 'availableRewards'));
    }

    /**
     * Get user's rank in seller leaderboard
     */
    private function getUserSellerRank($userId): ?int
    {
        $allUsersWithPoints = User::select('h2u_users.hu_id')
                                  ->selectRaw('COALESCE(SUM(h2u_seller_points.hsp_points_earned), 0) as total_points')
                                  ->where('hu_role', 'helper')
                                  ->join('h2u_seller_points', 'h2u_users.hu_id', '=', 'h2u_seller_points.hsp_user_id')
                                  ->where('h2u_seller_points.hsp_status', 'earned')
                                  ->groupBy('h2u_users.hu_id')
                                  ->havingRaw('COALESCE(SUM(h2u_seller_points.hsp_points_earned), 0) > 0')
                                  ->orderByRaw('COALESCE(SUM(h2u_seller_points.hsp_points_earned), 0) DESC')
                                  ->pluck('hu_id')
                                  ->toArray();

        $rank = array_search($userId, $allUsersWithPoints);
        return $rank !== false ? $rank + 1 : null;
    }

    private function ensureSellerAccess($user): void
    {
        if (!$user || !$user->canAccessSellerFeatures()) {
            throw new HttpException(403, 'Seller access required.');
        }
    }

    /**
     * Get user's rank in buyer leaderboard
     */
    private function getUserBuyerRank($userId): ?int
    {
        $allUsersWithPoints = User::select('h2u_users.hu_id')
                                  ->selectRaw('COALESCE(SUM(h2u_buyer_points.hbp_points_earned), 0) as total_points')
                                  ->join('h2u_buyer_points', 'h2u_users.hu_id', '=', 'h2u_buyer_points.hbp_user_id')
                                  ->where('h2u_buyer_points.hbp_status', 'earned')
                                  ->groupBy('h2u_users.hu_id')
                                  ->havingRaw('COALESCE(SUM(h2u_buyer_points.hbp_points_earned), 0) > 0')
                                  ->orderByRaw('COALESCE(SUM(h2u_buyer_points.hbp_points_earned), 0) DESC')
                                  ->pluck('hu_id')
                                  ->toArray();

        $rank = array_search($userId, $allUsersWithPoints);
        return $rank !== false ? $rank + 1 : null;
    }

    private function getSellerLeaderboardCount(): int
    {
        return User::select('h2u_users.hu_id')
            ->where('hu_role', 'helper')
            ->join('h2u_seller_points', 'h2u_users.hu_id', '=', 'h2u_seller_points.hsp_user_id')
            ->where('h2u_seller_points.hsp_status', 'earned')
            ->groupBy('h2u_users.hu_id')
            ->havingRaw('COALESCE(SUM(h2u_seller_points.hsp_points_earned), 0) > 0')
            ->get()
            ->count();
    }

    private function getBuyerLeaderboardCount(): int
    {
        return User::select('h2u_users.hu_id')
            ->join('h2u_buyer_points', 'h2u_users.hu_id', '=', 'h2u_buyer_points.hbp_user_id')
            ->where('h2u_buyer_points.hbp_status', 'earned')
            ->groupBy('h2u_users.hu_id')
            ->havingRaw('COALESCE(SUM(h2u_buyer_points.hbp_points_earned), 0) > 0')
            ->get()
            ->count();
    }
}
