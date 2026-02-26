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
    Schema::table('h2u_student_statuses', function (Blueprint $table) {
        $table->date('hss_graduation_date')->nullable()->after('hss_status');
    });
}

public function down()
{
    Schema::table('h2u_student_statuses', function (Blueprint $table) {
        $table->dropColumn('hss_graduation_date');
    });
}
};
