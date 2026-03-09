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
        Schema::create('h2u_certificate_redemptions', function (Blueprint $table) {
            $table->id('hcr_id');
            $table->unsignedBigInteger('hcr_user_id'); // User who redeemed
            $table->integer('hcr_points_used')->default(3); // Points used for redemption
            $table->string('hcr_certificate_number')->unique(); // Unique certificate number
            $table->enum('hcr_status', ['pending', 'issued', 'cancelled'])->default('pending');
            $table->text('hcr_notes')->nullable();
            $table->timestamp('hcr_issued_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('hcr_user_id')->references('hu_id')->on('h2u_users')->onDelete('cascade');
            
            // Indexes
            $table->index('hcr_user_id');
            $table->index('hcr_status');
            $table->index('hcr_certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_certificate_redemptions');
    }
};