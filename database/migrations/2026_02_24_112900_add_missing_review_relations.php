<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('h2u_reviews', 'hr_service_request_id')) {
                $table->foreignId('hr_service_request_id')
                    ->nullable()
                    ->constrained('h2u_service_requests', 'hsr_id')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('h2u_reviews', 'hr_service_application_id')) {
                $table->foreignId('hr_service_application_id')
                    ->nullable()
                    ->constrained('h2u_service_applications', 'hsa_id')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('h2u_reviews', 'hr_student_service_id')) {
                $table->foreignId('hr_student_service_id')
                    ->nullable()
                    ->constrained('h2u_student_services', 'hss_id')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('h2u_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('h2u_reviews', 'hr_service_request_id')) {
                $table->dropConstrainedForeignId('hr_service_request_id');
            }

            if (Schema::hasColumn('h2u_reviews', 'hr_service_application_id')) {
                $table->dropConstrainedForeignId('hr_service_application_id');
            }

            if (Schema::hasColumn('h2u_reviews', 'hr_student_service_id')) {
                $table->dropConstrainedForeignId('hr_student_service_id');
            }
        });
    }
};
