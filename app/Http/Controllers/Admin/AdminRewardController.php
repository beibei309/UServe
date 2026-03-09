<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminRewardController extends Controller
{
    /**
     * Display rewards dashboard with statistics
     */
    public function index()
    {
        // Overall statistics
        $totalRewards = Reward::count();
        $activeRewards = Reward::where('hr_is_active', true)->count();
        $totalRedemptions = RewardRedemption::count();
        $pendingRedemptions = RewardRedemption::where('hrr_status', 'pending')->count();
        
        // Recent redemptions
        $recentRedemptions = RewardRedemption::with(['user', 'reward'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Popular rewards
        $popularRewards = Reward::withCount('redemptions')
            ->orderBy('redemptions_count', 'desc')
            ->limit(5)
            ->get();
            
        // Rewards expiring soon (within 30 days)
        $expiringSoon = Reward::where('hr_is_active', true)
            ->where('hr_expires_at', '<=', Carbon::now()->addDays(30))
            ->where('hr_expires_at', '>', Carbon::now())
            ->orderBy('hr_expires_at')
            ->get();

        return view('admin.rewards.index', compact(
            'totalRewards', 
            'activeRewards', 
            'totalRedemptions', 
            'pendingRedemptions',
            'recentRedemptions',
            'popularRewards',
            'expiringSoon'
        ));
    }

    /**
     * Display list of all rewards
     */
    public function rewards(Request $request)
    {
        $query = Reward::query();
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('hr_type', $request->type);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('hr_is_active', true);
            } elseif ($request->status == 'inactive') {
                $query->where('hr_is_active', false);
            } elseif ($request->status == 'expired') {
                $query->where('hr_expires_at', '<', Carbon::now());
            }
        }
        
        // Search by title
        if ($request->filled('search')) {
            $query->where('hr_title', 'ILIKE', '%' . $request->search . '%');
        }
        
        $rewards = $query->withCount('redemptions')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.rewards.list', compact('rewards'));
    }

    /**
     * Show form to create new reward
     */
    public function create()
    {
        return view('admin.rewards.create');
    }

    /**
     * Store new reward
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hr_title' => 'required|string|max:255',
            'hr_description' => 'required|string',
            'hr_type' => 'required|in:discount,service_credit,voucher',
            'hr_points_cost' => 'required|integer|min:1',
            'hr_value' => 'required|numeric|min:0',
            'hr_code_prefix' => 'required|string|max:20',
            'hr_usage_limit' => 'nullable|integer|min:1',
            'hr_user_limit' => 'required|integer|min:1',
            'hr_expires_at' => 'nullable|date|after:now',
            'hr_terms' => 'nullable|array'
        ]);

        Reward::create($validated);

        return redirect()->route('admin.rewards.list')
            ->with('success', 'Reward created successfully!');
    }

    /**
     * Show form to edit reward
     */
    public function edit(Reward $reward)
    {
        return view('admin.rewards.edit', compact('reward'));
    }

    /**
     * Update reward
     */
    public function update(Request $request, Reward $reward)
    {
        $validated = $request->validate([
            'hr_title' => 'required|string|max:255',
            'hr_description' => 'required|string',
            'hr_type' => 'required|in:discount,service_credit,voucher',
            'hr_points_cost' => 'required|integer|min:1',
            'hr_value' => 'required|numeric|min:0',
            'hr_code_prefix' => 'required|string|max:20',
            'hr_usage_limit' => 'nullable|integer|min:1',
            'hr_user_limit' => 'required|integer|min:1',
            'hr_expires_at' => 'nullable|date',
            'hr_terms' => 'nullable|array'
        ]);

        $reward->update($validated);

        return redirect()->route('admin.rewards.list')
            ->with('success', 'Reward updated successfully!');
    }

    /**
     * Toggle reward active status
     */
    public function toggleStatus(Reward $reward)
    {
        $reward->update([
            'hr_is_active' => !$reward->hr_is_active
        ]);

        $status = $reward->hr_is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Reward {$status} successfully!");
    }

    /**
     * Delete reward (soft or hard delete based on redemptions)
     */
    public function destroy(Reward $reward)
    {
        // Check if reward has any redemptions
        $redemptionsCount = $reward->redemptions()->count();
        
        if ($redemptionsCount > 0) {
            // Soft delete - just deactivate
            $reward->update(['hr_is_active' => false]);
            return redirect()->back()
                ->with('success', 'Reward deactivated (has existing redemptions)');
        } else {
            // Hard delete - safe to remove
            $reward->delete();
            return redirect()->back()
                ->with('success', 'Reward deleted successfully!');
        }
    }

    /**
     * Display list of all redemptions
     */
    public function redemptions(Request $request)
    {
        $query = RewardRedemption::with(['user', 'reward']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('hrr_status', $request->status);
        }
        
        // Filter by reward type
        if ($request->filled('reward_type')) {
            $query->whereHas('reward', function($q) use ($request) {
                $q->where('hr_type', $request->reward_type);
            });
        }
        
        // Search by user name or redemption code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('hrr_redemption_code', 'ILIKE', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('hu_name', 'ILIKE', '%' . $search . '%');
                  });
            });
        }
        
        $redemptions = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.rewards.redemptions', compact('redemptions'));
    }

    /**
     * Update redemption status
     */
    public function updateRedemptionStatus(Request $request, RewardRedemption $redemption)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,used',
            'notes' => 'nullable|string|max:500'
        ]);

        $redemption->update([
            'hrr_status' => $validated['status'],
            'hrr_notes' => $validated['notes'] ?? $redemption->hrr_notes,
            'hrr_used_at' => $validated['status'] === 'used' ? Carbon::now() : null
        ]);

        return redirect()->back()
            ->with('success', 'Redemption status updated successfully!');
    }

    /**
     * Generate analytics data
     */
    public function analytics()
    {
        // Redemptions by month (last 12 months)
        $redemptionsByMonth = RewardRedemption::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Redemptions by reward type
        $redemptionsByType = RewardRedemption::selectRaw('h2u_rewards.hr_type, COUNT(*) as total')
            ->join('h2u_rewards', 'h2u_reward_redemptions.hrr_reward_id', '=', 'h2u_rewards.hr_id')
            ->groupBy('h2u_rewards.hr_type')
            ->get();
            
        // Top redeemers
        $topRedeemers = RewardRedemption::selectRaw('hrr_user_id, COUNT(*) as redemption_count, SUM(hrr_points_used) as total_points')
            ->with('user')
            ->groupBy('hrr_user_id')
            ->orderBy('redemption_count', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.rewards.analytics', compact(
            'redemptionsByMonth', 
            'redemptionsByType', 
            'topRedeemers'
        ));
    }

    /**
     * Export redemptions data
     */
    public function exportRedemptions()
    {
        $redemptions = RewardRedemption::with(['user', 'reward'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="redemptions_' . date('Y-m-d') . '.csv"'
        ];
        
        $callback = function() use ($redemptions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'User Name', 'User Email', 'Reward Title', 'Reward Type', 
                'Points Used', 'Redemption Code', 'Status', 'Redeemed At', 'Used At', 'Notes'
            ]);
            
            foreach ($redemptions as $redemption) {
                fputcsv($file, [
                    $redemption->hrr_id,
                    $redemption->user->hu_name ?? 'Unknown',
                    $redemption->user->email ?? 'Unknown',
                    $redemption->reward->hr_title ?? 'Deleted Reward',
                    $redemption->reward->hr_type ?? 'Unknown',
                    $redemption->hrr_points_used,
                    $redemption->hrr_redemption_code,
                    $redemption->hrr_status,
                    $redemption->hrr_redeemed_at ? $redemption->hrr_redeemed_at->format('Y-m-d H:i:s') : '',
                    $redemption->hrr_used_at ? $redemption->hrr_used_at->format('Y-m-d H:i:s') : '',
                    $redemption->hrr_notes ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}