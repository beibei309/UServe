<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceApplicationResponse;

class ServiceApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_id',
        'conversation_id',
        'service_type',
        'title',
        'description',
        'budget_range',
        'timeline',
        'contact_methods',
        'status',
        'customer_completed',
        'provider_completed',
        'customer_completed_at',
        'provider_completed_at',
        'fully_completed_at'
    ];

    protected $casts = [
        'contact_methods' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'customer_completed_at' => 'datetime',
        'provider_completed_at' => 'datetime',
        'fully_completed_at' => 'datetime',
        'customer_completed' => 'boolean',
        'provider_completed' => 'boolean'
    ];

    /**
     * Get the user who submitted this application
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service this application is for
     */
    public function service()
    {
        return $this->belongsTo(StudentService::class, 'service_id');
    }

    /**
     * Get the conversation this application is related to
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'service_application_id');
    }

    /**
     * Get the responses to this application
     */
    public function responses()
    {
        return $this->hasMany(ServiceApplicationResponse::class);
    }

    /**
     * Students' expressed interests for open applications
     */
    public function interests()
    {
        return $this->hasMany(ServiceApplicationInterest::class, 'service_application_id');
    }

    /**
     * Currently selected interest (if any)
     */
    public function selectedInterest()
    {
        return $this->hasOne(ServiceApplicationInterest::class, 'service_application_id')->where('status', 'selected');
    }

    /**
     * Scope for open applications
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for closed applications
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope for completed applications
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get formatted budget range
     */
    public function getFormattedBudgetAttribute()
    {
        $ranges = [
            'under_50' => 'Under RM 50',
            '50_100' => 'RM 50 - RM 100',
            '100_200' => 'RM 100 - RM 200',
            '200_500' => 'RM 200 - RM 500',
            '500_1000' => 'RM 500 - RM 1,000',
            'over_1000' => 'Over RM 1,000',
            'negotiable' => 'Negotiable'
        ];

        return $ranges[$this->budget_range] ?? 'Not specified';
    }

    /**
     * Get formatted timeline
     */
    public function getFormattedTimelineAttribute()
    {
        $timelines = [
            'asap' => 'As soon as possible',
            'within_week' => 'Within a week',
            'within_month' => 'Within a month',
            'flexible' => 'Flexible timeline',
            'ongoing' => 'Ongoing project'
        ];

        return $timelines[$this->timeline] ?? $this->timeline;
    }

    /**
     * Get formatted service type
     */
    public function getFormattedServiceTypeAttribute()
    {
        $types = [
            'tutoring' => 'Academic Tutoring',
            'design' => 'Graphic Design',
            'web_development' => 'Web Development',
            'photography' => 'Photography',
            'writing' => 'Content Writing',
            'translation' => 'Translation Services',
            'music' => 'Music Lessons',
            'fitness' => 'Fitness Training',
            'event_planning' => 'Event Planning',
            'other' => 'Other'
        ];

        return $types[$this->service_type] ?? ucfirst(str_replace('_', ' ', $this->service_type));
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            // Informational / available
            'open' => 'bg-blue-100 text-blue-800',
            // Positive states
            'accepted' => 'bg-green-100 text-green-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            // Neutral/terminal
            'closed' => 'bg-gray-100 text-gray-800',
            // Negative states
            'cancelled' => 'bg-red-100 text-red-800',
            'rejected' => 'bg-red-100 text-red-800',
            'declined' => 'bg-red-100 text-red-800',
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if application is still open
     */
    public function isOpen()
    {
        return $this->status === 'open';
    }

    /**
     * Check if application is closed
     */
    public function isClosed()
    {
        return $this->status === 'closed';
    }

    /**
     * Check if both parties have marked the service as completed
     */
    public function isFullyCompleted()
    {
        return $this->customer_completed && $this->provider_completed;
    }

    /**
     * Mark as completed by customer
     */
    public function markCompletedByCustomer()
    {
        $this->update([
            'customer_completed' => true,
            'customer_completed_at' => now()
        ]);

        $this->checkAndMarkFullyCompleted();
    }

    /**
     * Mark as completed by provider
     */
    public function markCompletedByProvider()
    {
        $this->update([
            'provider_completed' => true,
            'provider_completed_at' => now()
        ]);

        $this->checkAndMarkFullyCompleted();
    }

    /**
     * Check if both parties completed and mark as fully completed
     */
    private function checkAndMarkFullyCompleted()
    {
        if ($this->customer_completed && $this->provider_completed && !$this->fully_completed_at) {
            $this->update([
                'status' => 'completed',
                'fully_completed_at' => now()
            ]);

            // Make service available again
            if ($this->service) {
                $this->service->markAsAvailable();
            }
        }
    }

    /**
     * Check if user can mark as completed
     */
    public function canBeMarkedCompletedBy($user)
    {
        if ($this->status !== 'accepted') {
            return false;
        }

        // Customer can mark if they haven't already
        if ($user->id === $this->user_id) {
            return !$this->customer_completed;
        }

        // Provider can mark if they haven't already
        // Case 1: Linked student service, provider is the service owner
        if ($this->service && $user->id === $this->service->user_id) {
            return !$this->provider_completed;
        }

        // Case 2: Custom application with selected interest; provider is selected student
        if (!$this->service) {
            $selected = $this->selectedInterest()->first();
            if ($selected && $user->id === $selected->student_id) {
                return !$this->provider_completed;
            }
        }

        return false;
    }

    /**
     * Check if application is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}