<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('h2u_page_contents')) {
            return;
        }

        $blocks = [
            // Welcome text blocks
            ['page' => 'welcome', 'slug' => 'welcome.hero_title', 'label' => 'Hero - Main Title', 'type' => 'textarea', 'value' => "UPSI Student to Community\nWe've Got You.", 'default' => "UPSI Student to Community\nWe've Got You.", 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.hero_title_line_1', 'label' => 'Hero - Title Line 1 (Normal Color)', 'type' => 'text', 'value' => 'UPSI Student to Community', 'default' => 'UPSI Student to Community', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.hero_title_highlight', 'label' => 'Hero - Highlighted Text', 'type' => 'text', 'value' => "We've Got You.", 'default' => "We've Got You.", 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.hero_title_highlight_color', 'label' => 'Hero - Highlight Color (HEX)', 'type' => 'text', 'value' => '#818cf8', 'default' => '#818cf8', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.hero_title_line_2', 'label' => 'Hero - Title Line 2 (Optional)', 'type' => 'text', 'value' => '', 'default' => '', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.hero_subtitle', 'label' => 'Hero - Subtitle / Description', 'type' => 'textarea', 'value' => 'Connect with talented students for services ranging from academic help to creative tasks. Secure, reliable, and community-driven.', 'default' => 'Connect with talented students for services ranging from academic help to creative tasks. Secure, reliable, and community-driven.', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.features_badge', 'label' => 'Features - Badge Label', 'type' => 'text', 'value' => 'Advantages', 'default' => 'Advantages', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.features_title', 'label' => 'Features - Section Title', 'type' => 'text', 'value' => 'Why choose UPSI2u', 'default' => 'Why choose UPSI2u', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.features_subtitle', 'label' => 'Features - Section Description', 'type' => 'textarea', 'value' => 'We create a safe, reliable environment for students to connect, earn, and collaborate within the UPSI ecosystem.', 'default' => 'We create a safe, reliable environment for students to connect, earn, and collaborate within the UPSI ecosystem.', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.feature_1_title', 'label' => 'Feature 1 - Title', 'type' => 'text', 'value' => 'Verified Students', 'default' => 'Verified Students', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.feature_1_desc', 'label' => 'Feature 1 - Description', 'type' => 'textarea', 'value' => 'Safety first. Every service provider is a verified UPSI student.', 'default' => 'Safety first. Every service provider is a verified UPSI student.', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.feature_2_title', 'label' => 'Feature 2 - Title', 'type' => 'text', 'value' => 'Transparent Pricing', 'default' => 'Transparent Pricing', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.feature_2_desc', 'label' => 'Feature 2 - Description', 'type' => 'textarea', 'value' => 'What you see is what you pay. No hidden fees or commissions.', 'default' => 'What you see is what you pay. No hidden fees or commissions.', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.feature_3_title', 'label' => 'Feature 3 - Title', 'type' => 'text', 'value' => 'Community Growth', 'default' => 'Community Growth', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.feature_3_desc', 'label' => 'Feature 3 - Description', 'type' => 'textarea', 'value' => 'Directly empower your peers to develop skills and gain independence.', 'default' => 'Directly empower your peers to develop skills and gain independence.', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.cta_badge', 'label' => 'CTA - Badge Text', 'type' => 'text', 'value' => 'Become part of the community', 'default' => 'Become part of the community', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.cta_title', 'label' => 'CTA - Main Heading', 'type' => 'text', 'value' => 'Ready to get started?', 'default' => 'Ready to get started?', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.cta_subtitle', 'label' => 'CTA - Subtext', 'type' => 'textarea', 'value' => 'Join hundreds of UPSI students who are already connecting, learning, and earning on UPSI2u today.', 'default' => 'Join hundreds of UPSI students who are already connecting, learning, and earning on UPSI2u today.', 'active' => true],
            ['page' => 'welcome', 'slug' => 'welcome.cta_footnote', 'label' => 'CTA - Footnote', 'type' => 'text', 'value' => 'Exclusively for UPSI Students and local Tg.Malim', 'default' => 'Exclusively for UPSI Students and local Tg.Malim', 'active' => true],

            // About text blocks
            ['page' => 'about', 'slug' => 'about.hero_badge', 'label' => 'Hero - Mission Badge', 'type' => 'text', 'value' => 'Our Mission', 'default' => 'Our Mission', 'active' => true],
            ['page' => 'about', 'slug' => 'about.hero_title', 'label' => 'Hero - Main Heading', 'type' => 'text', 'value' => 'Empowering the UPSI Community through UPSI2u', 'default' => 'Empowering the UPSI Community through UPSI2u', 'active' => true],
            ['page' => 'about', 'slug' => 'about.hero_description', 'label' => 'Hero - Description Paragraph', 'type' => 'textarea', 'value' => 'UPSI2u (UPSI Service Circle) is more than just a marketplace. It is a dedicated ecosystem designed specifically for UPSI students to bridge the gap between talent and needs. Whether you\'re looking for expert tutoring, creative design, or technical coding help, your peers are here to deliver.', 'default' => 'UPSI2u (UPSI Service Circle) is more than just a marketplace. It is a dedicated ecosystem designed specifically for UPSI students to bridge the gap between talent and needs. Whether you\'re looking for expert tutoring, creative design, or technical coding help, your peers are here to deliver.', 'active' => true],
            ['page' => 'about', 'slug' => 'about.story_title', 'label' => 'Story - Section Title', 'type' => 'text', 'value' => 'Built by Students, For the Community.', 'default' => 'Built by Students, For the Community.', 'active' => true],
            ['page' => 'about', 'slug' => 'about.story_quote', 'label' => 'Story - Pull Quote', 'type' => 'textarea', 'value' => '"UPSI2u was developed in 2025 out of a simple need: a trusted, friendly, and more effective way for UPSI students to help one another."', 'default' => '"UPSI2u was developed in 2025 out of a simple need: a trusted, friendly, and more effective way for UPSI students to help one another."', 'active' => true],
            ['page' => 'about', 'slug' => 'about.story_body_1', 'label' => 'Story - Body Paragraph 1', 'type' => 'textarea', 'value' => 'Founded by a group of students who experienced the frustration of searching for reliable academic help and creative services. Tired of unreliable providers and cluttered listings, they decided to build the solution the UPSI community deserved.', 'default' => 'Founded by a group of students who experienced the frustration of searching for reliable academic help and creative services. Tired of unreliable providers and cluttered listings, they decided to build the solution the UPSI community deserved.', 'active' => true],
            ['page' => 'about', 'slug' => 'about.story_highlight', 'label' => 'Story - Highlighted Box Text', 'type' => 'textarea', 'value' => 'What started as a small project has now become a movement, transforming how we connect and support each other\'s financial and academic growth.', 'default' => 'What started as a small project has now become a movement, transforming how we connect and support each other\'s financial and academic growth.', 'active' => true],
            ['page' => 'about', 'slug' => 'about.story_body_2', 'label' => 'Story - Body Paragraph 2', 'type' => 'textarea', 'value' => 'Today, UPSI2u stands as a leader in student-led services at UPSI, continuously growing as more students turn to us for verified, peer-to-peer excellence.', 'default' => 'Today, UPSI2u stands as a leader in student-led services at UPSI, continuously growing as more students turn to us for verified, peer-to-peer excellence.', 'active' => true],
            ['page' => 'about', 'slug' => 'about.cta_title', 'label' => 'CTA - Heading', 'type' => 'text', 'value' => 'Ready to be part of the movement?', 'default' => 'Ready to be part of the movement?', 'active' => true],
            ['page' => 'about', 'slug' => 'about.cta_subtitle', 'label' => 'CTA - Subtext', 'type' => 'textarea', 'value' => 'Join UPSI2u and grow together with your campus community.', 'default' => 'Join UPSI2u and grow together with your campus community.', 'active' => true],

            // Settings blocks
            ['page' => 'settings', 'slug' => 'settings.support_email', 'label' => 'Support Email Address', 'type' => 'text', 'value' => 'support@upsi2u.upsi.edu.my', 'default' => 'support@upsi2u.upsi.edu.my', 'active' => true],
            ['page' => 'settings', 'slug' => 'settings.support_hours', 'label' => 'Support Hours', 'type' => 'text', 'value' => 'Mon-Fri, 8AM-5PM', 'default' => 'Mon-Fri, 8AM-5PM', 'active' => true],
            ['page' => 'settings', 'slug' => 'settings.phone_number', 'label' => 'Contact Phone / WhatsApp', 'type' => 'text', 'value' => '60123456789', 'default' => '60123456789', 'active' => true],
            ['page' => 'settings', 'slug' => 'settings.facebook_url', 'label' => 'Facebook URL', 'type' => 'text', 'value' => 'https://www.facebook.com/UPSIMalaysia', 'default' => 'https://www.facebook.com/UPSIMalaysia', 'active' => true],
            ['page' => 'settings', 'slug' => 'settings.instagram_url', 'label' => 'Instagram URL', 'type' => 'text', 'value' => 'https://www.instagram.com/upsi_malaysia', 'default' => 'https://www.instagram.com/upsi_malaysia', 'active' => true],
            ['page' => 'settings', 'slug' => 'settings.tiktok_url', 'label' => 'TikTok URL', 'type' => 'text', 'value' => 'https://www.tiktok.com/@upsi_malaysia', 'default' => 'https://www.tiktok.com/@upsi_malaysia', 'active' => true],

            // Dashboard text blocks
            ['page' => 'dashboard', 'slug' => 'dashboard.hero_badge', 'label' => 'Hero - Badge Text', 'type' => 'text', 'value' => 'Welcome back, {name}!', 'default' => 'Welcome back, {name}!', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.hero_title_line_1', 'label' => 'Hero - Title Line 1', 'type' => 'text', 'value' => 'Find the perfect', 'default' => 'Find the perfect', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.hero_title_highlight', 'label' => 'Hero - Highlighted Title', 'type' => 'text', 'value' => 'student seller', 'default' => 'student seller', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.hero_title_line_2', 'label' => 'Hero - Title Line 2', 'type' => 'text', 'value' => 'for your needs.', 'default' => 'for your needs.', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.hero_subtitle', 'label' => 'Hero - Subtitle', 'type' => 'textarea', 'value' => 'Discover talented UPSI students offering professional services. From design to daily tasks, get it done by your community.', 'default' => 'Discover talented UPSI students offering professional services. From design to daily tasks, get it done by your community.', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.search_placeholder', 'label' => 'Hero - Search Placeholder', 'type' => 'text', 'value' => 'What service are you looking for today?', 'default' => 'What service are you looking for today?', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.popular_label', 'label' => 'Hero - Popular Label', 'type' => 'text', 'value' => 'Popular:', 'default' => 'Popular:', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.categories_title', 'label' => 'Categories - Section Title', 'type' => 'text', 'value' => 'Explore Categories', 'default' => 'Explore Categories', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.recommended_title', 'label' => 'Recommendations - Section Title', 'type' => 'text', 'value' => 'Services you might like', 'default' => 'Services you might like', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.recommended_subtitle', 'label' => 'Recommendations - Section Subtitle', 'type' => 'text', 'value' => 'Recommended based on popular demand.', 'default' => 'Recommended based on popular demand.', 'active' => true],

            // Media blocks
            ['page' => 'welcome', 'slug' => 'welcome.hero_video', 'label' => 'Hero Background Video', 'type' => 'video', 'value' => 'videos/herobanner.mp4', 'default' => 'videos/herobanner.mp4', 'active' => true],
            ['page' => 'about', 'slug' => 'about.hero_image', 'label' => 'About - Hero Image', 'type' => 'image', 'value' => 'images/about.jpg', 'default' => 'images/about.jpg', 'active' => true],
            ['page' => 'about', 'slug' => 'about.story_image', 'label' => 'About - Story Image', 'type' => 'image', 'value' => 'images/about2.jpg', 'default' => 'images/about2.jpg', 'active' => true],
            ['page' => 'dashboard', 'slug' => 'dashboard.hero_image', 'label' => 'Dashboard Banner', 'type' => 'image', 'value' => 'images/bgupsi.jpg', 'default' => 'images/bgupsi.jpg', 'active' => true],
        ];

        foreach ($blocks as $block) {
            PageContent::query()->updateOrCreate(
                ['hpc_slug' => $block['slug']],
                [
                    'hpc_page' => $block['page'],
                    'hpc_label' => $block['label'],
                    'hpc_type' => $block['type'],
                    'hpc_value' => $block['value'],
                    'hpc_default' => $block['default'],
                    'hpc_is_active' => $block['active'],
                ]
            );
        }
    }
}
