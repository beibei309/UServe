<?php

use App\Models\User;

it('does not give an unverified community account a trust badge', function () {
    $user = User::factory()->make([
        'hu_role' => 'community',
        'hu_verification_status' => 'pending',
        'hu_public_verified_at' => null,
        'hu_staff_verified_at' => null,
    ]);

    expect($user->hu_trust_badge)->toBeNull();
});

it('labels verified account types clearly', function (array $attributes, string $expectedBadge) {
    $user = User::factory()->make($attributes);

    expect($user->hu_trust_badge)->toBe($expectedBadge);
})->with([
    'student' => [[
        'hu_role' => 'student',
        'hu_email_verified_at' => now(),
    ], 'Verified UPSI Student'],
    'helper' => [[
        'hu_role' => 'helper',
        'hu_email_verified_at' => now(),
        'hu_helper_verified_at' => now(),
    ], 'Verified UPSI Student Helper'],
    'staff' => [[
        'hu_role' => 'community',
        'hu_staff_email' => 'staff@upsi.edu.my',
        'hu_staff_verified_at' => now(),
    ], 'Verified UPSI Staff'],
    'public community' => [[
        'hu_role' => 'community',
        'hu_verification_status' => 'approved',
        'hu_public_verified_at' => now(),
        'hu_staff_verified_at' => null,
    ], 'Verified Community Member'],
]);
