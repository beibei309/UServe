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

        $communityUsers = User::where('role', 'community')->get();

        if ($communityUsers->isEmpty()) {
            $communityUsers->push(User::create([
                'name' => 'Seeder Community',
                'email' => 'community+tester@example.com',
                'password' => Hash::make('password'),
                'role' => 'community',
                'phone' => '0190000000',
                'verification_status' => 'approved',
                'public_verified_at' => now(),
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
                'student_service_id' => $service->id,
                'requester_id' => $requester->id,
                'provider_id' => $service->user_id,
                'status' => $status,
                'message' => fake()->sentence(),
                'offered_price' => $service->suggested_price ?? rand(20, 100),
                'selected_dates' => now()->addDays(rand(1, 7))->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '11:00:00',
                'selected_package' => json_encode([
                    'tier' => 'basic',
                    'price' => $service->basic_price,
                ]),
                'payment_status' => $paymentStatus,
                'payment_proof' => null,
                'dispute_reason' => $paymentStatus === 'dispute' ? 'Sample dispute reason for QA.' : null,
                'accepted_at' => in_array($status, ['accepted', 'in_progress', 'completed'], true) ? now()->subDays(3) : null,
                'started_at' => in_array($status, ['in_progress', 'completed'], true) ? now()->subDays(2) : null,
                'finished_at' => $status === 'completed' ? now()->subDay() : null,
                'completed_at' => $status === 'completed' ? now()->subDay() : null,
            ]);
        }
    }
}