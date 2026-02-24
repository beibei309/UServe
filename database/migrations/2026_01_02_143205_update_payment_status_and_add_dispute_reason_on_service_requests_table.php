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
                DB::statement("ALTER TABLE service_requests DROP CONSTRAINT IF EXISTS service_requests_payment_status_check");
                DB::statement("ALTER TABLE service_requests ALTER COLUMN payment_status TYPE VARCHAR(32)");
                DB::statement("ALTER TABLE service_requests ALTER COLUMN payment_status SET DEFAULT 'unpaid'");
                DB::statement("ALTER TABLE service_requests ADD CONSTRAINT service_requests_payment_status_check CHECK (payment_status IN ('unpaid','paid','verification_status','dispute'))");
            });

            Schema::table('service_requests', function (Blueprint $table) {
                $table->text('dispute_reason')->nullable()->after('payment_status');
            });
        } elseif ($driver === 'mysql') {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->enum('payment_status', [
                    'unpaid',
                    'paid',
                    'verification_status',
                    'dispute'
                ])
                ->default('unpaid')
                ->after('status')
                ->change();

                $table->text('dispute_reason')->nullable()->after('payment_status');
            });
        } else {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->string('payment_status', 32)->default('unpaid')->change();
                $table->text('dispute_reason')->nullable();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            Schema::table('service_requests', function (Blueprint $table) {
                if (Schema::hasColumn('service_requests', 'dispute_reason')) {
                    $table->dropColumn('dispute_reason');
                }
            });

            DB::transaction(function () {
                DB::statement("ALTER TABLE service_requests DROP CONSTRAINT IF EXISTS service_requests_payment_status_check");
                DB::statement("ALTER TABLE service_requests ADD CONSTRAINT service_requests_payment_status_check CHECK (payment_status IN ('unpaid','paid','verification_status'))");
                DB::statement("ALTER TABLE service_requests ALTER COLUMN payment_status SET DEFAULT 'unpaid'");
            });
        } elseif ($driver === 'mysql') {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->enum('payment_status', [
                    'unpaid',
                    'paid',
                    'verification_status'
                ])
                ->default('unpaid')
                ->after('status')
                ->change();

                if (Schema::hasColumn('service_requests', 'dispute_reason')) {
                    $table->dropColumn('dispute_reason');
                }
            });
        } else {
            Schema::table('service_requests', function (Blueprint $table) {
                if (Schema::hasColumn('service_requests', 'dispute_reason')) {
                    $table->dropColumn('dispute_reason');
                }
                $table->string('payment_status', 32)->default('unpaid')->change();
            });
        }
    }
};

