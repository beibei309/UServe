<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'h2u_reviews';
    protected $primaryKey = 'hr_id';

    protected $fillable = [
        'hr_service_request_id',
        'hr_student_service_id',
        'hr_reviewer_id',
        'hr_reviewee_id',
        'hr_rating',
        'hr_reply',
        'hr_replied_at',
        'hr_comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'hr_replied_at' => 'datetime',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'hr_service_request_id', 'hsr_id');
    }

    public function service()
    {
        return $this->belongsTo(StudentService::class, 'hr_student_service_id', 'hss_id');
    }

    /** Backward compatibility alias */
    public function studentService() { return $this->service(); }


    public function reviewer()
    {
        return $this->belongsTo(User::class, 'hr_reviewer_id', 'hu_id');
    }

    /** Backward compatibility alias */
    public function user()
    {
        return $this->reviewer();
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'hr_reviewee_id', 'hu_id');
    }
}