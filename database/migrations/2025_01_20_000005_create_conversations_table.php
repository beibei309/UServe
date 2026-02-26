<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('h2u_conversations', function (Blueprint $table) {
            $table->bigIncrements('hc_id');
            $table->unsignedBigInteger('hc_chat_request_id')->unique();
            $table->foreign('hc_chat_request_id')->references('hcr_id')->on('h2u_chat_requests')->cascadeOnDelete();
            $table->unsignedBigInteger('hc_student_id');
            $table->foreign('hc_student_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->unsignedBigInteger('hc_customer_id');
            $table->foreign('hc_customer_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->timestamp('hc_started_at')->nullable();
            $table->timestamp('hc_ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_conversations');
    }
};
