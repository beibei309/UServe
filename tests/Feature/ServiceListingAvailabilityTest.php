<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function createServiceListingFixture(string $title, string $serviceStatus = 'available', bool $sellerAvailable = true): array
{
    $now = now();
    static $fixtureNumber = 0;
    $fixtureNumber++;

    $helperId = DB::table('h2u_users')->insertGetId([
        'hu_name' => $title.' Helper',
        'hu_email' => str($title)->slug().'-helper@example.com',
        'hu_password' => Hash::make('password'),
        'hu_role' => 'helper',
        'hu_verification_status' => 'approved',
        'hu_public_verified_at' => $now,
        'hu_helper_verified_at' => $now,
        'hu_email_verified_at' => $now,
        'hu_is_available' => $sellerAvailable,
        'hu_is_suspended' => false,
        'hu_is_blacklisted' => false,
        'hu_is_blocked' => false,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hu_id');

    $categoryId = DB::table('h2u_categories')->insertGetId([
        'hc_name' => 'Availability Test Category '.$fixtureNumber,
        'hc_slug' => 'availability-test-category-'.$fixtureNumber,
        'hc_description' => 'Test category',
        'hc_color' => '#4f46e5',
        'hc_is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hc_id');

    $serviceId = DB::table('h2u_student_services')->insertGetId([
        'hss_user_id' => $helperId,
        'hss_category_id' => $categoryId,
        'hss_title' => $title,
        'hss_description' => $title.' description',
        'hss_suggested_price' => 25.00,
        'hss_basic_price' => 25.00,
        'hss_basic_duration' => '1 hour',
        'hss_basic_frequency' => 'Per Session',
        'hss_status' => $serviceStatus,
        'hss_booking_mode' => 'task',
        'hss_is_active' => true,
        'hss_approval_status' => 'approved',
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hss_id');

    return ['helperId' => $helperId, 'serviceId' => $serviceId];
}

it('defaults find services to bookable available services only', function () {
    createServiceListingFixture('Bookable Available Service', 'available', true);
    createServiceListingFixture('Service Status Unavailable', 'unavailable', true);
    createServiceListingFixture('Seller Account Unavailable', 'available', false);

    $response = $this->get('/services');

    $response->assertOk();
    $response->assertSee('Bookable Available Service');
    $response->assertDontSee('Service Status Unavailable');
    $response->assertDontSee('Seller Account Unavailable');
});

it('shows separate unavailable reasons when all statuses are selected', function () {
    createServiceListingFixture('Bookable Available Service', 'available', true);
    createServiceListingFixture('Service Status Unavailable', 'unavailable', true);
    createServiceListingFixture('Seller Account Unavailable', 'available', false);

    $response = $this->get('/services?available_only=');

    $response->assertOk();
    $response->assertSee('Bookable Available Service');
    $response->assertSee('Service Status Unavailable');
    $response->assertSee('Seller Account Unavailable');
    $response->assertSee('Service Unavailable');
    $response->assertSee('Seller Not Accepting Orders');
});

it('shows clear service availability on student profile service cards', function () {
    $available = createServiceListingFixture('Profile Available Service', 'available', true);
    createServiceListingFixture('Profile Service Unavailable', 'unavailable', true);
    $sellerUnavailable = createServiceListingFixture('Profile Seller Unavailable', 'available', false);

    $availableProfile = $this->get('/students/'.$available['helperId'].'/profile');
    $availableProfile->assertOk();
    $availableProfile->assertSee('Profile Available Service');
    $availableProfile->assertSee('Available');

    $sellerUnavailableProfile = $this->get('/students/'.$sellerUnavailable['helperId'].'/profile');
    $sellerUnavailableProfile->assertOk();
    $sellerUnavailableProfile->assertSee('Profile Seller Unavailable');
    $sellerUnavailableProfile->assertSee('Seller Not Accepting Orders');

    $serviceUnavailableHelperId = DB::table('h2u_student_services')
        ->where('hss_title', 'Profile Service Unavailable')
        ->value('hss_user_id');

    $serviceUnavailableProfile = $this->get('/students/'.$serviceUnavailableHelperId.'/profile');
    $serviceUnavailableProfile->assertOk();
    $serviceUnavailableProfile->assertSee('Profile Service Unavailable');
    $serviceUnavailableProfile->assertSee('Service Unavailable');
});
