<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'service_request_id')) {
                $table->foreignId('service_request_id')
                    ->nullable()
                    ->constrained('service_requests')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('reviews', 'service_application_id')) {
                $table->foreignId('service_application_id')
                    ->nullable()
                    ->constrained('service_applications')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('reviews', 'student_service_id')) {
                $table->foreignId('student_service_id')
                    ->nullable()
                    ->constrained('student_services')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'service_request_id')) {
                $table->dropConstrainedForeignId('service_request_id');
            }

            if (Schema::hasColumn('reviews', 'service_application_id')) {
                $table->dropConstrainedForeignId('service_application_id');
            }

            if (Schema::hasColumn('reviews', 'student_service_id')) {
                $table->dropConstrainedForeignId('student_service_id');
            }
        });
    }
};
