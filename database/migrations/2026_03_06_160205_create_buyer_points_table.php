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
        Schema::create('h2u_buyer_points', function (Blueprint $table) {
            $table->id('hbp_id');
            $table->unsignedBigInteger('hbp_user_id'); // Buyer ID
            $table->unsignedBigInteger('hbp_service_request_id')->nullable(); // Related service request
            $table->integer('hbp_points_earned')->default(1); // Points earned for this transaction
            $table->enum('hbp_status', ['earned', 'pending'])->default('earned');
            $table->text('hbp_description')->nullable(); // Optional description
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('hbp_user_id')->references('hu_id')->on('h2u_users')->onDelete('cascade');
            $table->foreign('hbp_service_request_id')->references('hsr_id')->on('h2u_service_requests')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('hbp_user_id');
            $table->index('hbp_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_points');
    }
};
