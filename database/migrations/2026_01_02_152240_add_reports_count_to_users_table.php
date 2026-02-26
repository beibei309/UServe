<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('h2u_users', function (Blueprint $table) {
        $table->integer('hu_reports_count')->default(0); // Counts how many times they were reported
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->dropColumn('hu_reports_count');
        });
    }
};
