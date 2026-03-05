<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class StudentStatus extends Model
{
    use HasFactory;

    protected $table = 'h2u_student_statuses';
    protected $primaryKey = 'hss_id';

    protected $fillable = [
        'hss_student_id',
        'hss_matric_no',
        'hss_semester',
        'hss_status',
        'hss_effective_date',
        'hss_graduation_date',
    ];

    // RELATIONSHIP (Make sure this appears only ONCE)
    public function student()
    {
        return $this->belongsTo(User::class, 'hss_student_id', 'hu_id');
    }

    public function getGraduationDateDisplayAttribute(): string
    {
        if (!$this->hss_graduation_date) {
            return '-';
        }

        return Carbon::parse($this->hss_graduation_date)->format('d M Y');
    }
}
