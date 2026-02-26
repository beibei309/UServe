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
            if (Schema::hasColumn('h2u_users', 'hu_otp_code')) {
                $table->dropColumn('hu_otp_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->string('hu_otp_code', 6)->nullable()->after('hu_email');
        });
    }
};
