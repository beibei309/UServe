<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            $table->string('status')->default('available')->after('suggested_price');
            $table->string('price_range')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            $table->dropColumn(['status', 'price_range']);
        });
    }
};