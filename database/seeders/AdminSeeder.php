<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'super@s2u.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
        ]);

        Admin::create([
            'name' => 'Admin One',
            'email' => 'admin1@s2u.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        Admin::create([
            'name' => 'Admin Two',
            'email' => 'admin2@s2u.com',
            'password' => Hash::make('admin456'),
            'role' => 'admin',
        ]);
    }
}
