<?php

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function seedPricingService(string $frequency, float $price, array $serviceOverrides = []): array
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

    $serviceId = DB::table('h2u_student_services')->insertGetId(array_merge([
        'hss_user_id' => $providerId,
        'hss_category_id' => $categoryId,
        'hss_title' => "Pricing Service {$suffix}",
        'hss_description' => 'Pricing service description',
        'hss_status' => 'available',
        'hss_booking_mode' => 'session',
        'hss_session_duration' => 60,
        'hss_operating_hours' => json_encode([
            'mon' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'tue' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'wed' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'thu' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'fri' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
            'sat' => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'],
            'sun' => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'],
        ]),
        'hss_unavailable_dates' => json_encode([]),
        'hss_is_active' => true,
        'hss_approval_status' => 'approved',
        'hss_basic_duration' => '2 hours',
        'hss_basic_frequency' => $frequency,
        'hss_basic_price' => $price,
        'created_at' => $now,
        'updated_at' => $now,
    ], $serviceOverrides), 'hss_id');

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

it('rejects a fixed-price booking whose submitted time does not match the package duration', function () {
    $seed = seedPricingService('Per Session', 250.00);

    $this->actingAs($seed['buyer'])
        ->postJson(route('service-requests.store'), [
            'student_service_id' => $seed['service_id'],
            'selected_dates' => now()->nextWeekday()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'selected_package' => 'basic',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'The selected package requires a 2h booking.');
});

it('rejects a booking outside the providers working hours', function () {
    $seed = seedPricingService('Per Session', 250.00);

    $this->actingAs($seed['buyer'])
        ->postJson(route('service-requests.store'), [
            'student_service_id' => $seed['service_id'],
            'selected_dates' => now()->nextWeekday()->toDateString(),
            'start_time' => '16:00',
            'end_time' => '18:00',
            'selected_package' => 'basic',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Choose a time within the provider\'s working hours.');
});

it('rejects a booking on a disabled day', function () {
    $seed = seedPricingService('Per Session', 250.00);

    $this->actingAs($seed['buyer'])
        ->postJson(route('service-requests.store'), [
            'student_service_id' => $seed['service_id'],
            'selected_dates' => now()->next('Saturday')->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'selected_package' => 'basic',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'The provider is not available on the selected day.');
});

it('rejects a start time that does not follow the providers booking interval', function () {
    $seed = seedPricingService('Per Hour', 150.00);

    $this->actingAs($seed['buyer'])
        ->postJson(route('service-requests.store'), [
            'student_service_id' => $seed['service_id'],
            'selected_dates' => now()->nextWeekday()->toDateString(),
            'start_time' => '09:30',
            'end_time' => '11:30',
            'selected_package' => 'basic',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Choose one of the available start times.');
});
