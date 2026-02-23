<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceApplicationInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_application_id',
        'student_id',
        'message',
        'status',
        'selected_at',
        'declined_at',
    ];

    protected $casts = [
        'selected_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(ServiceApplication::class, 'service_application_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class, 'interest_id');
    }
}