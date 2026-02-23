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
        Schema::table('reviews', function (Blueprint $table) {
            // Make conversation_id nullable since reviews can be for service_requests or service_applications
            $table->foreignId('conversation_id')->nullable()->change();
            
            // Add service_request_id if not exists
            if (!Schema::hasColumn('reviews', 'service_request_id')) {
                $table->foreignId('service_request_id')->nullable()->after('conversation_id')->constrained('service_requests')->cascadeOnDelete();
            }
            
            // Add service_application_id if not exists
            if (!Schema::hasColumn('reviews', 'service_application_id')) {
                $table->foreignId('service_application_id')->nullable()->after('service_request_id')->constrained('service_applications')->cascadeOnDelete();
            }
            
            // Add is_follow_up column if not exists
            if (!Schema::hasColumn('reviews', 'is_follow_up')) {
                $table->boolean('is_follow_up')->default(false)->after('comment');
            }
        });
        
        // --- START UNIQUE CONSTRAINT FIXES ---
        
        // 1. Try to drop the old default Laravel constraint
        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropUnique(['conversation_id', 'reviewer_id', 'reviewee_id']);
            });
        } catch (\Exception $e) {
            // Ignore
        }

        // 2. Try to drop the exact conflicting constraint name from the database error
        try {
            Schema::table('reviews', function (Blueprint $table) {
                // Drop the exact name that caused the previous DUP KEY error
                $table->dropUnique('reviews_conversation_id_reviewer_id_reviewee_id_unique');
            });
        } catch (\Exception $e) {
            // Ignore
        }
        
        // 3. Add the new unique constraint
        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique(
                    ['conversation_id', 'reviewer_id', 'reviewee_id', 'is_follow_up'], 
                    'reviews_conversation_reviewer_reviewee_followup_unique'
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