<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('reviews', 'service_request_id')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        Schema::table('reviews', function (Blueprint $table) use ($driver) {
            $column = $table->foreignId('service_request_id')->nullable()->constrained('service_requests')->cascadeOnDelete();

            if ($driver === 'mysql') {
                $column->after('conversation_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('reviews', 'service_request_id')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            try {
                $table->dropForeign(['service_request_id']);
            } catch (\Throwable $e) {
                // ignore constraint drop errors
            }

            $table->dropColumn('service_request_id');
        });
    }
};
