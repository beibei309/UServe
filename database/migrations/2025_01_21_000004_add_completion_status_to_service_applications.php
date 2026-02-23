<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_applications', function (Blueprint $table) {
            $table->boolean('customer_completed')->default(false)->after('status');
            $table->boolean('provider_completed')->default(false)->after('customer_completed');
            $table->timestamp('customer_completed_at')->nullable()->after('provider_completed');
            $table->timestamp('provider_completed_at')->nullable()->after('customer_completed_at');
            $table->timestamp('fully_completed_at')->nullable()->after('provider_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_applications', function (Blueprint $table) {
            $table->dropColumn([
                'customer_completed',
                'provider_completed', 
                'customer_completed_at',
                'provider_completed_at',
                'fully_completed_at'
            ]);
        });
    }
};