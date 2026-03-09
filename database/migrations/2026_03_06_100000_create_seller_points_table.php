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
        Schema::create('h2u_seller_points', function (Blueprint $table) {
            $table->id('hsp_id');
            $table->unsignedBigInteger('hsp_user_id'); // Seller ID
            $table->unsignedBigInteger('hsp_service_request_id'); // Related service request
            $table->integer('hsp_points_earned')->default(1); // Points earned for this transaction
            $table->enum('hsp_status', ['earned', 'pending'])->default('earned');
            $table->text('hsp_description')->nullable(); // Optional description
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('hsp_user_id')->references('hu_id')->on('h2u_users')->onDelete('cascade');
            $table->foreign('hsp_service_request_id')->references('hsr_id')->on('h2u_service_requests')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('hsp_user_id');
            $table->index('hsp_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_seller_points');
    }
};