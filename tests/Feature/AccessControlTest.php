<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('non helper users are redirected from seller dashboard route', function () {
    $user = User::factory()->create([
        'hu_role' => 'student',
        'hu_email_verified_at' => now(),
        'hu_verification_status' => 'approved',
        'hu_helper_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('students.index'));

    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertSessionHas('info', 'This page is for verified helpers only.');
});

test('unverified community users cannot perform protected post actions', function () {
    $user = User::factory()->create([
        'hu_role' => 'community',
        'hu_email_verified_at' => now(),
        'hu_verification_status' => 'pending',
    ]);

    $response = $this->actingAs($user)
        ->from('/services')
        ->post(route('reviews.store'), []);

    $response
        ->assertRedirect('/services')
        ->assertSessionHas('info', 'Please complete community verification first.');
});

test('admin cannot access superadmin routes', function () {
    $admin = Admin::create([
        'ha_name' => 'Normal Admin',
        'ha_email' => 'admin@example.com',
        'ha_password' => Hash::make('password'),
        'ha_role' => 'admin',
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.super.admins.index'));

    $response
        ->assertRedirect(route('admin.dashboard', absolute: false))
        ->assertSessionHas('error', 'Access denied — Superadmin only.');
});

test('superadmin can access superadmin routes', function () {
    $superadmin = Admin::create([
        'ha_name' => 'Super Admin',
        'ha_email' => 'superadmin@example.com',
        'ha_password' => Hash::make('password'),
        'ha_role' => 'superadmin',
    ]);

    $response = $this->actingAs($superadmin, 'admin')->get(route('admin.super.admins.index'));

    $response->assertStatus(200);
});
