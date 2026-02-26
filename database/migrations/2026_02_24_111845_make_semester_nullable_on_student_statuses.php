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
            DB::statement('ALTER TABLE h2u_student_statuses ALTER COLUMN hss_semester DROP NOT NULL');
        } else {
            Schema::table('h2u_student_statuses', function (Blueprint $table) {
                $table->string('hss_semester')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE h2u_student_statuses ALTER COLUMN hss_semester SET NOT NULL');
        } else {
            Schema::table('h2u_student_statuses', function (Blueprint $table) {
                $table->string('hss_semester')->nullable(false)->change();
            });
        }
    }
};
