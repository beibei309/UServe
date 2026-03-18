<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('h2u_student_statuses')) {
            return;
        }

        if (Schema::hasColumn('h2u_student_statuses', 'hss_program_code')
            && ! Schema::hasColumn('h2u_student_statuses', 'hss_program')) {
            DB::statement('ALTER TABLE h2u_student_statuses RENAME COLUMN hss_program_code TO hss_program');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('h2u_student_statuses')) {
            return;
        }

        if (Schema::hasColumn('h2u_student_statuses', 'hss_program')
            && ! Schema::hasColumn('h2u_student_statuses', 'hss_program_code')) {
            DB::statement('ALTER TABLE h2u_student_statuses RENAME COLUMN hss_program TO hss_program_code');
        }
    }
};
