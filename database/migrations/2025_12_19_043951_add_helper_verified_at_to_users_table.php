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
            $table->timestamp('hu_helper_verified_at')->nullable()->after('hu_staff_verified_at');
            $table->decimal('hu_latitude', 10, 8)->nullable()->after('hu_address');
            $table->decimal('hu_longitude', 11, 8)->nullable()->after('hu_latitude');
            $table->timestamp('hu_location_verified_at')->nullable()->after('hu_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->dropColumn(['hu_helper_verified_at', 'hu_latitude', 'hu_longitude', 'hu_location_verified_at']);
        });
    }
};
