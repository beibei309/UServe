<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_favorites', function (Blueprint $table) {
            $table->dropUnique('h2u_favorites_hf_user_id_hf_favorited_user_id_unique');
            $table->unique(
                ['hf_user_id', 'hf_favorited_user_id', 'hf_service_id'],
                'h2u_favorites_user_favorited_service_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('h2u_favorites', function (Blueprint $table) {
            $table->dropUnique('h2u_favorites_user_favorited_service_unique');
            $table->unique(['hf_user_id', 'hf_favorited_user_id']);
        });
    }
};
