<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'h2u_categories';
    protected $primaryKey = 'hc_id';

    protected $fillable = [
        'hc_name',
        'hc_slug',
        'hc_description',
        'hc_image_path',
        'hc_icon',
        'hc_color',
        'hc_is_active'
    
    ];

    public function services()
    {
        return $this->hasMany(StudentService::class, 'hss_category_id', 'hc_id');
    }
}