<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // 🔥 Re-declare enum with new value "dispute"
            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'verification_status',
                'dispute'
            ])
            ->default('unpaid')
            ->after('status')
            ->change();

            // 🔥 Add dispute reason
            $table->text('dispute_reason')->nullable()->after('payment_status');
        });
    }

    public function down()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // revert enum (remove dispute)
            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'verification_status'
            ])
            ->default('unpaid')
            ->after('status')
            ->change();

            $table->dropColumn('dispute_reason');
        });
    }
};

