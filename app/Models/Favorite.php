<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'h2u_favorites';
    protected $primaryKey = 'hf_id';

    protected $fillable = [
        'hf_user_id',
        'hf_favorited_user_id',
        'hf_service_id',
    ];

    public function service()
    {
        return $this->belongsTo(StudentService::class, 'hf_service_id', 'hss_id');
    }
}