<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dapatkan sekurang-kurangnya 2 user (Satu requester, satu provider)
        $users = DB::table('users')->pluck('id')->toArray();

        // Kalau user tak cukup, kita create fake user
        while (count($users) < 2) {
            $newId = DB::table('users')->insertGetId([
                'name' => 'User ' . count($users),
                'email' => 'user' . count($users) . '@test.com',
                'password' => bcrypt('password'),
                'created_at' => now(), 'updated_at' => now()
            ]);
            $users[] = $newId;
        }

        // 2. Dapatkan satu Service (Untuk tahu siapa Provider)
        $service = DB::table('student_services')->first();

        // Kalau tak ada service langsung, create satu
        if (!$service) {
            $providerId = $users[0]; // Kita lantik user pertama jadi provider
            $serviceId = DB::table('student_services')->insertGetId([
                'user_id' => $providerId, // Pastikan table student_services guna 'user_id' untuk owner
                'title' => 'Servis Format Laptop',
                'category' => 'Tech',
                'price' => 30.00,
                'description' => 'Servis laju siap 1 jam.',
                'created_at' => now(), 'updated_at' => now()
            ]);
        } else {
            $serviceId = $service->id;
            $providerId = $service->user_id; // Ambil owner service yang sedia ada
        }

        // 3. Masukkan Fake Requests
        $statuses = ['pending', 'accepted', 'rejected', 'completed', 'cancelled'];

        foreach (range(1, 10) as $index) {
            // Pilih requester (Pastikan requester BUKAN provider yang sama)
            $potentialRequesters = array_diff($users, [$providerId]);
            
            // Kalau tak ada orang lain, terpaksa guna provider jugak (edge case)
            $requesterId = !empty($potentialRequesters) ? $potentialRequesters[array_rand($potentialRequesters)] : $providerId;

            DB::table('service_requests')->insert([
                'student_service_id' => $serviceId,
                'requester_id' => $requesterId, // Guna 'requester_id' (ikut migration kau)
                'provider_id' => $providerId,   // Guna 'provider_id' (ikut migration kau)
                'offered_price' => rand(10, 50) . '.00',
                'message' => 'Hi, saya berminat nak guna servis ni.',
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now()->subDays(rand(1, 14)),
                'updated_at' => now(),
            ]);
        }
    }
}