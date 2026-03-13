<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('h2u_legal_pages', function (Blueprint $table) {
            $table->bigIncrements('hlp_id');
            $table->string('hlp_slug', 50)->unique();
            $table->string('hlp_title', 150);
            $table->longText('hlp_content');
            $table->boolean('hlp_is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('h2u_legal_pages')->insert([
            [
                'hlp_slug' => 'terms',
                'hlp_title' => 'Terms of Service',
                'hlp_content' => '<h2>1. Acceptance of Terms</h2><p>By accessing and using UpsiConnect, you accept and agree to be bound by the terms and provisions of this agreement.</p><h2>2. User Eligibility & Accounts Registration</h2><p>Restricted to UPSI students and verified community members. Users are responsible for maintaining the confidentiality of their account information and for all activities that occur under their account.</p><h2>3. Student Services & Disclaimers</h2><p>UpsiConnect facilitates connections between UPSI students and community members. While we verify student status, we do not guarantee the quality, safety, or legality of services provided. Users engage in transactions at their own risk.</p><h2>4. Safety & Verification</h2><p>For the safety of all users, UpsiConnect requires location verification and identity checks (Selfies/Documents). Any attempt to bypass these checks or provide fraudulent data will result in a permanent ban.</p><h2>5. Code of Conduct</h2><p>All users must maintain respectful communication. Harassment, discrimination, or inappropriate behavior is strictly prohibited. Users must report any suspicious activity immediately through the platform.</p><h2>6. Payments & Disputes</h2><p>UpsiConnect provides a platform for service requests but is not responsible for payment disputes between parties. Users are encouraged to finalize payments only after the service is successfully completed.</p><h2>7. Account Suspension</h2><p>UpsiConnect reserves the right to suspend or terminate accounts that violate community guidelines or safety protocols without prior notice.</p><h2>8. Contact Information</h2><p>For questions about these Terms of Service, please contact us through the platform\'s support system or visit the help desk.</p>',
                'hlp_is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'hlp_slug' => 'privacy',
                'hlp_title' => 'Privacy Policy',
                'hlp_content' => '<h2>1. Data Collection</h2><p>We collect personal information necessary for platform functionality, including your name, UPSI matric number (for students), phone number, and email address.</p><h2>2. Verification Data (Biometric & Identity)</h2><p>To ensure community safety, we collect and store profile photos, live verification selfies, and identity documents. This data is stored securely and is only accessible by authorized administrators.</p><h2>3. Location Information</h2><p>U-Serve collects precise location data during the verification process to ensure users are within the Muallim District service area.</p><h2>4. Data Usage</h2><p>Your data is used to facilitate service requests, verify your identity, and improve platform security. We do not sell your personal data to third parties.</p><h2>5. Data Retention</h2><p>Verification documents and selfies are retained as long as your account is active. You may request account deletion through the profile settings, which will remove your personal files from our storage.</p><h2>6. Third-Party Services</h2><p>We may use third-party tools for maps and notifications. These services have their own privacy policies regarding how they handle your data.</p><h2>7. Security Measures</h2><p>We implement industry-standard security measures, including encryption and strict access controls, to protect your sensitive information from unauthorized access.</p><h2>8. Policy Updates</h2><p>We may update this Privacy Policy from time to time. Users will be notified of significant changes through platform notifications.</p>',
                'hlp_is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_legal_pages');
    }
};
