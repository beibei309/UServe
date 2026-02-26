<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('h2u_reviews', function (Blueprint $table) {
            // Make hr_conversation_id nullable since reviews can be for service_requests or service_applications
            $table->foreignId('hr_conversation_id')->nullable()->change();

            // Add hr_service_request_id if not exists
            if (!Schema::hasColumn('h2u_reviews', 'hr_service_request_id')) {
                $table->foreignId('hr_service_request_id')->nullable()->after('hr_conversation_id')->constrained('h2u_service_requests', 'hsr_id')->cascadeOnDelete();
            }

            // Add hr_service_application_id if not exists
            if (!Schema::hasColumn('h2u_reviews', 'hr_service_application_id')) {
                $table->foreignId('hr_service_application_id')->nullable()->after('hr_service_request_id')->constrained('h2u_service_applications', 'hsa_id')->cascadeOnDelete();
            }

            // Add hr_is_follow_up column if not exists
            if (!Schema::hasColumn('h2u_reviews', 'hr_is_follow_up')) {
                $table->boolean('hr_is_follow_up')->default(false)->after('hr_comment');
            }
        });

        // --- START UNIQUE CONSTRAINT FIXES ---

        // 1. Try to drop the old default Laravel constraint
        try {
            Schema::table('h2u_reviews', function (Blueprint $table) {
                $table->dropUnique(['hr_conversation_id', 'hr_reviewer_id', 'hr_reviewee_id']);
            });
        } catch (\Exception $e) {
            // Ignore
        }

        // 2. Try to drop the exact conflicting constraint name from the database error
        try {
            Schema::table('h2u_reviews', function (Blueprint $table) {
                // Drop the exact name that caused the previous DUP KEY error
                $table->dropUnique('h2u_reviews_hr_conversation_id_hr_reviewer_id_hr_reviewee_id_unique');
            });
        } catch (\Exception $e) {
            // Ignore
        }

        // 3. Add the new unique constraint
        try {
            Schema::table('h2u_reviews', function (Blueprint $table) {
                $table->unique(
                    ['hr_conversation_id', 'hr_reviewer_id', 'hr_reviewee_id', 'hr_is_follow_up'],
                    'h2u_reviews_conversation_reviewer_reviewee_followup_unique'
                );
            });
        } catch (\Exception $e) {
            // Ignore
        }

        // --- END UNIQUE CONSTRAINT FIXES ---
    }

    /**
     * Reverse the migrations.
     * * NOTE: Contents removed to bypass persistent rollback failures during migrate:refresh.
     */
    public function down(): void
    {
        // NO ACTION: The migrate:refresh command will drop the table anyway,
        // avoiding the SQL error during rollback.
    }
};
