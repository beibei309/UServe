<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LegalPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LaunchSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AboutSeeder::class,
            FaqSeeder::class,
            PageContentSeeder::class,
            RewardSeeder::class,
            AdminSeeder::class,
        ]);

        $this->seedCategories();
        $this->seedLegalPages();
    }

    private function seedCategories(): void
    {
        if (! Schema::hasTable('h2u_categories')) {
            return;
        }

        $hasIconColumn = Schema::hasColumn('h2u_categories', 'hc_icon');

        foreach ($this->categoryDefinitions() as $categoryData) {
            $payload = [
                'hc_name' => $categoryData['name'],
                'hc_description' => $categoryData['description'],
                'hc_image_path' => $categoryData['image_path'],
                'hc_color' => $categoryData['color'],
                'hc_is_active' => true,
            ];

            if ($hasIconColumn) {
                $payload['hc_icon'] = $categoryData['icon'];
            }

            Category::query()->updateOrCreate(
                ['hc_slug' => Str::slug($categoryData['name'])],
                $payload
            );
        }
    }

    private function seedLegalPages(): void
    {
        if (! Schema::hasTable('h2u_legal_pages')) {
            return;
        }

        foreach ($this->legalPages() as $page) {
            LegalPage::query()->updateOrCreate(
                ['hlp_slug' => $page['hlp_slug']],
                $page
            );
        }
    }

    private function categoryDefinitions(): array
    {
        return [
            [
                'name' => 'Academic Tutoring',
                'description' => 'Help with studies, assignments, and subject revision.',
                'image_path' => 'tutor.png',
                'color' => '#4F46E5',
                'icon' => 'fa-solid fa-graduation-cap',
            ],
            [
                'name' => 'Technologies',
                'description' => 'Web development, mobile apps, formatting, and technical support.',
                'image_path' => 'tech.svg',
                'color' => '#10B981',
                'icon' => 'fa-solid fa-laptop-code',
            ],
            [
                'name' => 'Design & Creative',
                'description' => 'Graphic design, video editing, posters, and creative services.',
                'image_path' => 'graphic.svg',
                'color' => '#F59E0B',
                'icon' => 'fa-solid fa-paintbrush',
            ],
            [
                'name' => 'Housechores',
                'description' => 'Ironing, cleaning, laundry, and practical household help.',
                'image_path' => 'cleaning.png',
                'color' => '#540863',
                'icon' => 'fa-solid fa-broom',
            ],
            [
                'name' => 'Event Planning',
                'description' => 'Support for event setup, planning, coordination, and logistics.',
                'image_path' => 'event.png',
                'color' => '#4FB7B3',
                'icon' => 'fa-solid fa-star',
            ],
            [
                'name' => 'Runner & Errands',
                'description' => 'Parcel pickup, item delivery, queue help, and daily errands.',
                'image_path' => 'runner.png',
                'color' => '#EC4899',
                'icon' => 'fa-solid fa-motorcycle',
            ],
            [
                'name' => 'Sports & Recreation',
                'description' => 'Coaching, casual sports help, fitness sessions, and recreation support.',
                'image_path' => 'sports.png',
                'color' => '#3B82F6',
                'icon' => 'fa-solid fa-dumbbell',
            ],
        ];
    }

    private function legalPages(): array
    {
        return [
            [
                'hlp_slug' => 'terms',
                'hlp_title' => 'Terms of Service',
                'hlp_content' => '<h2>1. Acceptance of Terms</h2><p>By accessing and using UPSI2u, you agree to follow these terms and all applicable UPSI community rules.</p><h2>2. Eligibility</h2><p>UPSI2u is intended for UPSI students and verified community users. Student sellers must maintain valid student status before offering services.</p><h2>3. Services</h2><p>UPSI2u connects users with student service providers. Users are responsible for agreeing on service scope, timing, price, and expectations before work begins.</p><h2>4. Safety and Conduct</h2><p>Users must communicate respectfully, avoid unsafe or illegal services, and report suspicious activity through the platform.</p><h2>5. Payments and Disputes</h2><p>Payments are arranged between users. UPSI2u may provide request, report, and dispute tools, but users remain responsible for confirming service completion and payment details.</p><h2>6. Account Actions</h2><p>UPSI2u may restrict, suspend, or remove accounts or services that violate safety rules, receive serious reports, or misuse the platform.</p><h2>7. Contact</h2><p>For questions about these terms, contact the UPSI2u support team through the Help page.</p>',
                'hlp_is_active' => true,
            ],
            [
                'hlp_slug' => 'privacy',
                'hlp_title' => 'Privacy Policy',
                'hlp_content' => '<h2>1. Information We Collect</h2><p>UPSI2u collects account details, contact information, student identifiers, service content, request records, reports, and verification information needed to operate the platform.</p><h2>2. Student Status</h2><p>For student users, UPSI2u may read student status from the configured UPSI source view and store a local status record for platform eligibility checks.</p><h2>3. Verification Data</h2><p>Verification documents, selfies, and location checks may be stored to support community safety and later admin review.</p><h2>4. How Data Is Used</h2><p>Data is used to run service discovery, requests, profile features, reports, rewards, notifications, account verification, and platform moderation.</p><h2>5. Data Protection</h2><p>Access to sensitive information should be limited to authorized users and administrators. Production credentials and database access must be protected.</p><h2>6. Account Deletion</h2><p>Users may request account deletion or support assistance through the platform where available.</p><h2>7. Updates</h2><p>This policy may be updated as UPSI2u grows or as university requirements change.</p>',
                'hlp_is_active' => true,
            ],
        ];
    }
}
