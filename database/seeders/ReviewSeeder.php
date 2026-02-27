<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        $users = DB::table('h2u_users')->pluck('hu_id')->toArray();
=======
        $users = DB::table('users')->pluck('id')->toArray();
>>>>>>> aaa8c3c (Refactor tables and columns name)

        if (count($users) < 2) {
            $ts = now();
            for ($i = count($users); $i < 2; $i++) {
<<<<<<< HEAD
                DB::table('h2u_users')->insert([
                    'hu_name' => 'Reviewer Tester ' . ($i + 1),
                    'hu_email' => "reviewer{$i}@test.com",
                    'hu_password' => bcrypt('password'),
                    'hu_role' => 'community',
                    'hu_verification_status' => 'approved',
=======
                DB::table('users')->insert([
                    'name' => 'Reviewer Tester ' . ($i + 1),
                    'email' => "reviewer{$i}@test.com",
                    'password' => bcrypt('password'),
                    'role' => 'community',
                    'verification_status' => 'approved',
>>>>>>> aaa8c3c (Refactor tables and columns name)
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            }
<<<<<<< HEAD
            $users = DB::table('h2u_users')->pluck('hu_id')->toArray();
        }

        if (!Schema::hasTable('h2u_student_services')) {
            return;
        }

        $serviceId = DB::table('h2u_student_services')->value('hss_id');
=======
            $users = DB::table('users')->pluck('id')->toArray();
        }

        if (!Schema::hasTable('student_services')) {
            return;
        }

        $serviceId = DB::table('student_services')->value('id');
>>>>>>> aaa8c3c (Refactor tables and columns name)
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
<<<<<<< HEAD
                'hr_reviewer_id' => $reviewerId,
                'hr_reviewee_id' => $revieweeId,
                'hr_student_service_id' => $serviceId,
                'hr_service_request_id' => null,
                'hr_rating' => $data['rating'],
                'hr_comment' => $data['comment'],
                'hr_reply' => null,
                'hr_replied_at' => null,
=======
                'reviewer_id' => $reviewerId,
                'reviewee_id' => $revieweeId,
                'student_service_id' => $serviceId,
                'service_request_id' => null,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'reply' => null,
                'replied_at' => null,
>>>>>>> aaa8c3c (Refactor tables and columns name)
                'created_at' => now(),
                'updated_at' => now(),
            ];

<<<<<<< HEAD
            DB::table('h2u_reviews')->insertOrIgnore($insertData);
=======
            DB::table('reviews')->insertOrIgnore($insertData);
>>>>>>> aaa8c3c (Refactor tables and columns name)
        }
    }
}