<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Kita ambil ID user yang wujud (selain user id 1 yang biasanya admin)
        $users = DB::table('users')->where('id', '>', 1)->pluck('id')->toArray();
        
        // Kalau tak ada user lain, kita create fake user jap
        if (empty($users)) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Reviewer Tester',
                'email' => 'reviewer@test.com',
                'password' => bcrypt('password'),
            ]);
            $users = [$userId];
        }

        // Kita try cari service ID kalau ada
        $serviceId = null;
        if (Schema::hasTable('student_services')) {
            $serviceId = DB::table('student_services')->first()->id ?? null;
        }

        $reviews = [
            ['rating' => 5, 'comment' => 'Servis sangat mantap! Laju buat kerja.'],
            ['rating' => 4, 'comment' => 'Okay not bad, tapi lambat sikit reply.'],
            ['rating' => 1, 'comment' => 'Tidak memuaskan. Cancel last minute.'],
            ['rating' => 5, 'comment' => 'Terbaik boh! Recommended.'],
            ['rating' => 3, 'comment' => 'Boleh la, kena improve lagi.'],
        ];

        foreach ($reviews as $index => $data) {
            // Randomly pick user
            $randomUser = $users[array_rand($users)];

            $insertData = [
                'user_id' => $randomUser,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Kalau table review perlukan service_id, kita masukkan
            if (Schema::hasColumn('reviews', 'service_id') && $serviceId) {
                $insertData['service_id'] = $serviceId;
            }
            
            // Kalau table review perlukan reviewed_user_id (Owner servis)
            if (Schema::hasColumn('reviews', 'reviewed_user_id')) {
                $insertData['reviewed_user_id'] = 1; // Contoh: Review user ID 1
            }

            DB::table('reviews')->insertOrIgnore($insertData);
        }
    }
}