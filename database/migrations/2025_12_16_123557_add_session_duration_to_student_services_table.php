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
        // Default 60 mins jika tiada setting
        $table->integer('hss_session_duration')->default(60)->after('hss_operating_hours');
    });
}

public function down()
{
    Schema::table('h2u_student_services', function (Blueprint $table) {
        $table->dropColumn('hss_session_duration');
    });
}
};
