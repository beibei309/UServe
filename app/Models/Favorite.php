<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'favorited_user_id',
        'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(StudentService::class, 'service_id');
    }
}