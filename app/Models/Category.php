<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'icon',
        'color',
        'is_active'
    
    ];

    public function services()
    {
        return $this->hasMany(StudentService::class);
    }
}