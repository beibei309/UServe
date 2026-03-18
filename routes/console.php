<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if ((bool) config('upsi.sync.enabled', false)) {
    Schedule::command('upsi:sync-student-status --apply')
    ->dailyAt((string) config('upsi.sync.time', '02:15'))
        ->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/upsi-student-status-sync.log'));
}
