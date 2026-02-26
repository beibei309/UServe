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
    Schema::table('h2u_service_requests', function (Blueprint $table) {
        $table->json('hsr_selected_package')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
   {
    Schema::table('h2u_service_requests', function (Blueprint $table) {
        $table->dropColumn('hsr_selected_package');
        });
    }
};
