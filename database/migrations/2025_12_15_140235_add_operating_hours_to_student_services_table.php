<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('h2u_student_services', function (Blueprint $table) {
        // specific column for weekly schedule
        $table->json('hss_operating_hours')->nullable();
    });
}

public function down()
{
    Schema::table('h2u_student_services', function (Blueprint $table) {
        $table->dropColumn('hss_operating_hours');
    });
}
};
