<?php

use App\Services\ServicePackageDisplay;

it('shows a billing label without duplicated per text', function (string $value, string $expected) {
    expect(app(ServicePackageDisplay::class)->frequencyLabel($value))->toBe($expected);
})->with([
    ['Per Session', 'Per Session'],
    ['per Per Session', 'Per Session'],
    ['per task', 'Per Task'],
    ['', 'One-time'],
]);

it('keeps complete duration text without adding another unit', function () {
    $display = app(ServicePackageDisplay::class);

    expect($display->durationLabel('45 minutes'))->toBe('45 minutes')
        ->and($display->durationLabel('1.5 hours'))->toBe('1.5 hours')
        ->and($display->summary('1.5 hours', 'Per Session'))->toBe('1.5 hours | Per Session');
});
