<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            // Kita tambah column suggested_price (decimal atau float)
            $table->decimal('suggested_price', 8, 2)->nullable()->after('description');
            
            // Decimal(8, 2) bermakna 8 digit, dengan 2 digit di belakang perpuluhan (contoh: 999,999.99)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            // Kalau roll back, kita buang column tu
            $table->dropColumn('suggested_price');
        });
    }
};