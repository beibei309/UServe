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
        Schema::create('h2u_service_requests', function (Blueprint $table) {
            $table->bigIncrements('hsr_id');
            $table->unsignedBigInteger('hsr_student_service_id');
            $table->foreign('hsr_student_service_id')->references('hss_id')->on('h2u_student_services')->onDelete('cascade');
            $table->unsignedBigInteger('hsr_requester_id'); // Community member
            $table->foreign('hsr_requester_id')->references('hu_id')->on('h2u_users')->onDelete('cascade');
            $table->unsignedBigInteger('hsr_provider_id'); // Student
            $table->foreign('hsr_provider_id')->references('hu_id')->on('h2u_users')->onDelete('cascade');
            $table->text('hsr_message')->nullable(); // Optional message from requester
            $table->decimal('hsr_offered_price', 10, 2)->nullable(); // Price offered by requester
            $table->enum('hsr_status', ['pending', 'accepted', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('hsr_accepted_at')->nullable();
            $table->timestamp('hsr_completed_at')->nullable();
            $table->timestamps();

            $table->index(['hsr_provider_id', 'hsr_status']);
            $table->index(['hsr_requester_id', 'hsr_status']);
            $table->index(['hsr_student_service_id', 'hsr_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_service_requests');
    }
};
