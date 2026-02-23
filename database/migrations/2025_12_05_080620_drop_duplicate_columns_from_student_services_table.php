<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            $table->dropColumn(['suggested_price', 'price_range']);
        });
    }

    public function down(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->string('price_range')->nullable();
        });
    }
};
