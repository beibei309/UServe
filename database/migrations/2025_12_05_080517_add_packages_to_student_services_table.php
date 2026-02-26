<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            // Basic package
            $table->string('hss_basic_duration', 50)->nullable();
            $table->string('hss_basic_frequency', 50)->nullable();
            $table->decimal('hss_basic_price', 10, 2)->nullable();
            $table->text('hss_basic_description')->nullable();

            // Standard package
            $table->string('hss_standard_duration', 50)->nullable();
            $table->string('hss_standard_frequency', 50)->nullable();
            $table->decimal('hss_standard_price', 10, 2)->nullable();
            $table->text('hss_standard_description')->nullable();

            // Premium package
            $table->string('hss_premium_duration', 50)->nullable();
            $table->string('hss_premium_frequency', 50)->nullable();
            $table->decimal('hss_premium_price', 10, 2)->nullable();
            $table->text('hss_premium_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->dropColumn([
                'hss_basic_duration', 'hss_basic_frequency', 'hss_basic_price', 'hss_basic_description',
                'hss_standard_duration', 'hss_standard_frequency', 'hss_standard_price', 'hss_standard_description',
                'hss_premium_duration', 'hss_premium_frequency', 'hss_premium_price', 'hss_premium_description',
            ]);
        });
    }
};
