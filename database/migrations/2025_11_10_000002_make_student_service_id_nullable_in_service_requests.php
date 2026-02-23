<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Allow custom service requests without a linked student_service
        DB::statement('ALTER TABLE service_requests MODIFY student_service_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        // Revert to NOT NULL (will fail if NULLs exist, used only for local dev rollback)
        DB::statement('ALTER TABLE service_requests MODIFY student_service_id BIGINT UNSIGNED NOT NULL');
    }
};