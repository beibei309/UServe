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
        // We make it nullable so existing rows don't break
        $table->unsignedBigInteger('hsr_reported_by')->nullable()->after('hsr_dispute_reason');
    });
}

public function down()
{
    Schema::table('h2u_service_requests', function (Blueprint $table) {
        $table->dropColumn('hsr_reported_by');
    });
}
};
