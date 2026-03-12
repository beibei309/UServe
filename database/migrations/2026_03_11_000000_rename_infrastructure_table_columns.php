<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rename custom-prefixed columns in Laravel infrastructure tables
 * back to standard names that Laravel's core drivers expect.
 *
 * Affected tables: h2u_sessions, h2u_cache, h2u_cache_locks,
 *                  h2u_jobs, h2u_job_batches, h2u_failed_jobs
 */
return new class extends Migration
{
    private function renameColumnsIfNeeded(string $tableName, array $columnMap): void
    {
        if (! Schema::hasTable($tableName)) {
            return;
        }

        foreach ($columnMap as $from => $to) {
            if (Schema::hasColumn($tableName, $from) && ! Schema::hasColumn($tableName, $to)) {
                Schema::table($tableName, function (Blueprint $table) use ($from, $to) {
                    $table->renameColumn($from, $to);
                });
            }
        }
    }

    public function up(): void
    {
        $this->renameColumnsIfNeeded('h2u_sessions', [
            'hs_id' => 'id',
            'hs_user_id' => 'user_id',
            'hs_ip_address' => 'ip_address',
            'hs_user_agent' => 'user_agent',
            'hs_payload' => 'payload',
            'hs_last_activity' => 'last_activity',
        ]);

        $this->renameColumnsIfNeeded('h2u_cache', [
            'hc_key' => 'key',
            'hc_value' => 'value',
            'hc_expiration' => 'expiration',
        ]);

        $this->renameColumnsIfNeeded('h2u_cache_locks', [
            'hcl_key' => 'key',
            'hcl_owner' => 'owner',
            'hcl_expiration' => 'expiration',
        ]);

        $this->renameColumnsIfNeeded('h2u_jobs', [
            'hj_id' => 'id',
            'hj_queue' => 'queue',
            'hj_payload' => 'payload',
            'hj_attempts' => 'attempts',
            'hj_reserved_at' => 'reserved_at',
            'hj_available_at' => 'available_at',
            'hj_created_at' => 'created_at',
        ]);

        $this->renameColumnsIfNeeded('h2u_job_batches', [
            'hjb_id' => 'id',
            'hjb_name' => 'name',
            'hjb_total_jobs' => 'total_jobs',
            'hjb_pending_jobs' => 'pending_jobs',
            'hjb_failed_jobs' => 'failed_jobs',
            'hjb_failed_job_ids' => 'failed_job_ids',
            'hjb_options' => 'options',
            'hjb_cancelled_at' => 'cancelled_at',
            'hjb_created_at' => 'created_at',
            'hjb_finished_at' => 'finished_at',
        ]);

        $this->renameColumnsIfNeeded('h2u_failed_jobs', [
            'hfj_id' => 'id',
            'hfj_uuid' => 'uuid',
            'hfj_connection' => 'connection',
        ]);
    }

    public function down(): void
    {
        $this->renameColumnsIfNeeded('h2u_sessions', [
            'id' => 'hs_id',
            'user_id' => 'hs_user_id',
            'ip_address' => 'hs_ip_address',
            'user_agent' => 'hs_user_agent',
            'payload' => 'hs_payload',
            'last_activity' => 'hs_last_activity',
        ]);

        $this->renameColumnsIfNeeded('h2u_cache', [
            'key' => 'hc_key',
            'value' => 'hc_value',
            'expiration' => 'hc_expiration',
        ]);

        $this->renameColumnsIfNeeded('h2u_cache_locks', [
            'key' => 'hcl_key',
            'owner' => 'hcl_owner',
            'expiration' => 'hcl_expiration',
        ]);

        $this->renameColumnsIfNeeded('h2u_jobs', [
            'id' => 'hj_id',
            'queue' => 'hj_queue',
            'payload' => 'hj_payload',
            'attempts' => 'hj_attempts',
            'reserved_at' => 'hj_reserved_at',
            'available_at' => 'hj_available_at',
            'created_at' => 'hj_created_at',
        ]);

        $this->renameColumnsIfNeeded('h2u_job_batches', [
            'id' => 'hjb_id',
            'name' => 'hjb_name',
            'total_jobs' => 'hjb_total_jobs',
            'pending_jobs' => 'hjb_pending_jobs',
            'failed_jobs' => 'hjb_failed_jobs',
            'failed_job_ids' => 'hjb_failed_job_ids',
            'options' => 'hjb_options',
            'cancelled_at' => 'hjb_cancelled_at',
            'created_at' => 'hjb_created_at',
            'finished_at' => 'hjb_finished_at',
        ]);

        $this->renameColumnsIfNeeded('h2u_failed_jobs', [
            'id' => 'hfj_id',
            'uuid' => 'hfj_uuid',
            'connection' => 'hfj_connection',
        ]);
    }
};
