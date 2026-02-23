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
    Schema::table('service_requests', function (Blueprint $table) {
        $table->json('selected_dates')->nullable(); // Adjust the column type as necessary
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
   {
    Schema::table('service_requests', function (Blueprint $table) {
        $table->dropColumn('selected_dates');
        });
    }
};
