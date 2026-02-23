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
    Schema::table('student_statuses', function (Blueprint $table) {
        $table->date('graduation_date')->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('student_statuses', function (Blueprint $table) {
        $table->dropColumn('graduation_date');
    });
}
};
