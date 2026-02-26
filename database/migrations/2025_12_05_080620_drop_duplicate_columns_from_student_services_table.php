<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->dropColumn(['hss_suggested_price', 'hss_price_range']);
        });
    }

    public function down(): void
    {
        Schema::table('h2u_student_services', function (Blueprint $table) {
            $table->decimal('hss_suggested_price', 10, 2)->nullable();
            $table->string('hss_price_range')->nullable();
        });
    }
};
