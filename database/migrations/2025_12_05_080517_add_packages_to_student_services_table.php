<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            // Basic package
            $table->string('basic_duration', 50)->nullable();
            $table->string('basic_frequency', 50)->nullable();
            $table->decimal('basic_price', 10, 2)->nullable();
            $table->text('basic_description')->nullable();

            // Standard package
            $table->string('standard_duration', 50)->nullable();
            $table->string('standard_frequency', 50)->nullable();
            $table->decimal('standard_price', 10, 2)->nullable();
            $table->text('standard_description')->nullable();

            // Premium package
            $table->string('premium_duration', 50)->nullable();
            $table->string('premium_frequency', 50)->nullable();
            $table->decimal('premium_price', 10, 2)->nullable();
            $table->text('premium_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            $table->dropColumn([
                'basic_duration', 'basic_frequency', 'basic_price', 'basic_description',
                'standard_duration', 'standard_frequency', 'standard_price', 'standard_description',
                'premium_duration', 'premium_frequency', 'premium_price', 'premium_description',
            ]);
        });
    }
};
