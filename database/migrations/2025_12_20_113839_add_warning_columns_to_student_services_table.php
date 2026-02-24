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
        Schema::table('student_services', function (Blueprint $table) {
            if (!Schema::hasColumn('student_services', 'warning_count')) {
                $table->integer('warning_count')->default(0)->after('status');
            }

            if (!Schema::hasColumn('student_services', 'warning_reason')) {
                $table->text('warning_reason')->nullable()->after('warning_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('student_services', 'warning_count')) {
                $columnsToDrop[] = 'warning_count';
            }

            if (Schema::hasColumn('student_services', 'warning_reason')) {
                $columnsToDrop[] = 'warning_reason';
            }

            if ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
