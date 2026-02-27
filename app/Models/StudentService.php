<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentService extends Model
{
    use HasFactory;

    protected $table = 'h2u_student_services';
    protected $primaryKey = 'hss_id';

    protected $fillable = [
        'hss_user_id',
        'hss_category_id',
        'hss_title',
        'hss_image_path',
        'hss_description',
        'hss_status',
        'hss_is_active',
        'hss_unavailable_dates',
        'hss_operating_hours',
        'hss_session_duration',
        'hss_blocked_slots',
        'hss_booking_mode',
        'hss_approval_status',
        'hss_warning_count',
        'hss_warning_reason',
        'hss_suggested_price',
        'hss_price_range',
        // Basic package
        'hss_basic_duration',
        'hss_basic_frequency',
        'hss_basic_price',
        'hss_basic_description',
        // Standard package
        'hss_standard_duration',
        'hss_standard_frequency',
        'hss_standard_price',
        'hss_standard_description',
        // Premium package
        'hss_premium_duration',
        'hss_premium_frequency',
        'hss_premium_price',
        'hss_premium_description',
    ];

    protected $casts = [
        'hss_operating_hours' => 'array', 
        'hss_unavailable_dates' => 'array',
        'hss_blocked_slots' => 'array',
    ];

    protected $attributes = [
        'hss_status' => 'available',
        'hss_is_active' => true,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'hss_user_id', 'hu_id');
    }

    /** Backward compatibility alias */
    public function student() { return $this->user(); }

    public function category()
    {
        return $this->belongsTo(Category::class, 'hss_category_id', 'hc_id');
    }


    public function reviews()
{
    return $this->hasMany(Review::class, 'hr_student_service_id', 'hss_id');
}


    /**
     * Check if service is available
     */
    public function isAvailable()
    {
        return $this->hss_status === 'available' && $this->hss_is_active;
    }

    /**
     * Mark service as busy/unavailable
     */
    public function markAsBusy()
    {
        $this->update(['hss_status' => 'busy']);
    }

    /**
     * Mark service as available again
     */
    public function markAsAvailable()
    {
        $this->update(['hss_status' => 'available']);
    }
    
    public function orders()
    {
        return $this->hasMany(\App\Models\ServiceRequest::class, 'hsr_student_service_id', 'hss_id');
    }

public function favoritedBy()
{
    return $this->belongsToMany(
        \App\Models\User::class,
        'h2u_favorites',
        'hf_service_id',
        'hf_user_id',
        'hss_id',
        'hu_id'
    );
}


public function getIsFavouritedAttribute()
{
    if (!Auth::check()) {
        return false;
    }

    return DB::table('h2u_favorites')
        ->where('hf_user_id', Auth::id())
        ->where('hf_service_id', $this->hss_id)
        ->exists();
}





}