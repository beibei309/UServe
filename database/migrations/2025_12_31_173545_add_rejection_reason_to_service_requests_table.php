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
        $table->text('hsr_rejection_reason')->nullable()->after('hsr_status');
    });
}

public function down()
{
    Schema::table('h2u_service_requests', function (Blueprint $table) {
        $table->dropColumn('hsr_rejection_reason');
    });
}
};
