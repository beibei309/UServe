<?php

use Database\Seeders\PointsLeaderboardSeeder;
use Database\Seeders\ReviewSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

require_once __DIR__ . '/TestSeederHelper.php';

test('review seeder creates request-linked reviews from completed requests', function () {
    seedMinimalServiceFlow();
    Artisan::call('db:seed', ['--class' => ReviewSeeder::class]);

    $reviewCount = DB::table('h2u_reviews')
        ->whereNotNull('hr_service_request_id')
        ->whereNotNull('hr_student_service_id')
        ->count();

    expect($reviewCount)->toBeGreaterThan(0);
});

test('points leaderboard seeder creates seller and buyer point entries', function () {
    seedMinimalServiceFlow();
    Artisan::call('db:seed', ['--class' => PointsLeaderboardSeeder::class]);

    expect(DB::table('h2u_seller_points')->count())->toBeGreaterThan(0);
    expect(DB::table('h2u_buyer_points')->count())->toBeGreaterThan(0);
});
