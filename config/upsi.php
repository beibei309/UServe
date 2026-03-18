<?php

return [
    'student_view' => env('UPSI_STUDENT_VIEW', 'home2u.h2u_student'),

    'live_refresh' => [
        'enabled' => filter_var(env('UPSI_LIVE_REFRESH_ENABLED', true), FILTER_VALIDATE_BOOL),
        'ttl_minutes' => (int) env('UPSI_REFRESH_TTL_MINUTES', 15),
    ],

    'sync' => [
        'enabled' => filter_var(env('UPSI_SYNC_ENABLED', false), FILTER_VALIDATE_BOOL),
        'time' => env('UPSI_SYNC_TIME', '02:15'),
    ],
];
