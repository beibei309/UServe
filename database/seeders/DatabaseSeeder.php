<?php

namespace Database\Seeders;

use Database\Seeders\AboutSeeder;
use Database\Seeders\FaqSeeder;
use App\Models\User;
use App\Models\Category;
use App\Models\StudentService;
use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        StudentService::query()->delete();
        User::query()->delete();
        Category::query()->delete();

        $this->call(AboutSeeder::class);
        $this->call(FaqSeeder::class);
        $this->seedCategories();
        
        User::create([
            'name' => 'Community User',
            'email' => 'community@example.com',
            'password' => Hash::make('password'),
            'role' => 'community',
            'phone' => '0123456789',
            'verification_status' => 'approved',
            'public_verified_at' => now(),
        ]);

        $studentsData = $this->getStudentData();

        foreach ($studentsData as $studentData) {
            $student = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password'),
                'role' => 'helper',
                'phone' => '0123456789',
                'student_id' => $studentData['student_id'],
                'staff_email' => $studentData['email'],
                'verification_status' => 'approved',
                'staff_verified_at' => now(), 
                'is_available' => rand(0, 1) == 1,
            ]);

            foreach ($studentData['services'] as $serviceData) {
                $category = Category::where('name', $serviceData['category'])->first();
                
                StudentService::create([
                    'user_id' => $student->id,
                    'category_id' => $category->id,
                    'title' => $serviceData['title'],
                    'image_path' => $serviceData['image_path'],
                    'description' => $serviceData['description'],
                    
                    // 👇 FIX 2: Complete Package Details Mapping
                    'basic_duration' => $serviceData['packages']['basic']['duration'],
                    'basic_frequency' => $serviceData['packages']['basic']['frequency'],
                    'basic_price' => $serviceData['packages']['basic']['price'],
                    'basic_description' => $serviceData['packages']['basic']['description'],

                    'standard_duration' => $serviceData['packages']['standard']['duration'],
                    'standard_frequency' => $serviceData['packages']['standard']['frequency'],
                    'standard_price' => $serviceData['packages']['standard']['price'],
                    'standard_description' => $serviceData['packages']['standard']['description'],
                    
                    'premium_duration' => $serviceData['packages']['premium']['duration'],
                    'premium_frequency' => $serviceData['packages']['premium']['frequency'],
                    'premium_price' => $serviceData['packages']['premium']['price'],
                    'premium_description' => $serviceData['packages']['premium']['description'],

                    'status' => 'available',
                    'is_active' => true,
                    'approval_status' => 'approved',
                ]);
            }
        }

        // admin 
        $this->call(AdminSeeder::class);
    }
    
    protected function seedCategories()
    {
        $categories = [
            ['name' => 'Academic Tutoring','slug' => 'academic-tutoring', 'description' => 'Help with studies and assignments','image_path' => 'tutor.png','color' => '#4F46E5','is_active' => true],
            ['name' => 'Programming & Tech','slug' => 'programming-tech', 'description' => 'Web development, mobile apps, and technical services','image_path' => 'tech.svg','color' => '#10B981','is_active' => true],
            ['name' => 'Design & Creative','slug' => 'design-creative', 'description' => 'Graphic design, video editing, and creative services','image_path' => 'graphic.svg','color' => '#F59E0B','is_active' => true],
            ['name' => 'Housechores','slug' => 'housechores', 'description' => 'Ironing services, house cleaning, laundry helper','image_path' => 'cleaning.png','color' => '#540863','is_active' => true],
            ['name' => 'Event Planning','slug' => 'event-planning', 'description' => 'Event organization and planning services','image_path' => 'event.png','color' => '#4FB7B3','is_active' => true],
            ['name' => 'Runner & Errands','slug' => 'runner-errands', 'description' => 'Pickup parcel, help buy personal things','image_path' => 'runner.png','color' => '#EC4899','is_active' => true],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'image_path' => $categoryData['image_path'],
                'color' => $categoryData['color'],
                'is_active' => $categoryData['is_active'],
            ]);
        }
    }

     protected function getStudentData()
    {
        return [
            [
                'name' => 'Ahmad Rahman',
                'email' => 'ahmad@siswa.upsi.edu.my',
                'student_id' => 'CD21001',
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
                ]
            ],
            // ... (Other student data remains the same structure, but I've updated the languages to English for consistency)
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@siswa.upsi.edu.my',
                'student_id' => 'CD21002',
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
                ]
            ],
            [
                'name' => 'Lim Wei Ming',
                'email' => 'lim@siswa.upsi.edu.my',
                'student_id' => 'CD21003',
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
                ]
            ],
            [
                'name' => 'Priya Devi',
                'email' => 'priya@siswa.upsi.edu.my',
                'student_id' => 'CD21004',
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
                ]
            ],
            [
                'name' => 'Raj Kumar',
                'email' => 'raj@siswa.upsi.edu.my',
                'student_id' => 'CD21005',
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
                                'description' => 'Parcel pickup from the nearest Post Office.'
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
                ]
            ],
        ];

        foreach ($students as $studentData) {
            $student = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => '0123456789',
                'student_id' => $studentData['student_id'],
                'staff_email' => $studentData['email'],
                'verification_status' => 'approved',
                'staff_verified_at' => now(),
                'is_available' => rand(0, 1) == 1, 
            ]);

            foreach ($studentData['services'] as $serviceData) {
                $category = Category::where('name', $serviceData['category'])->first();
                
                StudentService::create([
                    'user_id' => $student->id,
                    'category_id' => $category->id,
                    'title' => $serviceData['title'],
                    'image_path' => $serviceData['image_path'],
                    'description' => $serviceData['description'],
                    'suggested_price' => $serviceData['price'],
                    'is_active' => true,
                ]);
            }
        }
               $allServices = \App\Models\StudentService::all();
               $requester = $communityUser; 

               foreach ($allServices->random(3) as $service) {
                \App\Models\ServiceRequest::create([
                    'student_service_id' => $service->id,      
                    'requester_id'       => $requester->id,    
                    'provider_id'        => $service->user_id, 
                    'status'             => 'pending',
                    'message'            => 'Hi, I need help with this!',
                    'offered_price'      => $service->suggested_price,
                    'created_at'         => now(),
               ]);
            }
 
        $ahmad = User::where('email', 'ahmad@siswa.upsi.edu.my')->first();
        $siti = User::where('email', 'siti@siswa.upsi.edu.my')->first();
        $requester = $communityUser; 

        $completedRequest = \App\Models\ServiceRequest::first(); 


	if ($ahmad && $siti && $requester && $completedRequest) {

    Review::create([
        'reviewer_id' => $requester->id,
        'reviewee_id' => $ahmad->id,
        'service_request_id' => $completedRequest->id,
        'conversation_id' => null,
        'rating' => 1, 
        'comment' => 'Service was extremely poor and slow. Need improvement.',
    ]);

    Review::create([
        'reviewer_id' => $requester->id,
        'reviewee_id' => $ahmad->id,
        'service_request_id' => $completedRequest->id,
        'conversation_id' => null,
        'rating' => 1, 
        'comment' => 'Second time using, still disappointing.',
    ]);

    Review::create([
        'reviewer_id' => $requester->id,
        'reviewee_id' => $siti->id,
        'service_request_id' => $completedRequest->id,
        'conversation_id' => null,
        'rating' => 5,
        'comment' => 'Excellent service, highly recommended!',
    ]);
}

        //admin 
        $this->call(AdminSeeder::class);

    }
}