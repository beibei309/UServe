<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    protected $table = 'h2u_legal_pages';
    protected $primaryKey = 'hlp_id';

    protected $fillable = [
        'hlp_slug',
        'hlp_title',
        'hlp_content',
        'hlp_is_active',
    ];

    protected function casts(): array
    {
        return [
            'hlp_is_active' => 'boolean',
        ];
    }
}
