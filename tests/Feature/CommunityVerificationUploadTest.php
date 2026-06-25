<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('community document upload approves account and records public verification time', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'hu_role' => 'community',
        'hu_email_verified_at' => now(),
        'hu_verification_status' => 'pending',
        'hu_public_verified_at' => null,
        'hu_verification_document_path' => null,
    ]);

    $response = $this->actingAs($user)->post(route('onboarding.community.submit_doc'), [
        'verification_document' => UploadedFile::fake()->image('identity-card.jpg')->size(128),
    ]);

    $response->assertSessionHas('success');

    $user->refresh();

    expect($user->hu_verification_status)->toBe('approved')
        ->and($user->hu_public_verified_at)->not->toBeNull()
        ->and($user->hu_trust_badge)->toBe('Verified Community Member');

    Storage::disk('local')->assertExists($user->hu_verification_document_path);
});
