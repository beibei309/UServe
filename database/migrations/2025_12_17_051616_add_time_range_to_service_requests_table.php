next<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('h2u_service_requests', function (Blueprint $table) {
            // optional: kekalkan hsr_selected_time kalau nak backward compatibility
            $table->time('hsr_start_time')->after('hsr_selected_dates')->nullable();
            $table->time('hsr_end_time')->after('hsr_start_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('h2u_service_requests', function (Blueprint $table) {
            $table->dropColumn(['hsr_start_time', 'hsr_end_time']);
        });
    }
};
