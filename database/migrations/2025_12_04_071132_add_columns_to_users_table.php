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
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->text('address')->nullable(); 
            $table->text('skills')->nullable(); 
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
   {
        Schema::table('users', function (Blueprint $table) {
            // Drop the columns if rolling back
            $table->dropColumn(['address', 'skills']);
        });
    }
};
