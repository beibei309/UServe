<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('h2u_reviews', function (Blueprint $table) {
            $table->bigIncrements('hr_id');
            $table->foreignId('hr_conversation_id')->constrained('h2u_conversations', 'hc_id')->cascadeOnDelete();
            $table->unsignedBigInteger('hr_reviewer_id');
            $table->foreign('hr_reviewer_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->unsignedBigInteger('hr_reviewee_id');
            $table->foreign('hr_reviewee_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->unsignedTinyInteger('hr_rating'); // 1-5
            $table->text('hr_comment')->nullable();
            $table->timestamps();

            $table->unique(['hr_conversation_id', 'hr_reviewer_id', 'hr_reviewee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_reviews');
    }
};
