<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Review;
use App\Models\BuyerPoint;
use App\Models\SellerPoint;
use App\Models\CertificateRedemption;
use App\Models\RewardRedemption;
use App\Models\StudentService;
use App\Models\ServiceRequest;
use App\Models\Favorite;
use App\Models\StudentStatus;
use App\Models\DatabaseNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'h2u_users';
    protected $primaryKey = 'hu_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hu_name',
        'hu_email',
        'hu_password',
        'hu_role',
        'hu_phone',
        'hu_student_id',
        'hu_profile_photo_path',
        'hu_selfie_media_path',
        'hu_public_verified_at',
        'hu_verification_status',
        'hu_staff_email',
        'hu_reports_count',
        'hu_staff_verified_at',
        'hu_is_available',
        'hu_is_suspended',
        'hu_is_blacklisted',
        'hu_is_blocked',
        'hu_warning_count',
        'hu_blacklist_reason',
        'hu_bio',
        'hu_faculty',
        'hu_course',
        'address',
        'hu_latitude',
        'hu_longitude',
        'hu_location_verified_at',
        'skills',
        'hu_work_experience_message',
        'hu_work_experience_file',
        'hu_verification_document_path',
        'hu_verification_note',
        'hu_helper_verified_at',
        'helper_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'hu_password',
        'remember_token',
    ];

    // expose computed badge & rating
    protected $appends = ['trust_badge', 'average_rating'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hu_email_verified_at' => 'datetime',
            'hu_public_verified_at' => 'datetime',
            'hu_staff_verified_at' => 'datetime',
            'hu_helper_verified_at' => 'datetime',
            'hu_location_verified_at' => 'datetime',
            'hu_is_available' => 'boolean',
            'hu_is_suspended' => 'boolean',
            'hu_is_blacklisted' => 'boolean',
            'hu_is_blocked' => 'boolean',
            'hu_password' => 'hashed',
        ];
    }

    // Relationships
    public function services()
    {
        return $this->hasMany(StudentService::class, 'hss_user_id', 'hu_id');
    }

    /** Backward compatibility aliases */
    public function studentServices() { return $this->services(); }
    public function student_services() { return $this->services(); }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'hr_reviewee_id', 'hu_id');
    }

    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'hr_reviewer_id', 'hu_id');
    }



