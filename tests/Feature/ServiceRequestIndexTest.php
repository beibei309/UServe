<?php

use Illuminate\Support\Facades\DB;

it('returns all service requests with correct structure', function () {
    // Seed minimal service flow
        require_once __DIR__.'/TestSeederHelper.php';
    $seed = seedMinimalServiceFlow();

    // Authenticate as community user (if API requires auth, add token logic here)
    $user = DB::table('h2u_users')->where('hu_id', $seed['communityId'])->first();
    // If using Sanctum or Passport, generate token and set header
    // $token = $user->createToken('TestToken')->plainTextToken;
    // $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->get('/api/v1/services');

    $response = $this->get('/api/v1/services');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data',
        'pagination'
    ]);
    $response->assertJson(['success' => true]);
    expect($response['data'])->toBeArray();
    expect($response['pagination'])->toBeArray();
});
