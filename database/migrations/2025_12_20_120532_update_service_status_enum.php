<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- 1. WAJIB TAMBAH NI

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 2. Kita guna Raw SQL sebab nak ubah 'ENUM'
        DB::statement("ALTER TABLE student_services MODIFY COLUMN approval_status ENUM('pending', 'approved', 'rejected', 'suspended') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kalau rollback, kita buang balik status 'suspended' tu
        DB::statement("ALTER TABLE student_services MODIFY COLUMN approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
