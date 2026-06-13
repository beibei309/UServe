<?php

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

function seedServiceRequestSecurityFlow(string $status = 'pending'): array
{
    $now = now();
    $suffix = uniqid('', true);

    $helperId = DB::table('h2u_users')->insertGetId([
        'hu_name' => 'Security Helper',
        'hu_email' => "security-helper-{$suffix}@example.test",
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

    DB::table('h2u_student_statuses')->insert([
        'hss_student_id' => $helperId,
        'hss_matric_no' => 'D2024999999',
        'hss_program' => 'Security Test Program',
        'hss_program_desc' => 'Security Test Program',
        'hss_status' => 'Active',
        'hss_graduation_date' => $now->copy()->addYear()->toDateString(),
        'hss_effective_date' => $now->toDateString(),
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $buyerId = DB::table('h2u_users')->insertGetId([
        'hu_name' => 'Security Buyer',
        'hu_email' => "security-buyer-{$suffix}@example.test",
        'hu_password' => Hash::make('password'),
        'hu_role' => 'community',
        'hu_verification_status' => 'approved',
        'hu_public_verified_at' => $now,
        'hu_email_verified_at' => $now,
        'hu_is_available' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hu_id');

    $otherId = DB::table('h2u_users')->insertGetId([
        'hu_name' => 'Security Other',
        'hu_email' => "security-other-{$suffix}@example.test",
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
        'hc_name' => "Security Category {$suffix}",
        'hc_slug' => "security-category-{$suffix}",
        'hc_description' => 'Security category',
        'hc_color' => '#4f46e5',
        'hc_is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hc_id');

    $serviceId = DB::table('h2u_student_services')->insertGetId([
        'hss_user_id' => $helperId,
        'hss_category_id' => $categoryId,
        'hss_title' => "Security Service {$suffix}",
        'hss_description' => 'Security service description',
        'hss_suggested_price' => 50.00,
        'hss_status' => 'available',
        'hss_booking_mode' => 'task',
        'hss_is_active' => true,
        'hss_approval_status' => 'approved',
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hss_id');

    $requestId = DB::table('h2u_service_requests')->insertGetId([
        'hsr_student_service_id' => $serviceId,
        'hsr_requester_id' => $buyerId,
        'hsr_provider_id' => $helperId,
        'hsr_status' => $status,
        'hsr_message' => 'Security request',
        'hsr_offered_price' => 50.00,
        'hsr_selected_dates' => json_encode([$now->toDateString()]),
        'hsr_start_time' => '09:00:00',
        'hsr_end_time' => '10:00:00',
        'hsr_selected_package' => json_encode(['basic']),
        'hsr_payment_status' => 'unpaid',
        'hsr_accepted_at' => in_array($status, ['accepted', 'in_progress', 'waiting_payment', 'completed'], true) ? $now->copy()->subDays(3) : null,
        'hsr_started_at' => in_array($status, ['in_progress', 'waiting_payment', 'completed'], true) ? $now->copy()->subDays(2) : null,
        'hsr_finished_at' => in_array($status, ['waiting_payment', 'completed'], true) ? $now->copy()->subDay() : null,
        'created_at' => $now,
        'updated_at' => $now,
    ], 'hsr_id');

    return [
        'helper' => User::findOrFail($helperId),
        'buyer' => User::findOrFail($buyerId),
        'other' => User::findOrFail($otherId),
        'request' => ServiceRequest::findOrFail($requestId),
    ];
}

it('blocks buyers from seller-only request actions', function () {
    $pending = seedServiceRequestSecurityFlow('pending');
    $this->actingAs($pending['buyer'])
        ->withHeader('Accept', 'application/json')
        ->post(route('service-requests.accept', $pending['request']))
        ->assertForbidden();

    $accepted = seedServiceRequestSecurityFlow('accepted');
    $this->actingAs($accepted['buyer'])
        ->withHeader('Accept', 'application/json')
        ->post(route('service-requests.mark-in-progress', $accepted['request']))
        ->assertForbidden();

    $inProgress = seedServiceRequestSecurityFlow('in_progress');
    $this->actingAs($inProgress['buyer'])
        ->withHeader('Accept', 'application/json')
        ->post(route('service-requests.mark-work-finished', $inProgress['request']))
        ->assertForbidden();

    $waitingPayment = seedServiceRequestSecurityFlow('waiting_payment');
    $this->actingAs($waitingPayment['buyer'])
        ->withHeader('Accept', 'application/json')
        ->post(route('service-requests.finalize', $waitingPayment['request']), ['outcome' => 'paid'])
        ->assertForbidden();
});

it('blocks unrelated users from marking a request as paid', function () {
    $seed = seedServiceRequestSecurityFlow('waiting_payment');

    $this->actingAs($seed['other'])
        ->withHeader('Accept', 'application/json')
        ->post(route('service-requests.mark-paid', $seed['request']->hsr_id))
        ->assertForbidden();
});

it('stores payment proofs privately and only serves them to participants', function () {
    Storage::fake('local');
    Storage::fake('public');

    $seed = seedServiceRequestSecurityFlow('waiting_payment');

    $this->actingAs($seed['buyer'])
        ->post(route('service-requests.buyer-confirm-payment', $seed['request']), [
            'payment_proof' => UploadedFile::fake()->image('receipt.jpg', 600, 400),
        ])
        ->assertRedirect();

    $seed['request']->refresh();

    expect($seed['request']->hsr_payment_proof)->toStartWith('payment_proofs/');
    Storage::disk('local')->assertExists($seed['request']->hsr_payment_proof);
    Storage::disk('public')->assertMissing($seed['request']->hsr_payment_proof);

    $this->actingAs($seed['other'])
        ->get(route('service-requests.payment-proof', $seed['request']))
        ->assertForbidden();

    $this->actingAs($seed['buyer'])
        ->get(route('service-requests.payment-proof', $seed['request']))
        ->assertOk();
});
