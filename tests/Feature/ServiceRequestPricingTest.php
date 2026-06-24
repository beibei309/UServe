<?php

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function seedPricingService(string $frequency, float $price): array
{
    $now = now();
    $suffix = uniqid('', true);

    $providerId = DB::table('h2u_users')->insertGetId([
        'hu_name' => 'Pricing Provider',
        'hu_email' => "pricing-provider-{$suffix}@example.test",
        'hu_password' => Hash::make('password'),
        'hu_role' => 'helper',
        'hu_verification_status' => 'approved',
        'hu_public_verified_at' => $now,
        'hu_helper_verified_at' => $now,
        'hu_email_verified_at' => $now,
        'hu_is_available' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hu_id');

    $buyerId = DB::table('h2u_users')->insertGetId([
        'hu_name' => 'Pricing Buyer',
        'hu_email' => "pricing-buyer-{$suffix}@example.test",
        'hu_password' => Hash::make('password'),
        'hu_role' => 'community',
        'hu_verification_status' => 'approved',
        'hu_public_verified_at' => $now,
        'hu_email_verified_at' => $now,
        'hu_is_available' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hu_id');

    $categoryId = DB::table('h2u_categories')->insertGetId([
        'hc_name' => "Pricing Category {$suffix}",
        'hc_slug' => "pricing-category-{$suffix}",
        'hc_description' => 'Pricing category',
        'hc_color' => '#4f46e5',
        'hc_is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hc_id');

    $serviceId = DB::table('h2u_student_services')->insertGetId([
        'hss_user_id' => $providerId,
        'hss_category_id' => $categoryId,
        'hss_title' => "Pricing Service {$suffix}",
        'hss_description' => 'Pricing service description',
        'hss_status' => 'available',
        'hss_booking_mode' => 'session',
        'hss_session_duration' => 60,
        'hss_is_active' => true,
        'hss_approval_status' => 'approved',
        'hss_basic_duration' => '2 hours',
        'hss_basic_frequency' => $frequency,
        'hss_basic_price' => $price,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hss_id');

    return [
        'buyer' => User::findOrFail($buyerId),
        'service_id' => $serviceId,
    ];
}

it('keeps a per-session package price fixed regardless of package duration', function () {
    $seed = seedPricingService('Per Session', 250.00);

    $this->actingAs($seed['buyer'])
        ->postJson(route('service-requests.store'), [
            'student_service_id' => $seed['service_id'],
            'selected_dates' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'selected_package' => 'basic',
            'message' => 'Portrait session',
            'offered_price' => 1.00,
        ])
        ->assertOk();

    $request = ServiceRequest::query()->latest('hsr_id')->firstOrFail();

    expect((float) $request->hsr_offered_price)->toBe(250.0);
});

it('scales an hourly package price using the booked duration', function () {
    $seed = seedPricingService('Per Hour', 150.00);

    $this->actingAs($seed['buyer'])
        ->postJson(route('service-requests.store'), [
            'student_service_id' => $seed['service_id'],
            'selected_dates' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'selected_package' => 'basic',
            'message' => 'Hourly session',
            'offered_price' => 1.00,
        ])
        ->assertOk();

    $request = ServiceRequest::query()->latest('hsr_id')->firstOrFail();

    expect((float) $request->hsr_offered_price)->toBe(300.0);
});

it('rejects a package name that does not belong to the service', function () {
    $seed = seedPricingService('Per Session', 250.00);

    $this->actingAs($seed['buyer'])
        ->postJson(route('service-requests.store'), [
            'student_service_id' => $seed['service_id'],
            'selected_dates' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'selected_package' => 'custom-free-package',
            'message' => 'Invalid package attempt',
            'offered_price' => 0,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('selected_package');
});
