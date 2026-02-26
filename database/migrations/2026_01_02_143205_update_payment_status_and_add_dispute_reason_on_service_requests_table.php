<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::transaction(function () {
                DB::statement("ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_hsr_payment_status_check");
                DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status TYPE VARCHAR(32)");
                DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status SET DEFAULT 'unpaid'");
                DB::statement("ALTER TABLE h2u_service_requests ADD CONSTRAINT h2u_service_requests_hsr_payment_status_check CHECK (hsr_payment_status IN ('unpaid','paid','verification_status','dispute'))");
            });

            Schema::table('h2u_service_requests', function (Blueprint $table) {
                $table->text('hsr_dispute_reason')->nullable()->after('hsr_payment_status');
            });
        } elseif ($driver === 'mysql') {
            Schema::table('h2u_service_requests', function (Blueprint $table) {
                $table->enum('hsr_payment_status', [
                    'unpaid',
                    'paid',
                    'verification_status',
                    'dispute'
                ])
                ->default('unpaid')
                ->after('hsr_status')
                ->change();

                $table->text('hsr_dispute_reason')->nullable()->after('hsr_payment_status');
            });
        } else {
            Schema::table('h2u_service_requests', function (Blueprint $table) {
                $table->string('hsr_payment_status', 32)->default('unpaid')->change();
                $table->text('hsr_dispute_reason')->nullable();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            Schema::table('h2u_service_requests', function (Blueprint $table) {
                if (Schema::hasColumn('h2u_service_requests', 'hsr_dispute_reason')) {
                    $table->dropColumn('hsr_dispute_reason');
                }
            });

            DB::transaction(function () {
                DB::statement("ALTER TABLE h2u_service_requests DROP CONSTRAINT IF EXISTS h2u_service_requests_hsr_payment_status_check");
                DB::statement("ALTER TABLE h2u_service_requests ADD CONSTRAINT h2u_service_requests_hsr_payment_status_check CHECK (hsr_payment_status IN ('unpaid','paid','verification_status'))");
                DB::statement("ALTER TABLE h2u_service_requests ALTER COLUMN hsr_payment_status SET DEFAULT 'unpaid'");
            });
        } elseif ($driver === 'mysql') {
            Schema::table('h2u_service_requests', function (Blueprint $table) {
                $table->enum('hsr_payment_status', [
                    'unpaid',
                    'paid',
                    'verification_status'
                ])
                ->default('unpaid')
                ->after('hsr_status')
                ->change();

                if (Schema::hasColumn('h2u_service_requests', 'hsr_dispute_reason')) {
                    $table->dropColumn('hsr_dispute_reason');
                }
            });
        } else {
            Schema::table('h2u_service_requests', function (Blueprint $table) {
                if (Schema::hasColumn('h2u_service_requests', 'hsr_dispute_reason')) {
                    $table->dropColumn('hsr_dispute_reason');
                }
                $table->string('hsr_payment_status', 32)->default('unpaid')->change();
            });
        }
    }
};

