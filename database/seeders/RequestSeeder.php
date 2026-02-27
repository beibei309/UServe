<?php

namespace Database\Seeders;

use App\Models\ServiceRequest;
use App\Models\StudentService;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class RequestSeeder extends Seeder
{
    public function run(): void
    {
        $services = StudentService::with('user')->get();

        if ($services->isEmpty()) {
            return;
        }

        $communityUsers = User::where('hu_role', 'community')->get();

        if ($communityUsers->isEmpty()) {
            $communityUsers->push(User::create([
                'hu_name' => 'Seeder Community',
                'hu_email' => 'community+tester@example.com',
                'hu_password' => Hash::make('password'),
                'hu_role' => 'community',
                'hu_phone' => '0190000000',
                'hu_verification_status' => 'approved',
                'hu_public_verified_at' => now(),
            ]));
        }

        $statusOptions = ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'];
        $paymentOptions = ['unpaid', 'paid', 'verification_status', 'dispute'];

        foreach (range(1, 10) as $index) {
            $service = $services->random();
            $requester = $communityUsers->random();

            $status = Arr::random($statusOptions);
            $paymentStatus = $status === 'completed' ? 'paid' : Arr::random($paymentOptions);

            ServiceRequest::create([
                'hsr_student_service_id' => $service->hss_id,
                'hsr_requester_id' => $requester->hu_id,
                'hsr_provider_id' => $service->hss_user_id,
                'hsr_status' => $status,
                'hsr_message' => fake()->sentence(),
                'hsr_offered_price' => $service->hss_suggested_price ?? rand(20, 100),
                'hsr_selected_dates' => [now()->addDays(rand(1, 7))->toDateString()],
                'hsr_start_time' => '09:00:00',
                'hsr_end_time' => '11:00:00',
                'hsr_selected_package' => [
                    'tier' => 'basic',
                    'price' => $service->hss_basic_price,
                ],
                'hsr_payment_status' => $paymentStatus,
                'hsr_payment_proof' => null,
                'hsr_dispute_reason' => $paymentStatus === 'dispute' ? 'Sample dispute reason for QA.' : null,
                'hsr_accepted_at' => in_array($status, ['accepted', 'in_progress', 'completed'], true) ? now()->subDays(3) : null,
                'hsr_started_at' => in_array($status, ['in_progress', 'completed'], true) ? now()->subDays(2) : null,
                'hsr_finished_at' => $status === 'completed' ? now()->subDay() : null,
                'hsr_completed_at' => $status === 'completed' ? now()->subDay() : null,
            ]);
        }
    }
}