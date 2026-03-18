<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_student_statuses', function (Blueprint $table) {
            $table->string('hss_program')->nullable()->after('hss_matric_no');
            $table->string('hss_program_desc')->nullable()->after('hss_program');
            $table->string('hss_source_student_name')->nullable()->after('hss_program_desc');
            $table->string('hss_source_email')->nullable()->after('hss_source_student_name');
            $table->string('hss_source_status_desc')->nullable()->after('hss_source_email');
        });
    }

    public function down(): void
    {
        Schema::table('h2u_student_statuses', function (Blueprint $table) {
            $table->dropColumn([
                'hss_program',
                'hss_program_desc',
                'hss_source_student_name',
                'hss_source_email',
                'hss_source_status_desc',
            ]);
        });
    }
};
