<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('h2u_reviews', function (Blueprint $table) {
            // Add student service id to reviews table
            $table->unsignedBigInteger('hr_student_service_id')->nullable()->after('hr_id');
            $table->foreign('hr_student_service_id')
                ->references('hss_id')
                ->on('h2u_student_services')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_reviews', function (Blueprint $table) {
            $table->dropForeign(['hr_student_service_id']);
            $table->dropColumn('hr_student_service_id');
        });
    }
};
