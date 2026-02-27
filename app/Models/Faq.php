<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
     protected $table = 'h2u_faqs';
     protected $primaryKey = 'hfq_id';

     protected $fillable = [
        'hfq_category',
        'hfq_question',
        'hfq_answer',
        'hfq_is_active',
        'hfq_display_order',
    ];
}

