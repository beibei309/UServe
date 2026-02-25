<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Status values used by the application workflow.
        $allowedStatuses = "'pending','accepted','rejected','in_progress','waiting_payment','completed','cancelled','disputed','approved'";

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE service_requests DROP CONSTRAINT IF EXISTS service_requests_status_check');
            DB::statement("ALTER TABLE service_requests ADD CONSTRAINT service_requests_status_check CHECK (status IN ({$allowedStatuses}))");
            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM({$allowedStatuses}) NOT NULL DEFAULT 'pending'");
            return;
        }

        // Fallback for other drivers: no-op to avoid incompatible SQL.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        $baseStatuses = "'pending','accepted','rejected','in_progress','completed','cancelled'";

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE service_requests DROP CONSTRAINT IF EXISTS service_requests_status_check');
            DB::statement("ALTER TABLE service_requests ADD CONSTRAINT service_requests_status_check CHECK (status IN ({$baseStatuses}))");
            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM({$baseStatuses}) NOT NULL DEFAULT 'pending'");
            return;
        }

        // Fallback for other drivers: no-op.
    }
};
