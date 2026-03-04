<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Review;
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

    public function notifications(): MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'hn_notifiable')
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
}
