<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Allow custom service requests without a linked student_service
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE h2u_service_requests ALTER COLUMN hsr_student_service_id DROP NOT NULL');
        } elseif ($driver === 'mysql') {
            DB::statement('ALTER TABLE h2u_service_requests MODIFY hsr_student_service_id BIGINT UNSIGNED NULL');
        } else {
            DB::statement('ALTER TABLE h2u_service_requests ALTER COLUMN hsr_student_service_id DROP NOT NULL');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        // Revert to NOT NULL (will fail if NULLs exist, used only for local dev rollback)
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE h2u_service_requests ALTER COLUMN hsr_student_service_id SET NOT NULL');
        } elseif ($driver === 'mysql') {
            DB::statement('ALTER TABLE h2u_service_requests MODIFY hsr_student_service_id BIGINT UNSIGNED NOT NULL');
        } else {
            DB::statement('ALTER TABLE h2u_service_requests ALTER COLUMN hsr_student_service_id SET NOT NULL');
        }
    }
};
