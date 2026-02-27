<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->pluck('id')->toArray();

        if (count($users) < 2) {
            $ts = now();
            for ($i = count($users); $i < 2; $i++) {
                DB::table('users')->insert([
                    'name' => 'Reviewer Tester ' . ($i + 1),
                    'email' => "reviewer{$i}@test.com",
                    'password' => bcrypt('password'),
                    'role' => 'community',
                    'verification_status' => 'approved',
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            }
            $users = DB::table('users')->pluck('id')->toArray();
        }

        if (!Schema::hasTable('student_services')) {
            return;
        }

        $serviceId = DB::table('student_services')->value('id');
        if (!$serviceId) {
            return;
        }

        $reviews = [
            ['rating' => 5, 'comment' => 'Servis sangat mantap! Laju buat kerja.'],
            ['rating' => 4, 'comment' => 'Okay not bad, tapi lambat sikit reply.'],
            ['rating' => 1, 'comment' => 'Tidak memuaskan. Cancel last minute.'],
            ['rating' => 5, 'comment' => 'Terbaik boh! Recommended.'],
            ['rating' => 3, 'comment' => 'Boleh la, kena improve lagi.'],
        ];

        foreach ($reviews as $index => $data) {
            $reviewerId = $users[array_rand($users)];
            $revieweePool = array_values(array_filter($users, fn ($id) => $id !== $reviewerId));
            if (empty($revieweePool)) {
                continue;
            }
            $revieweeId = $revieweePool[array_rand($revieweePool)];

            $insertData = [
                'reviewer_id' => $reviewerId,
                'reviewee_id' => $revieweeId,
                'student_service_id' => $serviceId,
                'service_request_id' => null,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'reply' => null,
                'replied_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('reviews')->insertOrIgnore($insertData);
        }
    }
}