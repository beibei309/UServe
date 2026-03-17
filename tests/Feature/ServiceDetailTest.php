
it('returns service details with correct structure', function () {
    // Seed minimal service flow
    if (!function_exists('seedMinimalServiceFlow')) {
        require __DIR__.'/SeedersReviewAndPointsTest.php';
    }
    $seed = seedMinimalServiceFlow();

    // Get a valid service ID
    $serviceId = $seed['serviceId'];

    $response = $this->getJson('/api/v1/services/' . $serviceId);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'id',
            'title',
            'description',
            'status',
            'is_active',
            'approval_status',
            'created_at',
            'updated_at',
            'pricing',
            'packages',
            'image',
            'provider',
            'category',
            'stats',
            'availability',
            'reviews',
            'metadata'
        ]
    ]);
    $response->assertJson(['success' => true]);
    expect($response['data']['id'])->toEqual($serviceId);
});
