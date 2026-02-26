<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->string('hu_otp_code', 6)->nullable();
            $table->boolean('hu_is_verified')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->dropColumn(['hu_otp_code', 'hu_is_verified']);
        });
    }
};
