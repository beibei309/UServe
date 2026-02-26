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
        // Tambah column hsr_started_at SELEPAS column hsr_accepted_at
        $table->timestamp('hsr_started_at')->nullable()->after('hsr_accepted_at');
    });
}

public function down()
{
    Schema::table('h2u_service_requests', function (Blueprint $table) {
        $table->dropColumn('hsr_started_at');
    });
}
};
