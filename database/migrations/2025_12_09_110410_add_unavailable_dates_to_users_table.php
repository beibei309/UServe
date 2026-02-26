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
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->date('hu_unavailable_start_date')->nullable()->after('hu_is_available');
            $table->date('hu_unavailable_end_date')->nullable()->after('hu_unavailable_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->dropColumn(['hu_unavailable_start_date', 'hu_unavailable_end_date']);
        });
    }
};
