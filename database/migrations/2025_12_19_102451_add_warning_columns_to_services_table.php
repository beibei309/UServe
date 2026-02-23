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
        $table->integer('warning_count')->default(0)->after('approval_status');
        $table->text('warning_reason')->nullable()->after('warning_count'); // Column baru untuk message
    });
}

public function down()
{
    Schema::table('student_services', function (Blueprint $table) {
        $table->dropColumn(['warning_count', 'warning_reason']);
    });
}
};
