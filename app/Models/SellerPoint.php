<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerPoint extends Model
{
    use HasFactory;

    protected $table = 'h2u_seller_points';
    protected $primaryKey = 'hsp_id';

    protected $fillable = [
        'hsp_user_id',
        'hsp_service_request_id',
        'hsp_points_earned',
        'hsp_status',
        'hsp_description',
    ];

    protected $casts = [
        'hsp_points_earned' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user (seller) who earned the points
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hsp_user_id', 'hu_id');
    }

    /**
     * Get the service request that generated these points
     */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'hsp_service_request_id', 'hsr_id');
    }

    /**
     * Scope to get only earned points
     */
    public function scopeEarned($query)
    {
        return $query->where('hsp_status', 'earned');
    }

    /**
     * Scope to get points for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('hsp_user_id', $userId);
    }

    /**
     * Get total earned points for a user
     */
    public static function getTotalPointsForUser($userId): int
    {
        return self::where('hsp_user_id', $userId)
                   ->where('hsp_status', 'earned')
                   ->sum('hsp_points_earned');
    }

    /**
     * Check if user has enough points for redemption
     */
    public static function hasEnoughPointsForRedemption($userId, $requiredPoints = 3): bool
    {
        return self::getTotalPointsForUser($userId) >= $requiredPoints;
    }
}