<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->json('hss_unavailable_dates')->nullable()->after('hss_description');
        });
    }

    public function down(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->dropColumn('hss_unavailable_dates');
        });
    }
};
