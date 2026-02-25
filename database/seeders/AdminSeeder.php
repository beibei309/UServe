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
            'email' => 'super@s2u.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
        ]);

        Admin::updateOrCreate([
            'email' => 'admin1@s2u.com',
        ], [
            'name' => 'Admin One',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        Admin::updateOrCreate([
            'email' => 'admin2@s2u.com',
        ], [
            'name' => 'Admin Two',
            'password' => Hash::make('admin456'),
            'role' => 'admin',
        ]);
    }
}
