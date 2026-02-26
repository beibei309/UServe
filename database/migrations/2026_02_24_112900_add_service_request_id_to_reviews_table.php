<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('h2u_reviews', 'hr_service_request_id')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        Schema::table('h2u_reviews', function (Blueprint $table) use ($driver) {
            $column = $table->foreignId('hr_service_request_id')->nullable()->constrained('h2u_service_requests', 'hsr_id')->cascadeOnDelete();

            if ($driver === 'mysql') {
                $column->after('hr_conversation_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('h2u_reviews', 'hr_service_request_id')) {
            return;
        }

        Schema::table('h2u_reviews', function (Blueprint $table) {
            try {
                $table->dropForeign(['hr_service_request_id']);
            } catch (\Throwable $e) {
                // ignore constraint drop errors
            }

            $table->dropColumn('hr_service_request_id');
        });
    }
};
