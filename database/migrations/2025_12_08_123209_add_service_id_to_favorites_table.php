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
          Schema::table('h2u_favorites', function (Blueprint $table) {
            $table->unsignedBigInteger('hf_service_id')->nullable()->after('hf_favorited_user_id');

            // OPTIONAL foreign key
            $table->foreign('hf_service_id')
                ->references('hss_id')
                ->on('h2u_student_services')
                ->onDelete('cascade');
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_favorites', function (Blueprint $table) {
            $table->dropForeign(['hf_service_id']);
            $table->dropColumn('hf_service_id');
        });
    }
};
