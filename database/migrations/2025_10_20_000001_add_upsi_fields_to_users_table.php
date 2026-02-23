<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('community')->index(); // community, student, admin
            $table->string('phone')->nullable()->index();
            $table->string('student_id')->nullable()->index(); // For student role

            // Trust and verification fields
            $table->timestamp('public_verified_at')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->string('profile_photo_path')->nullable();
            $table->string('selfie_media_path')->nullable();

            // Staff upgrade
            $table->string('staff_email')->nullable();
            $table->timestamp('staff_verified_at')->nullable();

            // Availability
            $table->boolean('is_available')->default(true)->index();

            // Moderation
            $table->boolean('is_suspended')->default(false)->index();
            $table->boolean('is_blacklisted')->default(false)->index();
            $table->text('blacklist_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'public_verified_at',
                'verification_status',
                'profile_photo_path',
                'selfie_media_path',
                'staff_email',
                'staff_verified_at',
                'is_available',
                'is_suspended',
                'is_blacklisted',
                'blacklist_reason',
            ]);
        });
    }
};