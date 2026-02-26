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
            // Only add columns if they don't exist
            if (!Schema::hasColumn('h2u_users', 'hu_bio')) {
                $table->text('hu_bio')->nullable()->after('hu_blacklist_reason');
            }
            if (!Schema::hasColumn('h2u_users', 'hu_faculty')) {
                $table->string('hu_faculty')->nullable()->after('hu_bio');
            }
            if (!Schema::hasColumn('h2u_users', 'hu_course')) {
                $table->string('hu_course')->nullable()->after('hu_faculty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->dropColumn(['hu_bio', 'hu_faculty', 'hu_course']);
        });
    }
};
