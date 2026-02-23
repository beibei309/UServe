<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. warning_count (Default: 0)
            $table->unsignedSmallInteger('warning_count')->default(0); 
            
            // 2. is_blocked (Default: FALSE/0)
            $table->boolean('is_blocked')->default(false); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['warning_count', 'is_blocked']);
        });
    }
};
