<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RewardRedemption extends Model
{
    protected $table = 'h2u_reward_redemptions';
    protected $primaryKey = 'hrr_id';

    protected $fillable = [
        'hrr_user_id',
        'hrr_reward_id',
        'hrr_points_used',
        'hrr_redemption_code',
        'hrr_status',
        'hrr_redeemed_at',
        'hrr_expires_at',
        'hrr_used_at',
        'hrr_notes'
    ];

    protected $casts = [
        'hrr_user_id' => 'integer',
        'hrr_reward_id' => 'integer',
        'hrr_points_used' => 'integer',
        'hrr_redeemed_at' => 'datetime',
        'hrr_expires_at' => 'datetime',
        'hrr_used_at' => 'datetime'
    ];

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hrr_user_id', 'hu_id');
    }

    /**
     * Relationship with reward
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class, 'hrr_reward_id', 'hr_id');
    }

    /**
     * Generate unique redemption code
     */
    public static function generateRedemptionCode(string $prefix = 'REWARD'): string
    {
        do {
            $code = strtoupper($prefix . '-' . Str::random(8));
        } while (self::where('hrr_redemption_code', $code)->exists());

        return $code;
    }

    /**
     * Check if redemption is still valid
     */
    public function isValid(): bool
    {
        return $this->hrr_status === 'active' && 
               ($this->hrr_expires_at === null || $this->hrr_expires_at->isFuture());
    }

    /**
     * Mark redemption as used
     */
    public function markAsUsed(?string $notes = null): bool
    {
        if ($this->hrr_status !== 'active') {
            return false;
        }

        return $this->update([
            'hrr_status' => 'used',
            'hrr_used_at' => now(),
            'hrr_notes' => $notes
        ]);
    }

    /**
     * Check if redemption can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->hrr_status, ['pending', 'active']);
    }

    /**
     * Cancel the redemption and refund points
     */
    public function cancel(?string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update([
            'hrr_status' => 'cancelled',
            'hrr_notes' => $reason
        ]);

        return true;
    }

    /**
     * Scope for active redemptions
     */
    public function scopeActive($query)
    {
        return $query->where('hrr_status', 'active');
    }

    /**
     * Scope for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('hrr_user_id', $userId);
    }
}
