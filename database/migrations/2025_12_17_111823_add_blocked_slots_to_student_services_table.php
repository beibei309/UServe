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
    Schema::table('h2u_student_services', function (Blueprint $table) {
        $table->json('hss_blocked_slots')->nullable()->after('hss_unavailable_dates');
    });
}

public function down()
{
    Schema::table('h2u_student_services', function (Blueprint $table) {
        $table->dropColumn('hss_blocked_slots');
    });
}
};
