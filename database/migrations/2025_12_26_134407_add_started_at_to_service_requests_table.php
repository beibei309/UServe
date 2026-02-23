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
        // Tambah column started_at SELEPAS column accepted_at
        $table->timestamp('started_at')->nullable()->after('accepted_at');
    });
}

public function down()
{
    Schema::table('service_requests', function (Blueprint $table) {
        $table->dropColumn('started_at');
    });
}
};
