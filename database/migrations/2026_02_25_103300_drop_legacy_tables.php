<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Remove legacy columns from reviews
        Schema::table('reviews', function (Blueprint $table) {
            foreach (['conversation_id', 'service_application_id', 'is_follow_up'] as $column) {
                if (Schema::hasColumn('reviews', $column)) {
                    // Force drop of columns, Postgres will handle cascade if needed via table drop step
                    $table->dropColumn($column);
                }
            }
        });
            // 1. Remove legacy columns from h2u_reviews
            Schema::table('h2u_reviews', function (Blueprint $table) {
                foreach (["hr_conversation_id", "hr_service_application_id", "hr_is_follow_up"] as $column) {
                    if (Schema::hasColumn('h2u_reviews', $column)) {
                        // Force drop of columns, Postgres will handle cascade if needed via table drop step
                        $table->dropColumn($column);
                    }
                }
            });

        // 2. Drop the legacy tables with CASCADE
        $tablesToDrop = [
            'messages',
            'chat_requests',
            'service_application_interests',
            'service_applications',
            'conversations',
            'service_packages',
            'warnings'
        ];

        foreach ($tablesToDrop as $table) {
            try {
                // CASCADE automatically drops all dependent objects (like foreign keys in other tables)
                DB::statement("DROP TABLE IF EXISTS $table CASCADE");
            } catch (\Exception $e) {
                // Log or ignore
            }
        }
            // 2. Drop the legacy tables with CASCADE (new naming convention)
            $tablesToDrop = [
                'h2u_messages',
                'h2u_chat_requests',
                'h2u_service_application_interests',
                'h2u_service_applications',
                'h2u_conversations',
                'h2u_service_packages',
                'h2u_warnings'
            ];

            foreach ($tablesToDrop as $table) {
                try {
                    // CASCADE automatically drops all dependent objects (like foreign keys in other tables)
                    DB::statement("DROP TABLE IF EXISTS $table CASCADE");
                } catch (\Exception $e) {
                    // Log or ignore
                }
            }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action as these represent a permanent cleanup of legacy systems.
    }
};
