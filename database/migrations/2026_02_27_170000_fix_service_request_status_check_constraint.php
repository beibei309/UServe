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
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $allowedStatuses = "'pending','accepted','rejected','in_progress','waiting_payment','completed','cancelled','disputed','approved'";

        DB::statement('ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_hsr_status_check');
        DB::statement('ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_status_check');
        DB::statement("ALTER TABLE h2u_service_requests ADD CONSTRAINT h2u_service_requests_hsr_status_check CHECK (hsr_status IN ({$allowedStatuses}))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $baseStatuses = "'pending','accepted','rejected','in_progress','completed','cancelled'";

        DB::statement('ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_hsr_status_check');
        DB::statement('ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_status_check');
        DB::statement("ALTER TABLE h2u_service_requests ADD CONSTRAINT h2u_service_requests_hsr_status_check CHECK (hsr_status IN ({$baseStatuses}))");
    }
};
