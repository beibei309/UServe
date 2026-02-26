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
        Schema::table('h2u_student_services', function (Blueprint $table) {
            if (!Schema::hasColumn('h2u_student_services', 'hss_warning_count')) {
                $table->integer('hss_warning_count')->default(0)->after('hss_status');
            }

            if (!Schema::hasColumn('h2u_student_services', 'hss_warning_reason')) {
                $table->text('hss_warning_reason')->nullable()->after('hss_warning_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('h2u_student_services', 'hss_warning_count')) {
                $columnsToDrop[] = 'hss_warning_count';
            }

            if (Schema::hasColumn('h2u_student_services', 'hss_warning_reason')) {
                $columnsToDrop[] = 'hss_warning_reason';
            }

            if ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
