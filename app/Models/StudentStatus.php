<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStatus extends Model
{
    use HasFactory;

    protected $table = 'student_statuses';

    protected $fillable = [
        'student_id',
        'matric_no',
        'semester',
        'status',
        'effective_date',
        'graduation_date',
    ];

    // RELATIONSHIP (Make sure this appears only ONCE)
    public function student()
    {
        // This links 'student_id' in this table to the 'id' in the 'users' table
        return $this->belongsTo(User::class, 'student_id');
    }
}