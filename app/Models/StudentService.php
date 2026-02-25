<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'image_path',
        'description',
        'status',
        'is_active',
        'unavailable_dates',
        'operating_hours',
        'session_duration',
        'blocked_slots',
        // Basic package
        'basic_duration',
        'basic_frequency',
        'basic_price',
        'basic_description',
        // Standard package
        'standard_duration',
        'standard_frequency',
        'standard_price',
        'standard_description',
        // Premium package
        'premium_duration',
        'premium_frequency',
        'premium_price',
        'premium_description',
    ];

    protected $casts = [
        'operating_hours' => 'array', 
        'unavailable_dates' => 'array',
        'blocked_slots' => 'array',
    ];

    protected $attributes = [
        'status' => 'available',
        'is_active' => true,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Backward compatibility alias */
    public function student() { return $this->user(); }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function reviews()
{
    // Pastikan 'student_service_id' wujud dalam table reviews
    return $this->hasMany(Review::class, 'student_service_id');
}


    /**
     * Check if service is available
     */
    public function isAvailable()
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Mark service as busy/unavailable
     */
    public function markAsBusy()
    {
        $this->update(['status' => 'busy']);
    }

    /**
     * Mark service as available again
     */
    public function markAsAvailable()
    {
        $this->update(['status' => 'available']);
    }
    
    public function orders()
    {
        return $this->hasMany(\App\Models\ServiceRequest::class, 'student_service_id');
    }

public function favoritedBy()
{
    return $this->belongsToMany(
        \App\Models\User::class,
        'favorites',
        'service_id',
        'user_id'
    );
}


public function getIsFavouritedAttribute()
{
    if (!Auth::check()) {
        return false;
    }

    return DB::table('favorites')
        ->where('user_id', Auth::id())
        ->where('service_id', $this->id)
        ->exists();
}





}