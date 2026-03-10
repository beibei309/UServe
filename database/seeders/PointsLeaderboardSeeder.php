<?php

namespace Database\Seeders;

use App\Models\BuyerPoint;
use App\Models\SellerPoint;
use App\Models\User;
use Illuminate\Database\Seeder;

class PointsLeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        $helpers = User::where('hu_role', 'helper')->take(5)->get();
        $buyers = User::whereIn('hu_role', ['community', 'student', 'helper'])->take(8)->get();

        if ($helpers->isNotEmpty()) {
            foreach ($helpers as $index => $helper) {
                SellerPoint::create([
                    'hsp_user_id' => $helper->hu_id,
                    'hsp_service_request_id' => null,
                    'hsp_points_earned' => max(1, 15 - ($index * 2)),
                    'hsp_status' => 'earned',
                    'hsp_description' => 'Seeder leaderboard seller points',
                ]);
            }
        }

        if ($buyers->isNotEmpty()) {
            foreach ($buyers as $index => $buyer) {
                BuyerPoint::create([
                    'hbp_user_id' => $buyer->hu_id,
                    'hbp_service_request_id' => null,
                    'hbp_points_earned' => max(1, 12 - $index),
                    'hbp_status' => 'earned',
                    'hbp_description' => 'Seeder leaderboard buyer points',
                ]);
            }
        }
    }
}
