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
        $table->json('blocked_slots')->nullable()->after('unavailable_dates');
    });
}

public function down()
{
    Schema::table('student_services', function (Blueprint $table) {
        $table->dropColumn('blocked_slots');
    });
}
};
