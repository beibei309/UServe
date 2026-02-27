<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'h2u_admins';
    protected $primaryKey = 'ha_id';

    protected $fillable = [
        'ha_name',
        'ha_email',
        'ha_password',
        'ha_role'
    ];

    public $timestamps = true;

    public function getAuthPassword(): string
    {
        return (string) $this->ha_password;
    }
}
