<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE student_statuses ALTER COLUMN semester DROP NOT NULL');
        } else {
            Schema::table('student_statuses', function (Blueprint $table) {
                $table->string('semester')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE student_statuses ALTER COLUMN semester SET NOT NULL');
        } else {
            Schema::table('student_statuses', function (Blueprint $table) {
                $table->string('semester')->nullable(false)->change();
            });
        }
    }
};
