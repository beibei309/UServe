<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('h2u_service_applications', function (Blueprint $table) {
            $table->boolean('hsa_customer_completed')->default(false)->after('hsa_status');
            $table->boolean('hsa_provider_completed')->default(false)->after('hsa_customer_completed');
            $table->timestamp('hsa_customer_completed_at')->nullable()->after('hsa_provider_completed');
            $table->timestamp('hsa_provider_completed_at')->nullable()->after('hsa_customer_completed_at');
            $table->timestamp('hsa_fully_completed_at')->nullable()->after('hsa_provider_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('h2u_service_applications', function (Blueprint $table) {
            $table->dropColumn([
                'hsa_customer_completed',
                'hsa_provider_completed',
                'hsa_customer_completed_at',
                'hsa_provider_completed_at',
                'hsa_fully_completed_at'
            ]);
        });
    }
};
