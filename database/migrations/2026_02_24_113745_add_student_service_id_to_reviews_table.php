<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('h2u_reviews', 'hr_student_service_id')) {
            return;
        }

        Schema::table('h2u_reviews', function (Blueprint $table) {
            $table->foreignId('hr_student_service_id')
                ->nullable()
                ->constrained('h2u_student_services', 'hss_id')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('h2u_reviews', 'hr_student_service_id')) {
            return;
        }

        Schema::table('h2u_reviews', function (Blueprint $table) {
            try {
                $table->dropForeign(['hr_student_service_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            $table->dropColumn('hr_student_service_id');
        });
    }
};
