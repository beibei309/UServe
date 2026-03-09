<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Reward extends Model
{
    protected $table = 'h2u_rewards';
    protected $primaryKey = 'hr_id';

    protected $fillable = [
        'hr_title',
        'hr_description',
        'hr_type',
        'hr_points_cost',
        'hr_value',
        'hr_code_prefix',
        'hr_usage_limit',
        'hr_user_limit',
        'hr_is_active',
        'hr_expires_at',
        'hr_terms'
    ];

    protected $casts = [
        'hr_points_cost' => 'integer',
        'hr_value' => 'decimal:2',
        'hr_usage_limit' => 'integer',
        'hr_user_limit' => 'integer',
        'hr_is_active' => 'boolean',
        'hr_expires_at' => 'datetime',
        'hr_terms' => 'array'
    ];

    /**
     * Relationship with reward redemptions
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class, 'hrr_reward_id', 'hr_id');
    }

    /**
     * Scope for active rewards
     */
    public function scopeActive($query)
    {
        return $query->where('hr_is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('hr_expires_at')
                              ->orWhere('hr_expires_at', '>', now());
                    });
    }

    /**
     * Check if user can redeem this reward
     */
    public function canUserRedeem(User $user): bool
    {
        // Check if reward is active
        if (!$this->hr_is_active) {
            return false;
        }

        // Check if reward has expired
        if ($this->hr_expires_at && $this->hr_expires_at->isPast()) {
            return false;
        }

        // Check if user has enough points
        if ($user->getAccessibleTotalPoints() < $this->hr_points_cost) {
            return false;
        }

        // Check user redemption limit
        $userRedemptions = $this->redemptions()
                               ->where('hrr_user_id', $user->hu_id)
                               ->whereIn('hrr_status', ['active', 'used'])
                               ->count();

        if ($userRedemptions >= $this->hr_user_limit) {
            return false;
        }

        // Check total usage limit
        if ($this->hr_usage_limit) {
            $totalRedemptions = $this->redemptions()
                                   ->whereIn('hrr_status', ['active', 'used'])
                                   ->count();

            if ($totalRedemptions >= $this->hr_usage_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get remaining redemptions for this reward
     */
    public function getRemainingRedemptions(): ?int
    {
        if (!$this->hr_usage_limit) {
            return null; // Unlimited
        }

        $used = $this->redemptions()
                    ->whereIn('hrr_status', ['active', 'used'])
                    ->count();

        return max(0, $this->hr_usage_limit - $used);
    }
}
