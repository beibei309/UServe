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
                DB::statement("ALTER TABLE h2u_student_services ALTER COLUMN hss_approval_status TYPE VARCHAR(20)");
                DB::statement("ALTER TABLE h2u_student_services ALTER COLUMN hss_approval_status SET DEFAULT 'pending'");
                DB::statement("ALTER TABLE h2u_student_services DROP CONSTRAINT IF EXISTS h2u_student_services_hss_approval_status_check");
                DB::statement("ALTER TABLE h2u_student_services ADD CONSTRAINT h2u_student_services_hss_approval_status_check CHECK (hss_approval_status IN ('pending','approved','rejected','suspended'))");
            });
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE h2u_student_services MODIFY COLUMN hss_approval_status ENUM('pending', 'approved', 'rejected', 'suspended') DEFAULT 'pending'");
        } else {
            DB::statement("ALTER TABLE h2u_student_services ALTER COLUMN hss_approval_status SET DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::transaction(function () {
                DB::statement("ALTER TABLE student_services DROP CONSTRAINT IF EXISTS student_services_approval_status_check");
                DB::statement("ALTER TABLE h2u_student_services ADD CONSTRAINT h2u_student_services_hss_approval_status_check CHECK (hss_approval_status IN ('pending','approved','rejected'))");
                DB::statement("ALTER TABLE h2u_student_services ALTER COLUMN hss_approval_status SET DEFAULT 'pending'");
            });
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE h2u_student_services MODIFY COLUMN hss_approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        } else {
            DB::statement("ALTER TABLE h2u_student_services ALTER COLUMN hss_approval_status SET DEFAULT 'pending'");
        }
    }
};
