<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('reviews', 'student_service_id')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('student_service_id')
                ->nullable()
                ->constrained('student_services')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('reviews', 'student_service_id')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            try {
                $table->dropForeign(['student_service_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            $table->dropColumn('student_service_id');
        });
    }
};
