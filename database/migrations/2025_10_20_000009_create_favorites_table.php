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
        // Check if table already exists (from SQL import)
        if (!Schema::hasTable('h2u_favorites')) {
            Schema::create('h2u_favorites', function (Blueprint $table) {
                $table->bigIncrements('hf_id');
                $table->unsignedBigInteger('hf_user_id');
                $table->foreign('hf_user_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
                $table->unsignedBigInteger('hf_favorited_user_id');
                $table->foreign('hf_favorited_user_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
                $table->timestamps();

                // Ensure a user can't favorite the same user multiple times
                $table->unique(['hf_user_id', 'hf_favorited_user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_favorites');
    }
};
