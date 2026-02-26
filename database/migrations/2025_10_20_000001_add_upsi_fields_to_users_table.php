<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->string('hu_role')->default('community')->index(); // community, student, admin
            $table->string('hu_phone')->nullable()->index();
            $table->string('hu_student_id')->nullable()->index(); // For student role

            // Trust and verification fields
            $table->timestamp('hu_public_verified_at')->nullable();
            $table->enum('hu_verification_status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->string('hu_profile_photo_path')->nullable();
            $table->string('hu_selfie_media_path')->nullable();

            // Staff upgrade
            $table->string('hu_staff_email')->nullable();
            $table->timestamp('hu_staff_verified_at')->nullable();

            // Availability
            $table->boolean('hu_is_available')->default(true)->index();

            // Moderation
            $table->boolean('hu_is_suspended')->default(false)->index();
            $table->boolean('hu_is_blacklisted')->default(false)->index();
            $table->text('hu_blacklist_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->dropColumn([
                'hu_role',
                'hu_phone',
                'hu_student_id',
                'hu_public_verified_at',
                'hu_verification_status',
                'hu_profile_photo_path',
                'hu_selfie_media_path',
                'hu_staff_email',
                'hu_staff_verified_at',
                'hu_is_available',
                'hu_is_suspended',
                'hu_is_blacklisted',
                'hu_blacklist_reason',
            ]);
        });
    }
};
