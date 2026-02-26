<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->string('hss_status')->default('pending')->after('hss_suggested_price');
            $table->string('hss_price_range')->nullable()->after('hss_status');
        });
    }

    public function down(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->dropColumn(['hss_status', 'hss_price_range']);
        });
    }
};
