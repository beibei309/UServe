<?php

use App\Models\StudentStatus;
use App\Models\User;

function createActiveHelperForModeSwitch(): User
{
    $helper = User::factory()->create([
        'hu_role' => 'helper',
        'hu_is_blocked' => false,
        'hu_helper_verified_at' => now(),
    ]);

    StudentStatus::create([
        'hss_student_id' => $helper->hu_id,
        'hss_matric_no' => 'D'.fake()->unique()->numerify('##########'),
        'hss_semester' => '1',
        'hss_status' => 'Active',
        'hss_effective_date' => now()->toDateString(),
        'hss_graduation_date' => now()->addYear()->toDateString(),
    ]);

    return $helper;
}

test('helper first switch click from default buyer mode goes to seller dashboard', function () {
    $helper = createActiveHelperForModeSwitch();

    $response = $this->actingAs($helper)->post(route('switch.mode'));

    $response->assertRedirect(route('students.index'));
    $this->assertSame('seller', session('view_mode'));
});

test('helper switch toggles from seller mode back to buyer mode', function () {
    $helper = createActiveHelperForModeSwitch();

    session(['view_mode' => 'seller']);

    $response = $this->actingAs($helper)->post(route('switch.mode'));

    $response->assertRedirect(route('dashboard'));
    $this->assertSame('buyer', session('view_mode'));
});
