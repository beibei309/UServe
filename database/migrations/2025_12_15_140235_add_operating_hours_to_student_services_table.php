<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('student_services', function (Blueprint $table) {
        // specific column for weekly schedule
        $table->json('operating_hours')->nullable(); 
    });
}

public function down()
{
    Schema::table('student_services', function (Blueprint $table) {
        $table->dropColumn('operating_hours');
    });
}
};
