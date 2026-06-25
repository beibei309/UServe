<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

if (!function_exists('seedMinimalServiceFlow')) {
    function seedMinimalServiceFlow(): array
    {
        $now = now();

        $helperId = DB::table('h2u_users')->insertGetId([
            'hu_name' => 'Seeder Helper',
            'hu_email' => 'seeder-helper@example.com',
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
            'hss_matric_no' => 'D2026999999',
            'hss_semester' => '1',
            'hss_status' => 'Active',
            'hss_effective_date' => $now->toDateString(),
            'hss_graduation_date' => $now->copy()->addYear()->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $communityId = DB::table('h2u_users')->insertGetId([
            'hu_name' => 'Seeder Community',
            'hu_email' => 'seeder-community@example.com',
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
            'hc_name' => 'Seeder Category',
            'hc_slug' => 'seeder-category',
            'hc_description' => 'Seeder category',
            'hc_color' => '#4f46e5',
            'hc_is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'hc_id');

        $serviceId = DB::table('h2u_student_services')->insertGetId([
            'hss_user_id' => $helperId,
            'hss_category_id' => $categoryId,
            'hss_title' => 'Seeder Service',
            'hss_description' => 'Seeder service description',
            'hss_suggested_price' => 50.00,
            'hss_status' => 'available',
            'hss_booking_mode' => 'task',
            'hss_is_active' => true,
            'hss_approval_status' => 'approved',
            'created_at' => $now,
            'updated_at' => $now,
        ], 'hss_id');

        DB::table('h2u_service_requests')->insert([
            'hsr_student_service_id' => $serviceId,
            'hsr_requester_id' => $communityId,
            'hsr_provider_id' => $helperId,
            'hsr_status' => 'completed',
            'hsr_message' => 'Seeder request for tests',
            'hsr_offered_price' => 50.00,
            'hsr_selected_dates' => json_encode([$now->toDateString()]),
            'hsr_start_time' => '09:00:00',
            'hsr_end_time' => '10:00:00',
            'hsr_selected_package' => json_encode(['tier' => 'basic', 'price' => 50.00]),
            'hsr_payment_status' => 'paid',
            'hsr_accepted_at' => $now->copy()->subDays(2),
            'hsr_started_at' => $now->copy()->subDays(1),
            'hsr_finished_at' => $now,
            'hsr_completed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return ['helperId' => $helperId, 'communityId' => $communityId, 'serviceId' => $serviceId];
    }
}
