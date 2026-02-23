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
    Schema::table('student_services', function (Blueprint $table) {
        // 'session' = Time based (Tutoring)
        // 'task' = Task based (Ironing, Repairs)
        // 'project' = Deadline based (Design)
        $table->string('booking_mode')->default('session')->after('category_id');
    });
}

public function down()
{
    Schema::table('student_services', function (Blueprint $table) {
        $table->dropColumn('booking_mode');
    });
}
};
