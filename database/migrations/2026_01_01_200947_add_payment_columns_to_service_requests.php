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
        Schema::table('h2u_service_requests', function (Blueprint $table) {
            // Ensure these columns exist
            // hsr_payment_status: 'unpaid', 'paid', 'unpaid_problem'
            if (!Schema::hasColumn('h2u_service_requests', 'hsr_payment_status')) {
                $table->string('hsr_payment_status')->default('unpaid');
            }

            // Ensure status includes 'awaiting_payment'
            // You might need to change your database column type if it's a strict ENUM,
            // otherwise a string column is fine.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_service_requests', function (Blueprint $table) {
            $table->dropColumn('hsr_payment_status');
        });
    }
};
