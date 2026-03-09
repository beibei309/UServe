<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rewards = [
            [
                'hr_title' => '10% Service Discount',
                'hr_description' => 'Get 10% off your next service booking',
                'hr_type' => 'discount',
                'hr_points_cost' => 2,
                'hr_value' => 10.00,
                'hr_code_prefix' => 'DISC10',
                'hr_usage_limit' => null, // Unlimited
                'hr_user_limit' => 3, // Each user can use 3 times
                'hr_is_active' => true,
                'hr_expires_at' => null,
                'hr_terms' => [
                    'Valid for 30 days from redemption',
                    'Cannot be combined with other offers',
                    'Minimum service value: RM 20'
                ]
            ],
            [
                'hr_title' => 'RM 5 Service Credit',
                'hr_description' => 'RM 5 credit towards any service',
                'hr_type' => 'service_credit',
                'hr_points_cost' => 3,
                'hr_value' => 5.00,
                'hr_code_prefix' => 'CREDIT5',
                'hr_usage_limit' => null,
                'hr_user_limit' => 2,
                'hr_is_active' => true,
                'hr_expires_at' => null,
                'hr_terms' => [
                    'Valid for 60 days from redemption',
                    'No minimum service value required',
                    'Can be combined with discounts'
                ]
            ],
            [
                'hr_title' => '15% Service Discount',
                'hr_description' => 'Get 15% off your next service booking',
                'hr_type' => 'discount',
                'hr_points_cost' => 4,
                'hr_value' => 15.00,
                'hr_code_prefix' => 'DISC15',
                'hr_usage_limit' => null,
                'hr_user_limit' => 2,
                'hr_is_active' => true,
                'hr_expires_at' => null,
                'hr_terms' => [
                    'Valid for 30 days from redemption',
                    'Cannot be combined with other offers',
                    'Minimum service value: RM 30'
                ]
            ],
            [
                'hr_title' => 'Free Priority Support',
                'hr_description' => 'Get priority customer support for urgent requests',
                'hr_type' => 'voucher',
                'hr_points_cost' => 5,
                'hr_value' => 0.00,
                'hr_code_prefix' => 'PRIORITY',
                'hr_usage_limit' => null,
                'hr_user_limit' => 1,
                'hr_is_active' => true,
                'hr_expires_at' => null,
                'hr_terms' => [
                    'Valid for 90 days from redemption',
                    'Includes faster response times',
                    'Available during business hours only'
                ]
            ],
            [
                'hr_title' => 'RM 10 Service Credit',
                'hr_description' => 'RM 10 credit towards any service',
                'hr_type' => 'service_credit',
                'hr_points_cost' => 6,
                'hr_value' => 10.00,
                'hr_code_prefix' => 'CREDIT10',
                'hr_usage_limit' => null,
                'hr_user_limit' => 1,
                'hr_is_active' => true,
                'hr_expires_at' => null,
                'hr_terms' => [
                    'Valid for 60 days from redemption',
                    'No minimum service value required',
                    'Can be combined with discounts'
                ]
            ]
        ];

        foreach ($rewards as $rewardData) {
            Reward::create($rewardData);
        }
    }
}