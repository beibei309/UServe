<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
  {
    Schema::table('h2u_student_services', function (Blueprint $table) {
        $table->enum('hss_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('hss_is_active');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
   {
    Schema::table('h2u_student_services', function (Blueprint $table) {
        $table->dropColumn('hss_approval_status');
    });
}
};
