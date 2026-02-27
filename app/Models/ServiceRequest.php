<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $table = 'h2u_service_requests';
    protected $primaryKey = 'hsr_id';

    protected $fillable = [
        'hsr_student_service_id',
        'hsr_requester_id',
        'hsr_provider_id',
        'hsr_selected_dates',
        'hsr_selected_time',
        'hsr_start_time',
        'hsr_end_time',
        'hsr_selected_package',
        'hsr_message',
        'hsr_offered_price',
        'hsr_status',
        'hsr_payment_status',
        'hsr_payment_proof',
        'hsr_dispute_reason',
        'hsr_reported_by',
        'hsr_rejection_reason',
        'hsr_accepted_at',
        'hsr_started_at',
        'hsr_finished_at',
        'hsr_completed_at'
    ];

    protected $casts = [
        'hsr_offered_price' => 'decimal:2',
        'hsr_accepted_at' => 'datetime',
        'hsr_started_at' => 'datetime',
        'hsr_finished_at' => 'datetime',
        'hsr_completed_at' => 'datetime',
        'hsr_selected_time' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'hsr_selected_dates' => 'array',
        'hsr_selected_package' => 'array',
    ];

    /**
     * Get the student service this request is for
     */
    public function studentService()
    {
        return $this->belongsTo(StudentService::class, 'hsr_student_service_id', 'hss_id');
    }

    /**
     * Get the community member who made the request
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'hsr_requester_id', 'hu_id');
    }

    /**
     * Get the student who provides the service
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'hsr_provider_id', 'hu_id');
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class)
                    ->where('hr_reviewee_id', $this->hsr_provider_id);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'hr_service_request_id', 'hsr_id');
    }

    public function getAverageRatingAttribute()
    {
        // We use 'receivedReviews' so we don't accidentally count reviews YOU wrote.
        return $this->receivedReviews()->avg('hr_rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->receivedReviews()->count();
    }

        public function review()
    {
            return $this->hasOne(Review::class, 'hr_service_request_id', 'hsr_id');
    }

    public function reviewForHelper()
    {
            return $this->hasOne(Review::class, 'hr_service_request_id', 'hsr_id')
            ->where('hr_reviewee_id', $this->hsr_provider_id);
    }

    public function reviewByHelper()
    {
            return $this->hasOne(Review::class, 'hr_service_request_id', 'hsr_id')
            ->where('hr_reviewer_id', $this->hsr_provider_id);
    }


    public function reviewForClient()
    {
        return $this->hasOne(Review::class, 'hr_service_request_id', 'hsr_id')
            ->where('hr_reviewee_id', $this->hsr_requester_id);
    }

    public function reviewByClient()
    {
        return $this->hasOne(Review::class, 'hr_service_request_id', 'hsr_id')
            ->where('hr_reviewer_id', $this->hsr_requester_id);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('hsr_status', 'pending');
    }

    /**
     * Scope for accepted requests
     */
    public function scopeAccepted($query)
    {
        return $query->where('hsr_status', 'accepted');
    }

    /**
     * Scope for completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->where('hsr_status', 'completed');
    }

    /**
     * Check if request is pending
     */
    public function isPending()
    {
        return $this->hsr_status === 'pending';
    }

    /**
     * Check if request is accepted
     */
    public function isAccepted()
    {
        return $this->hsr_status === 'accepted';
    }

    /**
     * Check if request is in progress
     */
    public function isInProgress()
    {
        return $this->hsr_status === 'in_progress';
    }

    public function isWorkFinished()
    {
        return $this->hsr_status === 'waiting_payment';
    }

    public function isPaid()
    {
        return $this->hsr_payment_status === 'paid';
    }

    public function isPaymentPending()
    {
        return $this->hsr_payment_status === 'verification_status';
    }

     public function PaidApproved()
    {
        // Backward compatibility alias
        return $this->isPaid();
    }

    public function isPaidApproved()
    {
        // Preferred named helper
        return $this->hsr_payment_status === 'paid';
    }

    /**
     * Check if request is completed
     */
    public function isCompleted()
    {
        return $this->hsr_status === 'completed';
    }

    /**
     * Accept the service request
     */
    public function accept()
    {
        $this->update([
            'hsr_status' => 'accepted',
            'hsr_accepted_at' => now()
        ]);
    }

    /**
     * Reject the service request
     */
    public function reject()
    {
        $this->update(['hsr_status' => 'rejected']);
    }

    /**
     * Mark as in progress
     */
    public function markInProgress()
    {
        $this->update(['hsr_status' => 'in_progress']);
    }

    /**
     * Mark as completed
     */
    public function markCompleted()
    {
        $this->update([
            'hsr_status' => 'completed',
            'hsr_completed_at' => now()
        ]);
    }

    /**
     * Check if user has reviewed this service request
     */
    public function hasUserReviewed($userId)
    {
        return $this->reviews()->where('hr_reviewer_id', $userId)->exists();
    }

    /**
     * Check if both parties have reviewed
     */
    public function bothPartiesReviewed()
    {
        $requesterReviewed = $this->reviews()->where('hr_reviewer_id', $this->hsr_requester_id)->exists();
        $providerReviewed = $this->reviews()->where('hr_reviewer_id', $this->hsr_provider_id)->exists();
        
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

        return $colors[$this->hsr_status] ?? 'bg-gray-100 text-gray-800';
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

        return $statuses[$this->hsr_status] ?? ucfirst((string) $this->hsr_status);
    }
}