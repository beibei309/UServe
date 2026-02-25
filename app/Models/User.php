<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Review;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'student_id',
        'profile_photo_path',
        'selfie_media_path',
        'public_verified_at',
        'verification_status',
        'staff_email',
        'reports_count',
        'staff_verified_at',
        'is_available',
        'is_suspended',
        'is_blacklisted',
        'is_blocked',
        'warning_count',
        'blacklist_reason',
        'bio',
        'faculty',
        'course',
        'address',
        'latitude',
        'longitude',
        'location_verified_at',
        'skills',
        'work_experience_message',
        'work_experience_file', 
        'verification_document_path',
        'verification_note',  
        'helper_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'email_verified_at' => 'datetime',
            'public_verified_at' => 'datetime',
            'staff_verified_at' => 'datetime',
            'helper_verified_at' => 'datetime',
            'location_verified_at' => 'datetime',
            'is_available' => 'boolean',
            'is_suspended' => 'boolean',
            'is_blacklisted' => 'boolean',
            'is_blocked' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function services()
    {
        return $this->hasMany(StudentService::class, 'user_id');
    }

    /** Backward compatibility aliases */
    public function studentServices() { return $this->services(); }
    public function student_services() { return $this->services(); }
    
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }
    


public function favoriteServices()
{
    return $this->belongsToMany(
        \App\Models\StudentService::class,
        'favorites',   // pivot table
        'user_id',     // user_id on favorites
        'service_id'   // service_id on favorites (IMPORTANT)
    )->withTimestamps();
}



    // Helpers
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isCommunity(): bool
    {
        return $this->role === 'community' || $this->role === 'staff';
    }

    public function isStaff(): bool
    {
        // Only community accounts with staff verification qualify as staff
        return $this->role === 'community' && !is_null($this->staff_verified_at) && !is_null($this->staff_email);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVerifiedPublic(): bool
    {
        return !is_null($this->public_verified_at) && $this->verification_status === 'approved';
    }

    public function isVerifiedStaff(): bool
    {
        return $this->role === 'community' && !is_null($this->staff_verified_at);
    }

    public function isAvailable(): bool
    {
        return (bool) $this->is_available;
    }

    public function getTrustBadgeAttribute(): string
    {
        // Staff gets priority badge even if they are community role
        if ($this->isVerifiedStaff()) {
            return 'Staf UPSI Rasmi';
        }
        if ($this->role === 'student' && $this->email_verified_at) {
            return 'Pelajar UPSI Terkini';
        }
        if ($this->isVerifiedPublic()) {
            return 'Pengguna Disahkan';
        }
        return 'Belum Disahkan';
    }

    public function getAverageRatingAttribute(): ?float
    {
        return $this->reviewsReceived()->avg('rating');
    }

    public function studentStatus()
    {
        // Links User (id) -> StudentStatus (student_id)
        return $this->hasOne(StudentStatus::class, 'student_id');
    }

    public function serviceRequestsReceived()
    {
        return $this->hasMany(ServiceRequest::class, 'provider_id');
    }
}
