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
            // Only add columns if they don't exist
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('blacklist_reason');
            }
            if (!Schema::hasColumn('users', 'faculty')) {
                $table->string('faculty')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'course')) {
                $table->string('course')->nullable()->after('faculty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'faculty', 'course']);
        });
    }
};
