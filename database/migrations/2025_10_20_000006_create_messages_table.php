<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('h2u_messages', function (Blueprint $table) {
            $table->bigIncrements('hm_id');
            $table->unsignedBigInteger('hm_conversation_id');
            $table->foreign('hm_conversation_id')->references('hc_id')->on('h2u_conversations')->cascadeOnDelete();
            $table->unsignedBigInteger('hm_sender_id');
            $table->foreign('hm_sender_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->text('hm_body');
            $table->timestamp('hm_read_at')->nullable();
            $table->timestamps();

            $table->index(['hm_conversation_id', 'hm_sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_messages');
    }
};
