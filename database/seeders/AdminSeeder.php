<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::updateOrCreate([
            'ha_email' => 'super@s2u.com',
        ], [
            'ha_name' => 'Super Admin',
            'ha_password' => Hash::make('password123'),
            'ha_role' => 'superadmin',
        ]);

        Admin::updateOrCreate([
            'ha_email' => 'admin1@s2u.com',
        ], [
            'ha_name' => 'Admin One',
            'ha_password' => Hash::make('admin123'),
            'ha_role' => 'admin',
        ]);

        Admin::updateOrCreate([
            'ha_email' => 'admin2@s2u.com',
        ], [
            'ha_name' => 'Admin Two',
            'ha_password' => Hash::make('admin456'),
            'ha_role' => 'admin',
        ]);
    }
}
