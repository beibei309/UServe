<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->string('hss_image_path')->nullable()->after('hss_title'); // or after any column you like
        });
    }

    public function down(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->dropColumn('hss_image_path');
        });
    }
};
