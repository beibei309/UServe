<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'h2u_reports';
    protected $primaryKey = 'hrp_id';

    protected $fillable = [
        'hrp_reporter_id',
        'hrp_target_user_id',
        'hrp_reason',
        'hrp_details',
        'hrp_status',
        'hrp_action_taken',
        'hrp_resolved_at',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'hrp_reporter_id', 'hu_id');
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'hrp_target_user_id', 'hu_id');
    }
}