public function favoriteServices()
{
    return $this->belongsToMany(
        \App\Models\StudentService::class,
        'h2u_favorites',
        'hf_user_id',
        'hf_service_id',
        'hu_id',
        'hss_id'
    )->withTimestamps();
}

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'hf_user_id', 'hu_id');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'hn_notifiable', 'hn_notifiable_type', 'hn_notifiable_id')
            ->orderBy('created_at', 'desc');
    }



    // Helpers
    public function isStudent(): bool
    {
        return $this->hu_role === 'student';
    }

    public function isCommunity(): bool
    {
        return $this->hu_role === 'community' || $this->hu_role === 'staff';
    }

    public function isStaff(): bool
    {
        // Only community accounts with staff verification qualify as staff
        return $this->hu_role === 'community' && !is_null($this->hu_staff_verified_at) && !is_null($this->hu_staff_email);
    }

    public function isAdmin(): bool
    {
        return $this->hu_role === 'admin';
    }

    public function isVerifiedPublic(): bool
    {
        return !is_null($this->hu_public_verified_at) && $this->hu_verification_status === 'approved';
    }

    public function isVerifiedStaff(): bool
    {
        return $this->hu_role === 'community' && !is_null($this->hu_staff_verified_at);
    }

    public function isAvailable(): bool
    {
        return (bool) $this->hu_is_available;
    }

    public function isHardLocked(): bool
    {
        return (bool) ($this->hu_is_suspended || $this->hu_is_blacklisted);
    }

    public function isSellerRestricted(): bool
    {
        return (bool) ($this->isHardLocked() || $this->hu_is_blocked);
    }

    public function moderationStatusKey(): string
    {
        if ($this->hu_is_blacklisted) {
            return 'blacklisted';
        }

        if ($this->hu_is_suspended) {
            return 'suspended';
        }

        if ($this->hu_is_blocked) {
            return 'blocked';
        }

        return 'active';
    }

    public function getTrustBadgeAttribute(): string
    {
        // Staff gets priority badge even if they are community role
        if ($this->isVerifiedStaff()) {
            return 'Staf UPSI Rasmi';
        }
        if ($this->hu_role === 'student' && $this->hu_email_verified_at) {
            return 'Pelajar UPSI Terkini';
        }
        if ($this->isVerifiedPublic()) {
            return 'Pengguna Disahkan';
        }
        return 'Belum Disahkan';
    }

    public function getAverageRatingAttribute(): ?float
    {
        return $this->reviewsReceived()->avg('hr_rating');
    }

    public function studentStatus()
    {
        // Links User (id) -> StudentStatus (student_id)
        return $this->hasOne(StudentStatus::class, 'hss_student_id', 'hu_id');
    }

    public function serviceRequestsReceived()
    {
        return $this->hasMany(ServiceRequest::class, 'hsr_provider_id', 'hu_id');
    }

    public function sellerPoints()
    {
        return $this->hasMany(SellerPoint::class, 'hsp_user_id', 'hu_id');
    }

    public function buyerPoints()
    {
        return $this->hasMany(BuyerPoint::class, 'hbp_user_id', 'hu_id');
    }

    public function certificateRedemptions()
    {
        return $this->hasMany(CertificateRedemption::class, 'hcr_user_id', 'hu_id');
    }

    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class, 'hrr_user_id', 'hu_id');
    }

    /**
     * Get total seller points for this user
     */
    public function getTotalSellerPoints(): int
    {
        return $this->sellerPoints()->where('hsp_status', 'earned')->sum('hsp_points_earned');
    }

    /**
     * Get total buyer points for this user
     */
    public function getTotalBuyerPoints(): int
    {
        return $this->buyerPoints()->where('hbp_status', 'earned')->sum('hbp_points_earned');
    }

    /**
     * Get total points (seller + buyer) for this user
     */
    public function getTotalPoints(): int
    {
        return $this->getTotalSellerPoints() + $this->getTotalBuyerPoints();
    }

    /**
     * Get accessible total points based on user role
     */
    public function getAccessibleTotalPoints(): int
    {
        if ($this->canAccessSellerFeatures()) {
            return $this->getTotalPoints(); // Helpers: both seller + buyer points
        } else {
            return $this->getTotalBuyerPoints(); // Community: only buyer points
        }
    }

    /**
     * Check if user can access seller features (helpers only)
     */
    public function canAccessSellerFeatures(): bool
    {
        return $this->hu_role === 'helper' && !$this->hu_is_blocked;
    }

    /**
     * Check if user can access buyer features (all users)
     */
    public function canAccessBuyerFeatures(): bool
    {
        return true; // All users can request services and earn buyer points
    }

    /**
     * Check if user has enough points for certificate redemption
     */
    public function canRedeemCertificate($requiredPoints = 1): bool
    {
        return $this->getTotalSellerPoints() >= $requiredPoints;
    }

    public function getAuthPassword(): string
    {
        return (string) $this->hu_password;
    }

    public function getEmailForPasswordReset(): string
    {
        return (string) $this->hu_email;
    }

    public function getEmailForVerification(): string
    {
        return (string) $this->hu_email;
    }

    public function routeNotificationForMail($notification = null): string
    {
        return (string) $this->hu_email;
    }

    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->hu_email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'hu_email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Override Notifiable trait methods to use custom column names
     */
    public function unreadNotifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'hn_notifiable', 'hn_notifiable_type', 'hn_notifiable_id')
            ->whereNull('hn_read_at')
            ->orderBy('created_at', 'desc');
    }

    public function readNotifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'hn_notifiable', 'hn_notifiable_type', 'hn_notifiable_id')
            ->whereNotNull('hn_read_at')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the entity's read notifications count.
     */
    public function getReadNotificationsCountAttribute()
    {
        return $this->readNotifications()->count();
    }

    /**
     * Get the entity's unread notifications count.
     */
    public function getUnreadNotificationsCountAttribute()  
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Override the notify method to ensure proper routing
     */
    public function routeNotificationFor($driver, $notification = null)
    {
        if ($driver === 'database') {
            return $this->notifications();
        }

        // For other drivers like 'mail', return the appropriate route
        if ($driver === 'mail') {
            return $this->hu_email;
        }

        return null;
    }

    /**
     * Override morphMany for notifications to use custom columns
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
    {
        if ($related === DatabaseNotification::class && $name === 'hn_notifiable') {
            // Force the correct column names for this specific relationship
            return parent::morphMany($related, $name, $type ?? 'hn_notifiable_type', $id ?? 'hn_notifiable_id', $localKey);
        }
        
        return parent::morphMany($related, $name, $type, $id, $localKey);
    }
}
