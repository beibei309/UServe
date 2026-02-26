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
        $table->integer('hss_warning_count')->default(0)->after('hss_approval_status');
        $table->text('hss_warning_reason')->nullable()->after('hss_warning_count'); // Column baru untuk message
    });
}

public function down()
{
    Schema::table('h2u_student_services', function (Blueprint $table) {
        $table->dropColumn(['hss_warning_count', 'hss_warning_reason']);
    });
}
};
