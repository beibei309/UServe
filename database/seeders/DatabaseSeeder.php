<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\StudentService;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private bool $studentServiceHasPriceRange = false;
    private bool $reviewsHasServiceRequestId = false;

    public function run(): void
    {
        DB::transaction(function () {
            $this->resetTables();

            $this->studentServiceHasPriceRange = Schema::hasColumn('h2u_student_services', 'hss_price_range');
            $this->reviewsHasServiceRequestId = Schema::hasColumn('h2u_reviews', 'hr_service_request_id');

            $this->call([
                AboutSeeder::class,
                FaqSeeder::class,
            ]);

            $categories = $this->seedCategories();
            $communityUser = $this->seedCommunityUser();
            $services = $this->seedHelpersWithServices($categories);

            $this->seedSampleRequestsAndReviews($communityUser, $services);

            $this->call([
                RequestSeeder::class,
                ReviewSeeder::class,
                RewardSeeder::class,
                PointsLeaderboardSeeder::class,
            ]);

            $this->call(IntegrationSnapshotSeeder::class);

            $this->call(AdminSeeder::class);
        });
    }

    private function resetTables(): void
    {
        $tables = [
            'h2u_reward_redemptions',
            'h2u_buyer_points',
            'h2u_seller_points',
            'h2u_rewards',
            'h2u_reviews',
            'h2u_service_requests',
            'h2u_student_services',
            'h2u_categories',
            'h2u_users',
        ];

        $existingTables = array_values(array_filter($tables, fn (string $table) => Schema::hasTable($table)));
        if (empty($existingTables)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            $quotedTables = array_map(fn (string $table) => '"'.$table.'"', $existingTables);
            DB::statement('TRUNCATE TABLE '.implode(', ', $quotedTables).' RESTART IDENTITY CASCADE');
            return;
        }

        foreach ($existingTables as $table) {
            DB::table($table)->delete();
        }
    }

    private function seedCommunityUser(): User
    {
        return User::create([
            'hu_name' => 'Community User',
            'hu_email' => 'community@example.com',
            'hu_password' => Hash::make('password'),
            'hu_role' => 'community',
            'hu_phone' => '0123456789',
            'hu_verification_status' => 'approved',
            'hu_public_verified_at' => now(),
            'hu_is_available' => true,
        ]);
    }

    private function seedCategories(): Collection
    {
        return collect($this->categoryDefinitions())->mapWithKeys(function ($categoryData) {
            $category = Category::create([
                'hc_name' => $categoryData['name'],
                'hc_slug' => Str::slug($categoryData['name']),
                'hc_description' => $categoryData['description'],
                'hc_image_path' => $categoryData['image_path'],
                'hc_color' => $categoryData['color'],
                'hc_is_active' => $categoryData['is_active'],
            ]);

            return [$categoryData['name'] => $category];
        });
    }

    private function seedHelpersWithServices(Collection $categories): Collection
    {
        return collect($this->helperProfiles())
            ->flatMap(function ($profile) use ($categories) {
                $helper = User::create([
                    'hu_name' => $profile['name'],
                    'hu_email' => $profile['email'],
                    'hu_password' => Hash::make('password'),
                    'hu_role' => 'helper',
                    'hu_phone' => '0123456789',
                    'hu_student_id' => $profile['student_id'],
                    'hu_staff_email' => $profile['email'],
                    'hu_verification_status' => 'approved',
                    'hu_staff_verified_at' => now(),
                    'hu_public_verified_at' => now(),
                    'hu_is_available' => true,
                ]);

                return collect($profile['services'])->map(function ($serviceData) use ($helper, $categories) {
                    $category = $categories->get($serviceData['category']);
                    $packages = $serviceData['packages'];

                    $payload = [
                        'hss_user_id' => $helper->hu_id,
                        'hss_category_id' => $category?->hc_id,
                        'hss_title' => $serviceData['title'],
                        'hss_image_path' => $serviceData['image_path'],
                        'hss_description' => $serviceData['description'],
                        'hss_suggested_price' => $packages['standard']['price'],
                        'hss_status' => 'available',
                        'hss_is_active' => true,
                        'hss_approval_status' => 'approved',
                        'hss_warning_count' => 0,
                        'hss_warning_reason' => null,
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
                    ];

                    if ($this->studentServiceHasPriceRange) {
                        $payload['hss_price_range'] = $this->formatPriceRange($packages);
                    }

                    return StudentService::create($payload);
                });
            })
            ->values();
    }

    private function seedSampleRequestsAndReviews(User $requester, Collection $services): void
    {
        if ($services->isEmpty()) {
            return;
        }

        $services->take(3)->each(function (StudentService $service, int $index) use ($requester) {
            $status = match ($index) {
                0 => 'completed',
                1 => 'in_progress',
                default => 'pending',
            };

            $request = ServiceRequest::create([
                'hsr_student_service_id' => $service->hss_id,
                'hsr_requester_id' => $requester->hu_id,
                'hsr_provider_id' => $service->hss_user_id,
                'hsr_status' => $status,
                'hsr_message' => 'Hi, I need help with this service!',
                'hsr_offered_price' => $service->hss_suggested_price,
                'hsr_payment_status' => $status === 'completed' ? 'paid' : 'unpaid',
                'hsr_selected_dates' => now()->addDays($index + 1)->toDateString(),
                'hsr_start_time' => '10:00:00',
                'hsr_end_time' => '12:00:00',
                'hsr_selected_package' => [
                    'tier' => 'standard',
                    'price' => $service->hss_standard_price,
                ],
                'hsr_accepted_at' => $status !== 'pending' ? now()->subDays(2) : null,
                'hsr_started_at' => in_array($status, ['in_progress', 'completed'], true) ? now()->subDay() : null,
                'hsr_finished_at' => $status === 'completed' ? now()->subDay() : null,
                'hsr_completed_at' => $status === 'completed' ? now()->subDay() : null,
            ]);

            if ($status === 'completed') {
                $reviewPayload = [
                    'hr_reviewer_id' => $requester->hu_id,
                    'hr_reviewee_id' => $service->hss_user_id,
                    'hr_student_service_id' => $service->hss_id,
                    'hr_rating' => 5,
                    'hr_comment' => 'Excellent service, highly recommended!'
                ];

                if ($this->reviewsHasServiceRequestId) {
                    $reviewPayload['hr_service_request_id'] = $request->hsr_id;
                }

                Review::create($reviewPayload);
            }
        });
    }

    private function categoryDefinitions(): array
    {
        return [
            ['name' => 'Academic Tutoring', 'description' => 'Help with studies and assignments', 'image_path' => 'tutor.png', 'color' => '#4F46E5', 'is_active' => true],
            ['name' => 'Programming & Tech', 'description' => 'Web development, mobile apps, and technical services', 'image_path' => 'tech.svg', 'color' => '#10B981', 'is_active' => true],
            ['name' => 'Design & Creative', 'description' => 'Graphic design, video editing, and creative services', 'image_path' => 'graphic.svg', 'color' => '#F59E0B', 'is_active' => true],
            ['name' => 'Housechores', 'description' => 'Ironing services, house cleaning, laundry helper', 'image_path' => 'cleaning.png', 'color' => '#540863', 'is_active' => true],
            ['name' => 'Event Planning', 'description' => 'Event organization and planning services', 'image_path' => 'event.png', 'color' => '#4FB7B3', 'is_active' => true],
            ['name' => 'Runner & Errands', 'description' => 'Pickup parcel, help buy personal things', 'image_path' => 'runner.png', 'color' => '#EC4899', 'is_active' => true],
        ];
    }

    private function helperProfiles(): array
    {
        return [
            [
                'name' => 'Ahmad Rahman',
                'email' => 'ahmad@siswa.upsi.edu.my',
                'student_id' => 'D20221109111',
                'services' => [
                    [
                        'title' => 'Mathematics Tutoring',
                        'image_path' => 'service_tutor.jpg',
                        'description' => 'Expert help in calculus, algebra, and statistics.',
                        'category' => 'Academic Tutoring',
                        'packages' => [
                            'basic' => [
                                'duration' => '1',
                                'frequency' => 'One Session',
                                'price' => 25.00,
                                'description' => 'Quick session focusing on 1-2 difficult topics.'
                            ],
                            'standard' => [
                                'duration' => '3',
                                'frequency' => 'One Session',
                                'price' => 70.00,
                                'description' => 'In-depth study session including practice exercises.'
                            ],
                            'premium' => [
                                'duration' => '4',
                                'frequency' => 'Weekly',
                                'price' => 250.00,
                                'description' => 'Intensive guidance for a month leading up to the final exam.'
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@siswa.upsi.edu.my',
                'student_id' => 'D20221109112',
                'services' => [
                    [
                        'title' => 'Web Development (Laravel/React)',
                        'image_path' => 'programming_service.jpg',
                        'description' => 'Full-stack web development services using Laravel and React.',
                        'category' => 'Programming & Tech',
                        'packages' => [
                            'basic' => [
                                'duration' => '3',
                                'frequency' => 'Small Project',
                                'price' => 150.00,
                                'description' => 'Bug fixing or small feature additions.'
                            ],
                            'standard' => [
                                'duration' => '1',
                                'frequency' => 'Simple Project',
                                'price' => 500.00,
                                'description' => 'Landing page website or full portfolio.'
                            ],
                            'premium' => [
                                'duration' => '3',
                                'frequency' => 'Complex Project',
                                'price' => 1500.00,
                                'description' => 'Complete CRUD system (e.g., simple inventory management system).'
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Lim Wei Ming',
                'email' => 'lim@siswa.upsi.edu.my',
                'student_id' => 'D20221109113',
                'services' => [
                    [
                        'title' => 'Logo & Branding Design',
                        'image_path' => 'service_planning.jpg',
                        'description' => 'Professional logo design, posters, and branding materials.',
                        'category' => 'Design & Creative',
                        'packages' => [
                            'basic' => [
                                'duration' => '2',
                                'frequency' => '1 Concept',
                                'price' => 35.00,
                                'description' => 'Simple text logo design with 2x revisions.'
                            ],
                            'standard' => [
                                'duration' => '4',
                                'frequency' => '3 Concepts',
                                'price' => 90.00,
                                'description' => 'Iconic logo with 5x revisions and source files.'
                            ],
                            'premium' => [
                                'duration' => '1',
                                'frequency' => 'Full Branding',
                                'price' => 250.00,
                                'description' => 'Logo, business cards, and brand usage guide.'
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Priya Devi',
                'email' => 'priya@siswa.upsi.edu.my',
                'student_id' => 'D20221109114',
                'services' => [
                    [
                        'title' => 'Laundry & Ironing Helper',
                        'image_path' => 'laundry_service.jpg',
                        'description' => 'Washing and ironing assistance in the campus area.',
                        'category' => 'Housechores',
                        'packages' => [
                            'basic' => [
                                'duration' => '2',
                                'frequency' => 'One Session',
                                'price' => 30.00,
                                'description' => 'Washing and folding clothes (max 10kg).'
                            ],
                            'standard' => [
                                'duration' => '3',
                                'frequency' => 'One Session',
                                'price' => 45.00,
                                'description' => 'Washing, folding, and ironing (max 10kg).'
                            ],
                            'premium' => [
                                'duration' => '3',
                                'frequency' => 'Weekly',
                                'price' => 160.00,
                                'description' => 'Weekly ironing and folding service for one month.'
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Raj Kumar',
                'email' => 'raj@siswa.upsi.edu.my',
                'student_id' => 'D20221109115',
                'services' => [
                    [
                        'title' => 'Runner & Parcel Pickup',
                        'image_path' => 'runner_service.jpg',
                        'description' => 'Help pick up parcels, buy food/items, or run errands around Tanjong Malim.',
                        'category' => 'Runner & Errands',
                        'packages' => [
                            'basic' => [
                                'duration' => '30',
                                'frequency' => '1 Location',
                                'price' => 10.00,
                                'description' => 'Parcel pickup from the nearest post office.'
                            ],
                            'standard' => [
                                'duration' => '1',
                                'frequency' => '2 Locations',
                                'price' => 25.00,
                                'description' => 'Buying food/items from 2 different locations.'
                            ],
                            'premium' => [
                                'duration' => '2',
                                'frequency' => 'Unlimited (Local)',
                                'price' => 40.00,
                                'description' => 'All local errands within a 2-hour limit.'
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function formatPriceRange(array $packages): string
    {
        $prices = array_filter([
            $packages['basic']['price'] ?? 0,
            $packages['standard']['price'] ?? 0,
            $packages['premium']['price'] ?? 0,
        ]);

        if (empty($prices)) {
            return 'RM0.00';
        }

        $min = min($prices);
        $max = max($prices);

        if ($min === $max) {
            return sprintf('RM%s', number_format($min, 2));
        }

        return sprintf('RM%s - RM%s', number_format($min, 2), number_format($max, 2));
    }
}
