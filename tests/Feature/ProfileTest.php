<?php

use App\Models\User;
use App\Notifications\StaffEmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();
    $originalEmail = $user->hu_email;
    $originalVerificationAt = $user->hu_email_verified_at;

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->hu_name);
<<<<<<< HEAD
<<<<<<< HEAD
    $this->assertSame('test@example.com', $user->hu_email);
    $this->assertNull($user->hu_email_verified_at);
=======
    $this->assertSame($originalEmail, $user->hu_email);
    $this->assertEquals($originalVerificationAt, $user->hu_email_verified_at);
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
    $this->assertSame($originalEmail, $user->hu_email);
    $this->assertEquals($originalVerificationAt, $user->hu_email_verified_at);
>>>>>>> develop
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->hu_email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->hu_email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('changing community staff email sends verification notification', function () {
    Notification::fake();

    $user = User::factory()->create([
        'hu_role' => 'community',
        'hu_staff_email' => null,
        'hu_staff_verified_at' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->hu_name,
            'staff_email' => 'lecturer@upsi.edu.my',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    $this->assertSame('lecturer@upsi.edu.my', $user->hu_staff_email);
    $this->assertNull($user->hu_staff_verified_at);

    Notification::assertSentOnDemand(StaffEmailVerificationNotification::class);
});

test('signed staff verification link verifies authenticated user staff email', function () {
    $user = User::factory()->create([
        'hu_role' => 'community',
        'hu_staff_email' => 'lecturer@upsi.edu.my',
        'hu_staff_verified_at' => null,
    ]);

    $url = URL::temporarySignedRoute('profile.staff.verify', now()->addMinutes(30), [
        'id' => $user->hu_id,
        'hash' => sha1(strtolower((string) $user->hu_staff_email)),
    ]);

    $response = $this->actingAs($user)->get($url);

    $response->assertRedirect('/profile');
    $this->assertNotNull($user->fresh()->hu_staff_verified_at);
});
