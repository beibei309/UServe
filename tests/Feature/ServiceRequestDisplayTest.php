<?php

use App\Models\ServiceRequest;
use App\Models\StudentService;
use App\Models\User;

it('shows package details and the buyer message on request details', function () {
    require_once __DIR__.'/TestSeederHelper.php';
    $seed = seedMinimalServiceFlow();

    User::query()->whereIn('hu_id', [$seed['communityId'], $seed['helperId']])->update([
        'hu_email_verified_at' => now(),
    ]);

    StudentService::findOrFail($seed['serviceId'])->update([
        'hss_basic_duration' => '1.5 hours',
        'hss_basic_frequency' => 'Per Session',
    ]);

    $request = ServiceRequest::query()->where('hsr_student_service_id', $seed['serviceId'])->firstOrFail();

    $this->actingAs(User::findOrFail($seed['communityId']))
        ->get(route('service-requests.show', $request))
        ->assertOk()
        ->assertSee('1.5 hours | Per Session')
        ->assertSee('Your Message')
        ->assertSee('Seeder request for tests');

    $this->actingAs(User::findOrFail($seed['communityId']))
        ->withSession(['view_mode' => 'buyer'])
        ->get(route('service-requests.index'))
        ->assertOk()
        ->assertSee('1.5 hours | Per Session');

    $this->actingAs(User::findOrFail($seed['helperId']))
        ->get(route('service-requests.show', $request))
        ->assertOk()
        ->assertSee('Customer Message')
        ->assertSee('Seeder request for tests');
});
