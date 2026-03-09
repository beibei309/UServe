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
        Schema::create('h2u_reward_redemptions', function (Blueprint $table) {
            $table->id('hrr_id');
            $table->unsignedBigInteger('hrr_user_id');
            $table->unsignedBigInteger('hrr_reward_id');
            $table->integer('hrr_points_used');
            $table->string('hrr_redemption_code', 50)->unique();
            $table->enum('hrr_status', ['pending', 'active', 'used', 'expired', 'cancelled'])->default('active');
            $table->timestamp('hrr_redeemed_at');
            $table->timestamp('hrr_expires_at')->nullable();
            $table->timestamp('hrr_used_at')->nullable();
            $table->text('hrr_notes')->nullable();
            $table->timestamps();

            $table->foreign('hrr_user_id')->references('hu_id')->on('h2u_users')->onDelete('cascade');
            $table->foreign('hrr_reward_id')->references('hr_id')->on('h2u_rewards')->onDelete('cascade');
            
            $table->index(['hrr_user_id']);
            $table->index(['hrr_status']);
            $table->index(['hrr_redemption_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_reward_redemptions');
    }
};
