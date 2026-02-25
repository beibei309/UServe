<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'student_service_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'reply',
        'replied_at',
        'comment',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function service()
    {
        return $this->belongsTo(StudentService::class, 'student_service_id');
    }

    /** Backward compatibility alias */
    public function studentService() { return $this->service(); }


    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}