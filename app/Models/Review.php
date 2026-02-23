<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'service_request_id',
        'service_application_id',
        'student_service_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'reply',
        'replied_at',
        'comment',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function serviceApplication()
    {
        return $this->belongsTo(ServiceApplication::class);
    }

    public function service()
    {

        return $this->belongsTo(StudentService::class, 'student_service_id');
    }

    public function studentService()
    {
        return $this->belongsTo(StudentService::class, 'student_service_id');
    }


    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}