<?php

use App\Services\ServicePackageDuration;

it('converts plain appointment durations to minutes', function (string $duration, int $minutes) {
    expect(app(ServicePackageDuration::class)->toMinutes($duration))->toBe($minutes);
})->with([
    ['30 minutes', 30],
    ['1 hour', 60],
    ['2 hours', 120],
    ['1.5 hours', 90],
    ['2h 30m', 150],
]);

it('does not treat day based work as an appointment duration', function () {
    expect(app(ServicePackageDuration::class)->toMinutes('2 days'))->toBeNull();
});

it('checks whether an appointment fits at least one available day', function () {
    $schedule = [
        'mon' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
        'tue' => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'],
    ];

    $duration = app(ServicePackageDuration::class);

    expect($duration->fitsAvailableDay('6 hours', $schedule))->toBeTrue()
        ->and($duration->fitsAvailableDay('10 hours', $schedule))->toBeFalse();
});
