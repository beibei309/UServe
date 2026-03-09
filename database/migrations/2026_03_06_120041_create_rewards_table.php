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
        Schema::create('h2u_rewards', function (Blueprint $table) {
            $table->id('hr_id');
            $table->string('hr_title');
            $table->text('hr_description');
            $table->enum('hr_type', ['discount', 'service_credit', 'voucher'])->default('voucher');
            $table->integer('hr_points_cost');
            $table->decimal('hr_value', 8, 2)->default(0.00);
            $table->string('hr_code_prefix', 20);
            $table->integer('hr_usage_limit')->nullable(); // Total usage limit across all users
            $table->integer('hr_user_limit')->default(1); // Per-user usage limit
            $table->boolean('hr_is_active')->default(true);
            $table->timestamp('hr_expires_at')->nullable();
            $table->json('hr_terms')->nullable(); // Terms and conditions
            $table->timestamps();

            $table->index(['hr_is_active']);
            $table->index(['hr_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_rewards');
    }
};
