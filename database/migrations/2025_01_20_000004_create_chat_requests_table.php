<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('h2u_chat_requests', function (Blueprint $table) {
            $table->bigIncrements('hcr_id');
            $table->unsignedBigInteger('hcr_requester_id'); // community/staff
            $table->foreign('hcr_requester_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->unsignedBigInteger('hcr_recipient_id'); // student
            $table->foreign('hcr_recipient_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->enum('hcr_status', ['pending', 'accepted', 'declined', 'cancelled'])->default('pending')->index();
            $table->text('hcr_message')->nullable();
            $table->timestamp('hcr_accepted_at')->nullable();
            $table->timestamp('hcr_declined_at')->nullable();
            $table->timestamps();

            $table->index(['hcr_requester_id', 'hcr_recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_chat_requests');
    }
};
