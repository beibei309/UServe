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
                DB::statement("ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_hsr_payment_status_check");
                DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status TYPE VARCHAR(32)");
                DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status SET DEFAULT 'unpaid'");
                DB::statement("ALTER TABLE h2u_service_requests ADD CONSTRAINT h2u_service_requests_hsr_payment_status_check CHECK (hsr_payment_status IN ('unpaid','paid','verification_status'))");
            });

            Schema::table('h2u_service_requests', function (Blueprint $table) {
                $table->string('hsr_payment_proof')->nullable()->after('hsr_payment_status');
            });
        } elseif ($driver === 'mysql') {
            Schema::table('h2u_service_requests', function (Blueprint $table) {
                $table->enum('hsr_payment_status', ['unpaid', 'paid', 'verification_status'])
                    ->default('unpaid')
                    ->after('hsr_status')
                    ->change();

                $table->string('hsr_payment_proof')->nullable()->after('hsr_payment_status');
            });
        } else {
            Schema::table('h2u_service_requests', function (Blueprint $table) {
                $table->string('hsr_payment_status', 32)->default('unpaid')->change();
                $table->string('hsr_payment_proof')->nullable();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            Schema::table('service_requests', function (Blueprint $table) {
                if (Schema::hasColumn('service_requests', 'payment_proof')) {
                    $table->dropColumn('payment_proof');
                }
            });

            DB::transaction(function () {
                DB::statement("ALTER TABLE service_requests DROP CONSTRAINT IF EXISTS service_requests_payment_status_check");
                DB::statement("ALTER TABLE service_requests ADD CONSTRAINT service_requests_payment_status_check CHECK (payment_status IN ('unpaid','paid'))");
                DB::statement("ALTER TABLE service_requests ALTER COLUMN payment_status SET DEFAULT 'unpaid'");
            });
        } else {
            Schema::table('service_requests', function (Blueprint $table) {
                if (Schema::hasColumn('service_requests', 'payment_proof')) {
                    $table->dropColumn('payment_proof');
                }
                $table->string('payment_status', 32)->default('unpaid')->change();
            });
        }
    }
};
