<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('h2u_service_requests', function (Blueprint $table) {
            $table->time('hsr_selected_time')->nullable()->after('hsr_selected_dates');
        });
    }

    public function down(): void
    {
        Schema::table('h2u_service_requests', function (Blueprint $table) {
            $table->dropColumn('hsr_selected_time');
        });
    }
};
