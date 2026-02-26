<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            // 1. hu_warning_count (Default: 0)
            $table->unsignedSmallInteger('hu_warning_count')->default(0);

            // 2. hu_is_blocked (Default: FALSE/0)
            $table->boolean('hu_is_blocked')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->dropColumn(['hu_warning_count', 'hu_is_blocked']);
        });
    }
};
