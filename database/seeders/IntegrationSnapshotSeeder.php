<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class IntegrationSnapshotSeeder extends Seeder
{
    private function prefixedTable(string $table): string
    {
        return match ($table) {
            'users' => 'h2u_users',
            'categories' => 'h2u_categories',
            'student_services' => 'h2u_student_services',
            'reviews' => 'h2u_reviews',
            'student_statuses' => 'h2u_student_statuses',
            'faqs' => 'h2u_faqs',
            default => $table,
        };
    }

    private function mapColumns(string $table, array $data): array
    {
        $map = match ($table) {
            'h2u_categories' => [
                'id' => 'hc_id', 'name' => 'hc_name', 'slug' => 'hc_slug', 'description' => 'hc_description',
                'image_path' => 'hc_image_path', 'icon' => 'hc_icon', 'color' => 'hc_color', 'is_active' => 'hc_is_active',
            ],
            'h2u_users' => [
                'id' => 'hu_id', 'name' => 'hu_name', 'email' => 'hu_email', 'email_verified_at' => 'hu_email_verified_at',
                'password' => 'hu_password', 'remember_token' => 'remember_token', 'role' => 'hu_role',
                'phone' => 'hu_phone', 'student_id' => 'hu_student_id', 'profile_photo_path' => 'hu_profile_photo_path',
                'selfie_media_path' => 'hu_selfie_media_path', 'public_verified_at' => 'hu_public_verified_at',
                'verification_status' => 'hu_verification_status', 'staff_email' => 'hu_staff_email',
                'reports_count' => 'hu_reports_count', 'verification_note' => 'hu_verification_note',
                'verification_document_path' => 'hu_verification_document_path', 'staff_verified_at' => 'hu_staff_verified_at',
                'is_available' => 'hu_is_available', 'unavailable_start_date' => 'hu_unavailable_start_date',
                'unavailable_end_date' => 'hu_unavailable_end_date', 'is_suspended' => 'hu_is_suspended',
                'is_blacklisted' => 'hu_is_blacklisted', 'is_blocked' => 'hu_is_blocked',
                'warning_count' => 'hu_warning_count', 'blacklist_reason' => 'hu_blacklist_reason',
                'bio' => 'hu_bio', 'faculty' => 'hu_faculty', 'course' => 'hu_course', 'latitude' => 'hu_latitude',
                'longitude' => 'hu_longitude', 'location_verified_at' => 'hu_location_verified_at',
                'work_experience_message' => 'hu_work_experience_message', 'work_experience_file' => 'hu_work_experience_file',
                'helper_verified_at' => 'hu_helper_verified_at',
            ],
            'h2u_student_services' => [
                'id' => 'hss_id', 'user_id' => 'hss_user_id', 'category_id' => 'hss_category_id', 'title' => 'hss_title',
                'image_path' => 'hss_image_path', 'description' => 'hss_description', 'status' => 'hss_status',
                'is_active' => 'hss_is_active', 'unavailable_dates' => 'hss_unavailable_dates',
                'operating_hours' => 'hss_operating_hours', 'session_duration' => 'hss_session_duration',
                'blocked_slots' => 'hss_blocked_slots', 'booking_mode' => 'hss_booking_mode',
                'approval_status' => 'hss_approval_status', 'warning_count' => 'hss_warning_count',
                'warning_reason' => 'hss_warning_reason', 'suggested_price' => 'hss_suggested_price',
                'basic_duration' => 'hss_basic_duration', 'basic_frequency' => 'hss_basic_frequency',
                'basic_price' => 'hss_basic_price', 'basic_description' => 'hss_basic_description',
                'standard_duration' => 'hss_standard_duration', 'standard_frequency' => 'hss_standard_frequency',
                'standard_price' => 'hss_standard_price', 'standard_description' => 'hss_standard_description',
                'premium_duration' => 'hss_premium_duration', 'premium_frequency' => 'hss_premium_frequency',
                'premium_price' => 'hss_premium_price', 'premium_description' => 'hss_premium_description',
            ],
            'h2u_reviews' => [
                'id' => 'hr_id', 'service_request_id' => 'hr_service_request_id', 'student_service_id' => 'hr_student_service_id',
                'reviewer_id' => 'hr_reviewer_id', 'reviewee_id' => 'hr_reviewee_id', 'rating' => 'hr_rating',
                'reply' => 'hr_reply', 'replied_at' => 'hr_replied_at', 'comment' => 'hr_comment',
            ],
            'h2u_student_statuses' => [
                'id' => 'hss_id', 'student_id' => 'hss_student_id', 'matric_no' => 'hss_matric_no',
                'semester' => 'hss_semester', 'status' => 'hss_status', 'effective_date' => 'hss_effective_date',
                'graduation_date' => 'hss_graduation_date',
            ],
            'h2u_faqs' => [
                'id' => 'hfq_id', 'category' => 'hfq_category', 'question' => 'hfq_question',
                'answer' => 'hfq_answer', 'is_active' => 'hfq_is_active', 'display_order' => 'hfq_display_order',
            ],
            default => [],
        };

        $mapped = [];
        foreach ($data as $key => $value) {
            $mapped[$map[$key] ?? $key] = $value;
        }

        return $mapped;
    }

    private function upsertBy(string $table, array $match, array $values): void
    {
        $table = $this->prefixedTable($table);
        $match = $this->mapColumns($table, $match);
        $values = $this->mapColumns($table, $values);

        $existingColumns = Schema::getColumnListing($table);
        $match = array_intersect_key($match, array_flip($existingColumns));
        $values = array_intersect_key($values, array_flip($existingColumns));

        if (empty($match)) {
            return;
        }

        $query = DB::table($table)->where($match);

        if ($query->exists()) {
            $query->update($values);
            return;
        }

        DB::table($table)->insert(array_merge($match, $values));
    }

    public function run(): void
    {
        $now = now();

        $categories = [
            ['name' => 'Academic Tutoring', 'slug' => 'academic-tutoring', 'description' => 'Help with studies and assignments', 'color' => '#4f46e5', 'is_active' => true, 'icon' => 'fa fa-graduation-cap'],
            ['name' => 'Technologies', 'slug' => 'programming-tech', 'description' => 'Web development, mobile apps, and technical services', 'color' => '#10b981', 'is_active' => true, 'icon' => 'fa fa-laptop-code'],
            ['name' => 'Design & Creative', 'slug' => 'design-creative', 'description' => 'Graphic design, video editing, and creative services', 'color' => '#f59e0b', 'is_active' => true, 'icon' => 'fa fa-paint-brush'],
            ['name' => 'Housechores', 'slug' => 'housechores', 'description' => 'Ironing services, house cleaning, laundry helper', 'color' => '#540863', 'is_active' => true, 'icon' => 'fa fa-soap'],
            ['name' => 'Event Planning', 'slug' => 'event-planning', 'description' => 'Event organization and planning services', 'color' => '#4fb7b3', 'is_active' => true, 'icon' => 'fa fa-star'],
            ['name' => 'Runner & Errands', 'slug' => 'runner-errands', 'description' => 'Pickup parcel, help buy personal things', 'color' => '#ec4899', 'is_active' => true, 'icon' => 'fa fa-bicycle'],
        ];

        foreach ($categories as $category) {
            $this->upsertBy('categories',
                ['slug' => $category['slug']],
                array_merge($category, ['updated_at' => $now, 'created_at' => $now])
            );
        }

        $users = [
            [
                'name' => 'anafrhnh',
                'email' => 'ainafarhanah1@gmail.com',
                'role' => 'community',
                'phone' => '0147387627',
                'student_id' => null,
                'verification_status' => 'approved',
                'verification_note' => 'Open Mouth 😮',
                'verification_document_path' => 'verification_docs/verify_45_1766239096.pdf',
                'profile_photo_path' => 'profile-photos/1769194376_IMG_0691.jpeg',
                'selfie_media_path' => 'uploads/verification/selfie_45_1766239073.jpg',
                'helper_verified_at' => null,
                'is_available' => false,
                'address' => null,
                'latitude' => null,
                'longitude' => null,
                'location_verified_at' => null,
                'warning_count' => 2,
                'is_blocked' => false,
                'reports_count' => 10,
                'bio' => null,
                'faculty' => null,
                'course' => null,
            ],
            [
                'name' => 'Aina Farhanah Binti Haszdree',
                'email' => 'd20231109103@siswa.upsi.edu.my',
                'role' => 'helper',
                'phone' => '0147387627',
                'student_id' => 'D20231109103',
                'verification_status' => 'approved',
                'verification_note' => null,
                'verification_document_path' => null,
                'profile_photo_path' => 'profile-photos/1769500653_passport aina.jpg',
                'selfie_media_path' => 'uploads/verification/helper_selfie_52_1766302950.jpg',
                'helper_verified_at' => Carbon::parse('2025-12-21 07:42:30'),
                'is_available' => true,
                'address' => 'GPS: 3.7178, 101.5326',
                'latitude' => 3.71788070,
                'longitude' => 101.53263140,
                'location_verified_at' => Carbon::parse('2025-12-21 07:42:20'),
                'warning_count' => 1,
                'is_blocked' => false,
                'reports_count' => 0,
                'bio' => 'Student helper profile',
                'faculty' => 'FSS',
                'course' => 'Kejuruteraan Perisian',
            ],
            [
                'name' => 'HON YEE SHUAN',
                'email' => 'honyeeshuan0805@gmail.com',
                'role' => 'community',
                'phone' => '0124031353',
                'student_id' => null,
                'verification_status' => 'approved',
                'verification_note' => 'Cover One Eye 👁️',
                'verification_document_path' => 'verification_docs/verify_73_1769607029.png',
                'profile_photo_path' => 'profile-photos/1769604880_selfie_1_1766071838.jpg',
                'selfie_media_path' => 'uploads/verification/selfie_73_1769607011.jpg',
                'helper_verified_at' => null,
                'is_available' => false,
                'address' => 'GPS: 3.7215, 101.5199',
                'latitude' => 3.72152889,
                'longitude' => 101.51988510,
                'location_verified_at' => Carbon::parse('2026-01-23 10:41:35'),
                'warning_count' => 3,
                'is_blocked' => false,
                'reports_count' => 2,
                'bio' => null,
                'faculty' => null,
                'course' => null,
            ],
            [
                'name' => 'Aina Farhanah',
                'email' => 'ainafarhanah2201@gmail.com',
                'role' => 'community',
                'phone' => '0147387627',
                'student_id' => null,
                'verification_status' => 'approved',
                'verification_note' => 'Thumbs Up 👍',
                'verification_document_path' => 'verification_docs/verify_74_1769158006.pdf',
                'profile_photo_path' => 'profile-photos/1769157948_passport aina.jpg',
                'selfie_media_path' => 'uploads/verification/selfie_74_1769157970.jpg',
                'helper_verified_at' => null,
                'is_available' => false,
                'address' => 'GPS: 3.0263, 101.4499',
                'latitude' => 3.02629647,
                'longitude' => 101.44993557,
                'location_verified_at' => Carbon::parse('2026-01-23 16:44:47'),
                'warning_count' => 0,
                'is_blocked' => false,
                'reports_count' => 0,
                'bio' => null,
                'faculty' => null,
                'course' => null,
            ],
            [
                'name' => 'MUHAMMAD HAFIZZUDDIN',
                'email' => 'd20231108500@siswa.upsi.edu.my',
                'role' => 'helper',
                'phone' => '0198859106',
                'student_id' => 'D20231108500',
                'verification_status' => 'approved',
                'verification_note' => null,
                'verification_document_path' => null,
                'profile_photo_path' => 'profile-photos/1769737971_1708323477264.png',
                'selfie_media_path' => 'uploads/verification/helper_selfie_75_1769738081.jpg',
                'helper_verified_at' => Carbon::parse('2026-01-30 09:54:41'),
                'is_available' => true,
                'address' => 'GPS: 3.6830, 101.5258',
                'latitude' => 3.68302093,
                'longitude' => 101.52587898,
                'location_verified_at' => Carbon::parse('2026-01-30 09:52:30'),
                'warning_count' => 0,
                'is_blocked' => false,
                'reports_count' => 0,
                'bio' => 'hi...',
                'faculty' => 'FSSKJ',
                'course' => null,
            ],
            [
                'name' => 'Zuria Nabila',
                'email' => 'd20231106544@siswa.upsi.edu.my',
                'role' => 'helper',
                'phone' => '0184656108',
                'student_id' => 'D20231106544',
                'verification_status' => 'approved',
                'verification_note' => null,
                'verification_document_path' => null,
                'profile_photo_path' => 'profile-photos/1769506426_zuria.PNG',
                'selfie_media_path' => 'uploads/verification/helper_selfie_78_1769505756.jpg',
                'helper_verified_at' => Carbon::parse('2026-01-27 17:22:36'),
                'is_available' => true,
                'address' => 'GPS: 3.7220, 101.5216',
                'latitude' => 3.72202471,
                'longitude' => 101.52167901,
                'location_verified_at' => Carbon::parse('2026-01-27 17:21:41'),
                'warning_count' => 0,
                'is_blocked' => false,
                'reports_count' => 0,
                'bio' => null,
                'faculty' => 'FKMT',
                'course' => 'ISMP MULTIMEDIA',
            ],
            [
                'name' => 'Nurul Syakirah',
                'email' => 'd20231109097@siswa.upsi.edu.my',
                'role' => 'student',
                'phone' => '0125764231',
                'student_id' => 'D20231109097',
                'verification_status' => 'approved',
                'verification_note' => null,
                'verification_document_path' => null,
                'profile_photo_path' => null,
                'selfie_media_path' => null,
                'helper_verified_at' => null,
                'is_available' => true,
                'address' => null,
                'latitude' => null,
                'longitude' => null,
                'location_verified_at' => null,
                'warning_count' => 0,
                'is_blocked' => false,
                'reports_count' => 0,
                'bio' => null,
                'faculty' => null,
                'course' => null,
            ],
            [
                'name' => 'Farhanah',
                'email' => 'hannahazer2@gmail.com',
                'role' => 'community',
                'phone' => '0147387627',
                'student_id' => null,
                'verification_status' => 'approved',
                'verification_note' => 'Open Mouth 😮',
                'verification_document_path' => 'verification_docs/verify_81_1769738579.pdf',
                'profile_photo_path' => 'profile-photos/1769738518_The template picker in Figma Slides (2).png',
                'selfie_media_path' => 'uploads/verification/selfie_81_1769738543.jpg',
                'helper_verified_at' => null,
                'is_available' => false,
                'address' => 'GPS: 3.6830, 101.5259',
                'latitude' => 3.68302667,
                'longitude' => 101.52587544,
                'location_verified_at' => Carbon::parse('2026-01-30 10:01:42'),
                'warning_count' => 0,
                'is_blocked' => false,
                'reports_count' => 0,
                'bio' => null,
                'faculty' => null,
                'course' => null,
            ],
            [
                'name' => 'Muhammad Haiqal Idham Bin Diris',
                'email' => 'd20221101882@siswa.upsi.edu.my',
                'role' => 'student',
                'phone' => '0178373166',
                'student_id' => 'D20221101882',
                'verification_status' => 'approved',
                'verification_note' => null,
                'verification_document_path' => null,
                'profile_photo_path' => null,
                'selfie_media_path' => null,
                'helper_verified_at' => null,
                'is_available' => true,
                'address' => null,
                'latitude' => null,
                'longitude' => null,
                'location_verified_at' => null,
                'warning_count' => 0,
                'is_blocked' => false,
                'reports_count' => 0,
                'bio' => null,
                'faculty' => null,
                'course' => null,
            ],
            [
                'name' => 'Hon Yee Shuan',
                'email' => 'd20231109111@siswa.upsi.edu.my',
                'role' => 'student',
                'phone' => '0124031353',
                'student_id' => 'D20231109111',
                'verification_status' => 'approved',
                'verification_note' => null,
                'verification_document_path' => null,
                'profile_photo_path' => 'profile-photos/1772028466_IMG_9134.JPG',
                'selfie_media_path' => 'uploads/verification/helper_selfie_17_1772028480.jpg',
                'helper_verified_at' => null,
                'is_available' => true,
                'address' => null,
                'latitude' => null,
                'longitude' => null,
                'location_verified_at' => null,
                'warning_count' => 0,
                'is_blocked' => false,
                'reports_count' => 0,
                'bio' => null,
                'faculty' => null,
                'course' => null,
            ],
            [
                'name' => 'Hon Yee Yee',
                'email' => 'honyeeshuan0008@gmail.com',
                'role' => 'community',
                'phone' => '0124031353',
                'student_id' => null,
                'verification_status' => 'approved',
                'verification_note' => 'Cover One Eye 👁️',
                'verification_document_path' => 'verification_docs/verify_16_1772028436.PNG',
                'profile_photo_path' => 'profile-photos/0YbqjpdNmzmShjb1WxKScBT6VINscZWOZluAvWe0.jpg',
                'selfie_media_path' => 'uploads/verification/selfie_16_1772028412.jpg',
                'helper_verified_at' => null,
                'is_available' => false,
                'address' => 'GPS: 3.7237, 101.5185',
                'latitude' => 3.72372640,
                'longitude' => 101.51853320,
                'location_verified_at' => Carbon::parse('2026-01-23 10:41:35'),
                'warning_count' => 3,
                'is_blocked' => false,
                'reports_count' => 2,
                'bio' => null,
                'faculty' => null,
                'course' => null,
            ],
        ];
        
        foreach ($users as $user) {
            $this->upsertBy('users',
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'email_verified_at' => $now,
                    'password' => Hash::make('password'),
                    'remember_token' => null,
                    'role' => $user['role'],
                    'phone' => $user['phone'],
                    'student_id' => $user['student_id'],
                    'public_verified_at' => $user['verification_status'] === 'approved' ? $now : null,
                    'verification_status' => $user['verification_status'],
                    'verification_note' => $user['verification_note'],
                    'verification_document_path' => $user['verification_document_path'],
                    'profile_photo_path' => $user['profile_photo_path'],
                    'selfie_media_path' => $user['selfie_media_path'],
                    'staff_email' => null,
                    'staff_verified_at' => null,
                    'helper_verified_at' => $user['helper_verified_at'],
                    'is_available' => $user['is_available'],
                    'unavailable_start_date' => null,
                    'unavailable_end_date' => null,
                    'is_suspended' => false,
                    'is_blacklisted' => false,
                    'blacklist_reason' => null,
                    'bio' => $user['bio'],
                    'faculty' => $user['faculty'],
                    'course' => $user['course'],
                    'helper_status' => false,
                    'address' => $user['address'],
                    'latitude' => $user['latitude'],
                    'longitude' => $user['longitude'],
                    'location_verified_at' => $user['location_verified_at'],
                    'skills' => null,
                    'work_experience_message' => null,
                    'work_experience_file' => null,
                    'warning_count' => $user['warning_count'],
                    'is_blocked' => $user['is_blocked'],
                    'reports_count' => $user['reports_count'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $userIds = DB::table('h2u_users')->whereIn('hu_email', array_column($users, 'email'))->pluck('hu_id', 'hu_email');
        $categoryIds = DB::table('h2u_categories')->pluck('hc_id', 'hc_slug');

        $services = [
            [
                'user_email' => 'd20231109103@siswa.upsi.edu.my',
                'category_slug' => 'event-planning',
                'booking_mode' => 'session',
                'title' => 'Professional Emcee Service',
                'image_path' => 'emcee_service.jpg',
                'description' => '<p>Saya menawarkan servis emcee untuk majlis rasmi dan tidak rasmi.</p>',
                'status' => 'available',
                'warning_count' => 0,
                'warning_reason' => null,
                'is_active' => true,
                'approval_status' => 'approved',
                'basic_price' => 50.00,
                'basic_description' => '<p>Lite</p>',
                'standard_price' => 120.00,
                'standard_description' => '<p>Standard</p>',
                'premium_price' => 250.00,
                'premium_description' => '<p>Full</p>',
                'operating_hours' => json_encode(['mon' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00']]),
                'session_duration' => 30,
            ],
            [
                'user_email' => 'd20231109103@siswa.upsi.edu.my',
                'category_slug' => 'programming-tech',
                'booking_mode' => 'session',
                'title' => 'Website landing page setup',
                'image_path' => 'website_service.png',
                'description' => '<p>Add-on development and landing page setup.</p>',
                'status' => 'available',
                'warning_count' => 0,
                'warning_reason' => null,
                'is_active' => true,
                'approval_status' => 'approved',
                'basic_duration' => '2 days',
                'basic_frequency' => 'per request',
                'basic_price' => 80.00,
                'basic_description' => '<p>Best for students.</p>',
                'operating_hours' => json_encode(['fri' => ['enabled' => true, 'start' => '10:00', 'end' => '18:00']]),
                'session_duration' => null,
            ],
            [
                'user_email' => 'd20231106544@siswa.upsi.edu.my',
                'category_slug' => 'design-creative',
                'booking_mode' => 'session',
                'title' => 'Video Editing',
                'image_path' => 'video-edit_service.png',
                'description' => '<p>Video editing for assignments and events.</p>',
                'status' => 'available',
                'warning_count' => 0,
                'warning_reason' => null,
                'is_active' => true,
                'approval_status' => 'approved',
                'basic_duration' => '1-5 hari',
                'basic_frequency' => 'per video',
                'basic_price' => 20.00,
                'basic_description' => '<p>Editing package.</p>',
                'operating_hours' => json_encode(['mon' => ['enabled' => false]]),
                'session_duration' => null,
            ],
        ];

        foreach ($services as $service) {
            $userId = $userIds[$service['user_email']] ?? null;
            $categoryId = $categoryIds[$service['category_slug']] ?? null;

            if (!$userId || !$categoryId) {
                continue;
            }

            $this->upsertBy('student_services',
                ['user_id' => $userId, 'title' => $service['title']],
                [
                    'category_id' => $categoryId,
                    'booking_mode' => $service['booking_mode'],
                    'image_path' => $service['image_path'],
                    'description' => $service['description'],
                    'unavailable_dates' => json_encode([]),
                    'blocked_slots' => json_encode([]),
                    'status' => $service['status'],
                    'warning_count' => $service['warning_count'],
                    'warning_reason' => $service['warning_reason'],
                    'is_active' => $service['is_active'],
                    'approval_status' => $service['approval_status'],
                    'basic_duration' => $service['basic_duration'] ?? null,
                    'basic_frequency' => $service['basic_frequency'] ?? null,
                    'basic_price' => $service['basic_price'] ?? null,
                    'basic_description' => $service['basic_description'] ?? null,
                    'standard_duration' => $service['standard_duration'] ?? null,
                    'standard_frequency' => $service['standard_frequency'] ?? null,
                    'standard_price' => $service['standard_price'] ?? null,
                    'standard_description' => $service['standard_description'] ?? null,
                    'premium_duration' => $service['premium_duration'] ?? null,
                    'premium_frequency' => $service['premium_frequency'] ?? null,
                    'premium_price' => $service['premium_price'] ?? null,
                    'premium_description' => $service['premium_description'] ?? null,
                    'suggested_price' => $service['standard_price'] ?? $service['basic_price'] ?? null,
                    'operating_hours' => $service['operating_hours'],
                    'session_duration' => $service['session_duration'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $serviceIds = DB::table('h2u_student_services')
            ->whereIn('hss_title', array_column($services, 'title'))
            ->pluck('hss_id', 'hss_title');

        $reviews = [
            [
                'reviewer_email' => 'ainafarhanah1@gmail.com',
                'reviewee_email' => 'd20231109103@siswa.upsi.edu.my',
                'service_title' => 'Website landing page setup',
                'rating' => 5,
                'comment' => 'Service sangat pantas',
            ],
            [
                'reviewer_email' => 'd20231109103@siswa.upsi.edu.my',
                'reviewee_email' => 'honyeeshuan0805@gmail.com',
                'service_title' => 'Website landing page setup',
                'rating' => 4,
                'comment' => 'Nice buyer',
            ],
        ];

        foreach ($reviews as $review) {
            $reviewerId = $userIds[$review['reviewer_email']] ?? null;
            $revieweeId = $userIds[$review['reviewee_email']] ?? null;
            $studentServiceId = $serviceIds[$review['service_title']] ?? null;

            if (!$reviewerId || !$revieweeId) {
                continue;
            }

            $this->upsertBy('reviews',
                [
                    'reviewer_id' => $reviewerId,
                    'reviewee_id' => $revieweeId,
                    'student_service_id' => $studentServiceId,
                    'comment' => $review['comment'],
                ],
                [
                    'service_request_id' => null,
                    'rating' => $review['rating'],
                    'reply' => null,
                    'replied_at' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $studentStatuses = [
            ['email' => 'd20231109103@siswa.upsi.edu.my', 'matric_no' => 'D20231109103', 'semester' => 'Semester 10', 'status' => 'active', 'graduation_date' => null, 'effective_date' => '2025-12-21'],
            ['email' => 'd20221101882@siswa.upsi.edu.my', 'matric_no' => 'D20221101882', 'semester' => null, 'status' => 'active', 'graduation_date' => null, 'effective_date' => '2026-02-23'],
        ];

        foreach ($studentStatuses as $status) {
            $studentId = $userIds[$status['email']] ?? null;

            if (!$studentId) {
                continue;
            }

            $this->upsertBy('student_statuses',
                ['student_id' => $studentId],
                [
                    'matric_no' => $status['matric_no'],
                    'semester' => $status['semester'],
                    'status' => $status['status'],
                    'graduation_date' => $status['graduation_date'],
                    'effective_date' => $status['effective_date'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $faqs = [
            ['category' => 'General & Accounts', 'question' => 'Who can use U-Serve?', 'answer' => 'U-Serve can be used by students and community users in the Muallim area.', 'is_active' => true, 'display_order' => 1],
            ['category' => 'Safety & Support', 'question' => 'Why was my service banned?', 'answer' => 'Services can be banned for policy violations, repeated reports, or unsafe activity.', 'is_active' => true, 'display_order' => 1],
        ];

        foreach ($faqs as $faq) {
            $this->upsertBy('faqs',
                ['question' => $faq['question']],
                array_merge($faq, ['updated_at' => $now, 'created_at' => $now])
            );
        }
    }
}
