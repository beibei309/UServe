<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete(); // community/staff
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete(); // student
            $table->enum('status', ['pending', 'accepted', 'declined', 'cancelled'])->default('pending')->index();
            $table->text('message')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamps();

            $table->index(['requester_id', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_requests');
    }
};