<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\StudentService;
use App\Models\StudentStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TesterSeeder extends Seeder
{
    private const PASSWORD = 'Password123!';

    public function run(): void
    {
        $this->seedCommunityTesters();
        $this->seedStudentHelpers();
        $this->seedStudentBuyers();
    }

    private function seedCommunityTesters(): void
    {
        foreach ($this->communityTesters() as $tester) {
            User::query()->updateOrCreate(
                ['hu_email' => $tester['email']],
                [
                    'hu_name' => $tester['name'],
                    'hu_password' => Hash::make(self::PASSWORD),
                    'hu_role' => 'community',
                    'hu_phone' => $tester['phone'],
                    'hu_verification_status' => 'approved',
                    'hu_public_verified_at' => now(),
                    'hu_email_verified_at' => now(),
                    'hu_is_available' => true,
                    'address' => $tester['address'],
                    'hu_latitude' => $tester['latitude'],
                    'hu_longitude' => $tester['longitude'],
                    'hu_location_verified_at' => now(),
                ]
            );
        }
    }

    private function seedStudentBuyers(): void
    {
        foreach ($this->studentBuyers() as $student) {
            $user = User::query()->updateOrCreate(
                ['hu_email' => $student['email']],
                [
                    'hu_name' => $student['name'],
                    'hu_password' => Hash::make(self::PASSWORD),
                    'hu_role' => 'student',
                    'hu_phone' => $student['phone'],
                    'hu_student_id' => $student['student_id'],
                    'hu_staff_email' => $student['email'],
                    'hu_verification_status' => 'approved',
                    'hu_public_verified_at' => now(),
                    'hu_staff_verified_at' => now(),
                    'hu_email_verified_at' => now(),
                    'hu_is_available' => true,
                    'hu_faculty' => $student['faculty'],
                    'hu_course' => $student['course'],
                    'hu_bio' => $student['bio'],
                ]
            );

            $this->seedStatusFor($user, $student);
        }
    }

    private function seedStudentHelpers(): void
    {
        foreach ($this->studentHelpers() as $student) {
            $user = User::query()->updateOrCreate(
                ['hu_email' => $student['email']],
                [
                    'hu_name' => $student['name'],
                    'hu_password' => Hash::make(self::PASSWORD),
                    'hu_role' => 'helper',
                    'hu_phone' => $student['phone'],
                    'hu_student_id' => $student['student_id'],
                    'hu_staff_email' => $student['email'],
                    'hu_verification_status' => 'approved',
                    'hu_public_verified_at' => now(),
                    'hu_staff_verified_at' => now(),
                    'hu_helper_verified_at' => now(),
                    'hu_email_verified_at' => now(),
                    'helper_status' => true,
                    'hu_is_available' => true,
                    'hu_faculty' => $student['faculty'],
                    'hu_course' => $student['course'],
                    'skills' => $student['skills'],
                    'hu_bio' => $student['bio'],
                    'hu_work_experience_message' => $student['experience'],
                ]
            );

            $this->seedStatusFor($user, $student);

            foreach ($student['services'] as $service) {
                $this->seedService($user, $service);
            }
        }
    }

    private function seedStatusFor(User $user, array $student): void
    {
        if (! Schema::hasTable('h2u_student_statuses')) {
            return;
        }

        StudentStatus::query()->updateOrCreate(
            ['hss_student_id' => $user->hu_id],
            [
                'hss_matric_no' => $student['student_id'],
                'hss_program' => $student['course'],
                'hss_program_desc' => $student['course'],
                'hss_source_student_name' => $student['name'],
                'hss_source_email' => $student['email'],
                'hss_source_status_desc' => 'Aktif',
                'hss_semester' => $student['semester'],
                'hss_status' => 'Active',
                'hss_graduation_date' => now()->addYears(2)->toDateString(),
                'hss_effective_date' => now()->toDateString(),
            ]
        );
    }

    private function seedService(User $user, array $service): void
    {
        $category = Category::query()
            ->where('hc_slug', Str::slug($service['category']))
            ->orWhere('hc_name', $service['category'])
            ->first();

        $packages = $service['packages'];

        StudentService::query()->updateOrCreate(
            [
                'hss_user_id' => $user->hu_id,
                'hss_title' => $service['title'],
            ],
            [
                'hss_category_id' => $category?->hc_id,
                'hss_image_path' => $service['image_path'],
                'hss_description' => $service['description'],
                'hss_status' => 'available',
                'hss_is_active' => true,
                'hss_approval_status' => 'approved',
                'hss_warning_count' => 0,
                'hss_warning_reason' => null,
                'hss_suggested_price' => $packages['standard']['price'],
                'hss_price_range' => $this->priceRange($packages),
                'hss_booking_mode' => $service['booking_mode'],
                'hss_session_duration' => $service['booking_mode'] === 'scheduled'
                    ? $service['start_time_gap']
                    : null,
                'hss_operating_hours' => [
                    'mon' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'tue' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'wed' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'thu' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'fri' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
                    'sat' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
                    'sun' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
                ],
                'hss_basic_duration' => $packages['basic']['duration'],
                'hss_basic_frequency' => $packages['basic']['frequency'],
                'hss_basic_price' => $packages['basic']['price'],
                'hss_basic_description' => $packages['basic']['description'],
                'hss_standard_duration' => $packages['standard']['duration'],
                'hss_standard_frequency' => $packages['standard']['frequency'],
                'hss_standard_price' => $packages['standard']['price'],
                'hss_standard_description' => $packages['standard']['description'],
                'hss_premium_duration' => $packages['premium']['duration'],
                'hss_premium_frequency' => $packages['premium']['frequency'],
                'hss_premium_price' => $packages['premium']['price'],
                'hss_premium_description' => $packages['premium']['description'],
            ]
        );
    }

    private function priceRange(array $packages): string
    {
        $prices = collect($packages)->pluck('price')->filter()->sort()->values();

        if ($prices->isEmpty()) {
            return 'Price negotiable';
        }

        return 'RM '.$prices->first().' - RM '.$prices->last();
    }

    private function communityTesters(): array
    {
        return [
            [
                'name' => 'UPSI Staff Tester',
                'email' => 'staff.tester@upsi2u.test',
                'phone' => '01110002001',
                'address' => 'Universiti Pendidikan Sultan Idris, Tanjong Malim',
                'latitude' => 3.6856,
                'longitude' => 101.5244,
            ],
            [
                'name' => 'Community Tester Tanjong Malim',
                'email' => 'community.tester@upsi2u.test',
                'phone' => '01110002002',
                'address' => 'Taman Universiti, Tanjong Malim',
                'latitude' => 3.6808,
                'longitude' => 101.5189,
            ],
        ];
    }

    private function studentBuyers(): array
    {
        return [
            [
                'name' => 'Student Buyer Tester',
                'email' => 'student.buyer@upsi2u.test',
                'phone' => '01110002100',
                'student_id' => 'D2023999900',
                'faculty' => 'Fakulti Komputeran dan Meta-Teknologi',
                'course' => 'Software Engineering',
                'semester' => 'Semester 4',
                'bio' => 'UPSI student tester account for browsing and requesting services.',
            ],
        ];
    }

    private function studentHelpers(): array
    {
        return [
            [
                'name' => 'Aina Sofea Tester',
                'email' => 'student.helper.design@upsi2u.test',
                'phone' => '01110002101',
                'student_id' => 'D2023999901',
                'faculty' => 'Fakulti Seni, Kelestarian dan Industri Kreatif',
                'course' => 'Bachelor of Design',
                'semester' => 'Semester 5',
                'skills' => 'Poster Design, Canva, Branding, Social Media Content',
                'bio' => 'Creative student helper focused on event posters, club promotions, and clean visual content for UPSI activities.',
                'experience' => 'Designed posters and digital announcements for student society programmes, faculty talks, and small business promotions.',
                'services' => [
                    [
                        'category' => 'Design & Creative',
                        'title' => 'Poster Design for Clubs, Events and Small Businesses',
                        'booking_mode' => 'task',
                        'start_time_gap' => null,
                        'image_path' => 'images/demo-services/poster-design.svg',
                        'description' => 'I design clear and attractive posters for UPSI club events, faculty talks, Instagram announcements, product promotions and community activities. The design can follow your colour theme, include UPSI-style formal layouts, or use a more modern social media style. Please prepare your event title, date, venue, logo, required text and preferred reference style before booking.',
                        'packages' => [
                            'basic' => [
                                'duration' => '2 Days',
                                'frequency' => 'Per Task',
                                'price' => 25.00,
                                'description' => 'One simple digital poster with one revision. Suitable for class announcements or small activities.',
                            ],
                            'standard' => [
                                'duration' => '3 Days',
                                'frequency' => 'Per Task',
                                'price' => 45.00,
                                'description' => 'One event poster plus one square Instagram version with up to two revisions.',
                            ],
                            'premium' => [
                                'duration' => '5 Days',
                                'frequency' => 'Per Task',
                                'price' => 85.00,
                                'description' => 'Poster, Instagram post, story layout and simple caption support for a complete event campaign.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Daniel Hakim Tester',
                'email' => 'student.helper.tutor@upsi2u.test',
                'phone' => '01110002102',
                'student_id' => 'D2023999902',
                'faculty' => 'Fakulti Sains dan Matematik',
                'course' => 'Mathematics Education',
                'semester' => 'Semester 6',
                'skills' => 'Mathematics, Statistics, SPSS Basics, Assignment Guidance',
                'bio' => 'Friendly tutor for students who need patient explanation in mathematics and basic statistics.',
                'experience' => 'Peer mentor for quantitative subjects and small-group revision sessions before quizzes and final exams.',
                'services' => [
                    [
                        'category' => 'Academic Tutoring',
                        'title' => 'Mathematics and Basic Statistics Tutoring',
                        'booking_mode' => 'scheduled',
                        'start_time_gap' => 30,
                        'image_path' => 'images/demo-services/statistics-tutoring.svg',
                        'description' => 'One-to-one or small group tutoring for mathematics, quantitative methods, basic statistics and assignment problem-solving. I can help explain concepts step by step, review tutorial questions, prepare quiz revision notes and guide you through basic SPSS or spreadsheet calculations. This service is suitable for students who need a calm explanation before tests or assignments.',
                        'packages' => [
                            'basic' => [
                                'duration' => '1 Hour',
                                'frequency' => 'Per Session',
                                'price' => 20.00,
                                'description' => 'Quick explanation for one topic or a small set of tutorial questions.',
                            ],
                            'standard' => [
                                'duration' => '3 Hours',
                                'frequency' => 'Per Session',
                                'price' => 55.00,
                                'description' => 'Focused revision session with examples, exercises and short recap notes.',
                            ],
                            'premium' => [
                                'duration' => '4 Hours',
                                'frequency' => 'Per Session',
                                'price' => 180.00,
                                'description' => 'Four weekly sessions for continuous support before quizzes, assignments or final exam preparation.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Farah Nabilah Tester',
                'email' => 'student.helper.runner@upsi2u.test',
                'phone' => '01110002103',
                'student_id' => 'D2023999903',
                'faculty' => 'Fakulti Pengurusan dan Ekonomi',
                'course' => 'Business Management',
                'semester' => 'Semester 4',
                'skills' => 'Parcel Pickup, Campus Errands, Printing Collection, Basic Delivery',
                'bio' => 'Reliable campus runner for students and staff around UPSI and nearby Tanjong Malim areas.',
                'experience' => 'Helped classmates collect parcels, print documents and run small errands around campus during busy weeks.',
                'services' => [
                    [
                        'category' => 'Runner & Errands',
                        'title' => 'Parcel Pickup and Campus Errands around UPSI',
                        'booking_mode' => 'task',
                        'start_time_gap' => null,
                        'image_path' => 'images/demo-services/parcel-runner.svg',
                        'description' => 'I can help collect parcels, pick up printed documents, buy small items, send items between campus areas or assist with simple errands around UPSI and nearby Tanjong Malim locations. Please provide clear pickup/drop-off details, item size and preferred time. Large or fragile items must be discussed first.',
                        'packages' => [
                            'basic' => [
                                'duration' => 'Same Day',
                                'frequency' => 'Per Task',
                                'price' => 8.00,
                                'description' => 'One simple pickup or drop-off within nearby UPSI campus areas.',
                            ],
                            'standard' => [
                                'duration' => 'Same Day',
                                'frequency' => 'Per Task',
                                'price' => 18.00,
                                'description' => 'Multiple nearby stops such as parcel pickup plus printing collection.',
                            ],
                            'premium' => [
                                'duration' => '1 Week',
                                'frequency' => 'Per Task',
                                'price' => 55.00,
                                'description' => 'Up to five simple errands in a week by arranged schedule.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Irfan Danish Tester',
                'email' => 'student.helper.tech@upsi2u.test',
                'phone' => '01110002104',
                'student_id' => 'D2023999904',
                'faculty' => 'Fakulti Komputeran dan Meta-Teknologi',
                'course' => 'Information Technology',
                'semester' => 'Semester 5',
                'skills' => 'Laptop Formatting, Software Installation, Backup Setup, Basic Troubleshooting',
                'bio' => 'Tech support helper for students who need help with laptop setup, formatting and common software issues.',
                'experience' => 'Provided basic IT support for classmates, including formatting laptops, installing software and setting up backup folders.',
                'services' => [
                    [
                        'category' => 'Technologies',
                        'title' => 'Laptop Formatting and Basic Tech Support',
                        'booking_mode' => 'scheduled',
                        'start_time_gap' => 30,
                        'image_path' => 'images/demo-services/laptop-support.svg',
                        'description' => 'I help with laptop formatting, Windows setup, basic software installation, file backup arrangement, printer setup and simple troubleshooting. This service is intended for common student laptop issues. Hardware repair, data recovery from damaged drives or licensed paid software are not included unless discussed first.',
                        'packages' => [
                            'basic' => [
                                'duration' => '30 Minutes',
                                'frequency' => 'Per Session',
                                'price' => 10.00,
                                'description' => 'Basic check and explanation of the issue with recommended next steps.',
                            ],
                            'standard' => [
                                'duration' => '2 Hours',
                                'frequency' => 'Per Session',
                                'price' => 45.00,
                                'description' => 'Laptop formatting and basic system setup. User must prepare backup and required accounts.',
                            ],
                            'premium' => [
                                'duration' => '3 Hours',
                                'frequency' => 'Per Session',
                                'price' => 75.00,
                                'description' => 'Formatting, basic backup arrangement, essential software setup and printer/Wi-Fi check.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Mei Ling Tester',
                'email' => 'student.helper.writing@upsi2u.test',
                'phone' => '01110002105',
                'student_id' => 'D2023999905',
                'faculty' => 'Fakulti Bahasa dan Komunikasi',
                'course' => 'English Language',
                'semester' => 'Semester 6',
                'skills' => 'Proofreading, Translation, Academic Writing, Bahasa Melayu-English',
                'bio' => 'Language helper for proofreading, translation and clearer academic writing support.',
                'experience' => 'Assisted peers with proofreading reports, improving presentation scripts and translating short formal texts.',
                'services' => [
                    [
                        'category' => 'Academic Tutoring',
                        'title' => 'Proofreading, Translation and Academic Writing Support',
                        'booking_mode' => 'task',
                        'start_time_gap' => null,
                        'image_path' => 'images/demo-services/translation-writing.svg',
                        'description' => 'I help review grammar, improve sentence clarity, translate short Bahasa Melayu-English texts and polish academic reports or presentation scripts. I do not write assignments from zero, but I can help improve your existing draft while keeping your original ideas. Please provide your draft, deadline and formatting requirements.',
                        'packages' => [
                            'basic' => [
                                'duration' => '1 Day',
                                'frequency' => 'Per Task',
                                'price' => 18.00,
                                'description' => 'Grammar and clarity proofreading for a short draft or presentation script.',
                            ],
                            'standard' => [
                                'duration' => '2 Days',
                                'frequency' => 'Per Task',
                                'price' => 40.00,
                                'description' => 'Proofreading with comments, clearer phrasing and basic formatting suggestions.',
                            ],
                            'premium' => [
                                'duration' => '4 Days',
                                'frequency' => 'Per Task',
                                'price' => 85.00,
                                'description' => 'Detailed proofreading, structure comments and bilingual translation support for selected sections.',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
