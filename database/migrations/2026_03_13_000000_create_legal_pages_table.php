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
                'hlp_content' => '<h2>1. Acceptance of Terms</h2><p>By accessing and using U-Serve, you accept and agree to be bound by these terms.</p><h2>2. Use License</h2><p>Permission is granted to temporarily use U-Serve for personal, non-commercial use only.</p><h2>3. User Accounts</h2><p>Users are responsible for maintaining confidentiality of their account information.</p><h2>4. Student Services</h2><p>U-Serve connects UPSI students and community members. Service quality remains the responsibility of service providers.</p>',
                'hlp_is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'hlp_slug' => 'privacy',
                'hlp_title' => 'Privacy Policy',
                'hlp_content' => '<h2>1. Information We Collect</h2><p>We collect information provided during account and service usage.</p><h2>2. How We Use Information</h2><p>Information is used to operate, improve, and secure the platform.</p><h2>3. Information Sharing</h2><p>We do not sell personal information to third parties without consent.</p><h2>4. Data Security</h2><p>We apply reasonable safeguards to protect user data.</p><h2>5. Your Rights</h2><p>You may request access, correction, or deletion of your personal information as permitted by law.</p>',
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
