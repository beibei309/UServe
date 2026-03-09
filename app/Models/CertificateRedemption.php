<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CertificateRedemption extends Model
{
    use HasFactory;

    protected $table = 'h2u_certificate_redemptions';
    protected $primaryKey = 'hcr_id';

    protected $fillable = [
        'hcr_user_id',
        'hcr_points_used',
        'hcr_certificate_number',
        'hcr_status',
        'hcr_notes',
        'hcr_issued_at',
    ];

    protected $casts = [
        'hcr_points_used' => 'integer',
        'hcr_issued_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who redeemed the certificate
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hcr_user_id', 'hu_id');
    }

    /**
     * Scope to get only issued certificates
     */
    public function scopeIssued($query)
    {
        return $query->where('hcr_status', 'issued');
    }

    /**
     * Scope to get certificates for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('hcr_user_id', $userId);
    }

    /**
     * Generate a unique certificate number
     */
    public static function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (self::where('hcr_certificate_number', $number)->exists());

        return $number;
    }

    /**
     * Mark certificate as issued
     */
    public function markAsIssued(): bool
    {
        return $this->update([
            'hcr_status' => 'issued',
            'hcr_issued_at' => now(),
        ]);
    }

    /**
     * Check if certificate can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->hcr_status === 'pending';
    }
}
