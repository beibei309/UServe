<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // 1️⃣ Move payment_status after status
            $table->enum('payment_status', ['unpaid', 'paid', 'verification_status'])
                ->default('unpaid')
                ->after('status')
                ->change();

            // 2️⃣ Add payment_proof after payment_status
            $table->string('payment_proof')->nullable()->after('payment_status');
        });
    }

    public function down()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('payment_proof');

            // optional revert order if you want
        });
    }
};
