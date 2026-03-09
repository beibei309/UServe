<?php

use App\Models\User;

test('helper first switch click from default buyer mode goes to seller dashboard', function () {
    $helper = User::factory()->create([
        'hu_role' => 'helper',
        'hu_is_blocked' => false,
        'hu_helper_verified_at' => now(),
    ]);

    $response = $this->actingAs($helper)->post(route('switch.mode'));

    $response->assertRedirect(route('students.index'));
    $this->assertSame('seller', session('view_mode'));
});

test('helper switch toggles from seller mode back to buyer mode', function () {
    $helper = User::factory()->create([
        'hu_role' => 'helper',
        'hu_is_blocked' => false,
        'hu_helper_verified_at' => now(),
    ]);

    session(['view_mode' => 'seller']);

    $response = $this->actingAs($helper)->post(route('switch.mode'));

    $response->assertRedirect(route('dashboard'));
    $this->assertSame('buyer', session('view_mode'));
});
