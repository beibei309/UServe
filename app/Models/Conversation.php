<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_request_id',
        'student_id',
        'customer_id',
        'started_at',
        'ended_at',
    ];

    public function chatRequest()
    {
        return $this->belongsTo(ChatRequest::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}