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
        DB::statement("
            ALTER TABLE service_requests
            MODIFY payment_status 
            ENUM('unpaid', 'paid', 'verification_status')
            NOT NULL
            DEFAULT 'unpaid'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE service_requests
            MODIFY payment_status 
            VARCHAR(255)
            NOT NULL
            DEFAULT 'unpaid'
        ");
    }
};
