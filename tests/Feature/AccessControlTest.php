<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('non helper users are redirected from seller dashboard route', function () {
    $user = User::factory()->create([
        'role' => 'student',
        'email_verified_at' => now(),
        'verification_status' => 'approved',
        'helper_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('students.index'));

    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertSessionHas('info', 'This page is for verified helpers only.');
});

test('unverified community users cannot perform protected post actions', function () {
    $user = User::factory()->create([
        'role' => 'community',
        'email_verified_at' => now(),
        'verification_status' => 'pending',
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
        'name' => 'Normal Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.super.admins.index'));

    $response
        ->assertRedirect(route('admin.dashboard', absolute: false))
        ->assertSessionHas('error', 'Access denied — Superadmin only.');
});

test('superadmin can access superadmin routes', function () {
    $superadmin = Admin::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@example.com',
        'password' => Hash::make('password'),
        'role' => 'superadmin',
    ]);

    $response = $this->actingAs($superadmin, 'admin')->get(route('admin.super.admins.index'));

    $response->assertStatus(200);
});
