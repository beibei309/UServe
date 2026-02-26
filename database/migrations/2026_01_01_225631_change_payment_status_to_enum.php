<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::transaction(function () {
                DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status TYPE VARCHAR(32)");
                DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status SET DEFAULT 'unpaid'");
                DB::statement("ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_hsr_payment_status_check");
                DB::statement("ALTER TABLE h2u_service_requests ADD CONSTRAINT h2u_service_requests_hsr_payment_status_check CHECK (hsr_payment_status IN ('unpaid','paid','verification_status'))");
            });
        } elseif ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE h2u_service_requests
                MODIFY hsr_payment_status
                ENUM('unpaid', 'paid', 'verification_status')
                NOT NULL
                DEFAULT 'unpaid'
            ");
        } else {
            DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status SET DEFAULT 'unpaid'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::transaction(function () {
                    DB::statement("ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_hsr_payment_status_check");
                    DB::statement("ALTER TABLE h2u_service_requests ADD CONSTRAINT h2u_service_requests_hsr_payment_status_check CHECK (hsr_payment_status IN ('unpaid','paid'))");
                    DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status SET DEFAULT 'unpaid'");
            });
        } elseif ($driver === 'mysql') {
            DB::statement("
                    ALTER TABLE h2u_service_requests
                    MODIFY hsr_payment_status
                    VARCHAR(255)
                    NOT NULL
                    DEFAULT 'unpaid'
            ");
        } else {
                DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status SET DEFAULT 'unpaid'");
        }
    }
};
