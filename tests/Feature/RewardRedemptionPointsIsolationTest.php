<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\actingAs;

require_once __DIR__ . '/TestSeederHelper.php';

test('helpers cannot redeem buyer rewards using seller points', function () {
    $seed = seedMinimalServiceFlow();

    $helper = User::query()->where('hu_id', $seed['helperId'])->firstOrFail();

    $serviceRequestId = (int) DB::table('h2u_service_requests')->max('hsr_id');

    // Give helper seller points, but NO buyer points
    DB::table('h2u_seller_points')->insert([
        'hsp_user_id' => $helper->hu_id,
        'hsp_service_request_id' => $serviceRequestId,
        'hsp_points_earned' => 5,
        'hsp_status' => 'earned',
        'hsp_description' => 'Test seller points',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $rewardId = DB::table('h2u_rewards')->insertGetId([
        'hr_title' => 'Test Reward',
        'hr_description' => 'Test reward description',
        'hr_type' => 'voucher',
        'hr_points_cost' => 3,
        'hr_value' => 10.00,
        'hr_code_prefix' => 'TEST',
        'hr_usage_limit' => null,
        'hr_user_limit' => 1,
        'hr_is_active' => true,
        'hr_expires_at' => null,
        'hr_terms' => json_encode([]),
        'created_at' => now(),
        'updated_at' => now(),
    ], 'hr_id');

    $response = actingAs($helper)
        ->from('/points/buyer-dashboard')
        ->post(route('points.rewards.redeem'), ['reward_id' => $rewardId]);

    $response->assertStatus(302);
    $response->assertSessionHas('error');

    expect(DB::table('h2u_reward_redemptions')->where('hrr_user_id', $helper->hu_id)->count())->toBe(0);
    expect(DB::table('h2u_buyer_points')->where('hbp_user_id', $helper->hu_id)->count())->toBe(0);
});
