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
        // Kita tambah 2 column ni
        $table->integer('warning_count')->default(0)->after('status'); // Simpan bilangan warning (0, 1, 2, 3)
        $table->text('warning_reason')->nullable()->after('warning_count'); // Simpan sebab warning
    });
}

public function down()
{
    Schema::table('student_services', function (Blueprint $table) {
        $table->dropColumn(['warning_count', 'warning_reason']);
    });
}
};
