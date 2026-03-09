<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerPoint extends Model
{
    use HasFactory;

    protected $table = 'h2u_buyer_points';
    protected $primaryKey = 'hbp_id';

    protected $fillable = [
        'hbp_user_id',
        'hbp_service_request_id',
        'hbp_points_earned',
        'hbp_status',
        'hbp_description',
    ];

    protected $casts = [
        'hbp_points_earned' => 'integer',
    ];

    /**
     * Get the user that owns the buyer points
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hbp_user_id', 'hu_id');
    }

    /**
     * Get the service request that generated these points
     */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'hbp_service_request_id', 'hsr_id');
    }

    /**
     * Scope to get only earned points
     */
    public function scopeEarned($query)
    {
        return $query->where('hbp_status', 'earned');
    }

    /**
     * Get total points for a specific user
     */
    public static function getTotalPointsForUser(int $userId): int
    {
        return self::where('hbp_user_id', $userId)
                   ->where('hbp_status', 'earned')
                   ->sum('hbp_points_earned');
    }
}
