<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_service_id',
        'requester_id',
        'provider_id',
        'selected_dates',
        'selected_time',
        'start_time',
        'end_time',
        'selected_package',
        'message',
        'offered_price',
        'status',
        'payment_status',
        'payment_proof',
        'dispute_reason',
        'reported_by',
        'rejection_reason',
        'accepted_at',
        'started_at',
        'finished_at',
        'completed_at'
    ];

    protected $casts = [
        'offered_price' => 'decimal:2',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'completed_at' => 'datetime',
        'selected_time' => 'string',        
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'selected_dates' => 'datetime', 
        'selected_package' => 'string',
    ];

    /**
     * Get the student service this request is for
     */
    public function studentService()
    {
        return $this->belongsTo(StudentService::class);
    }

    /**
     * Get the community member who made the request
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the student who provides the service
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class)
                    ->where('reviewee_id', $this->user_id);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        // We use 'receivedReviews' so we don't accidentally count reviews YOU wrote.
        return $this->receivedReviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->receivedReviews()->count();
    }

        public function review()
    {
        return $this->hasOne(Review::class, 'service_request_id');
    }

    public function reviewForHelper()
    {
        return $this->hasOne(Review::class, 'service_request_id')
            ->where('reviewee_id', $this->provider_id);
    }

    public function reviewByHelper()
    {
        return $this->hasOne(Review::class, 'service_request_id')
            ->where('reviewer_id', $this->provider_id);
    }


    public function reviewForClient()
    {
        return $this->hasOne(Review::class, 'service_request_id')
            ->where('reviewee_id', $this->requester_id);
    }

    public function reviewByClient()
    {
        return $this->hasOne(Review::class, 'service_request_id')
            ->where('reviewer_id', $this->requester_id);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted requests
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope for completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if request is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is accepted
     */
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if request is in progress
     */
    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isWorkFinished()
    {
        return $this->status === 'waiting_payment';
    }

    public function isPaid()
    {
        return $this->payment_status === 'verification_status';
    }

     public function PaidApproved()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if request is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Accept the service request
     */
    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now()
        ]);
    }

    /**
     * Reject the service request
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Mark as in progress
     */
    public function markInProgress()
    {
        $this->update(['status' => 'in_progress']);
    }

    /**
     * Mark as completed
     */
    public function markCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Check if user has reviewed this service request
     */
    public function hasUserReviewed($userId)
    {
        return $this->reviews()->where('reviewer_id', $userId)->exists();
    }

    /**
     * Check if both parties have reviewed
     */
    public function bothPartiesReviewed()
    {
        $requesterReviewed = $this->reviews()->where('reviewer_id', $this->requester_id)->exists();
        $providerReviewed = $this->reviews()->where('reviewer_id', $this->provider_id)->exists();
        
        return $requesterReviewed && $providerReviewed;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'accepted' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'in_progress' => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800'
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute()
    {
        $statuses = [
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }
}