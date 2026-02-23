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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_service_id')->constrained('student_services')->onDelete('cascade');
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade'); // Community member
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade'); // Student
            $table->text('message')->nullable(); // Optional message from requester
            $table->decimal('offered_price', 10, 2)->nullable(); // Price offered by requester
            $table->enum('status', ['pending', 'accepted', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['provider_id', 'status']);
            $table->index(['requester_id', 'status']);
            $table->index(['student_service_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};