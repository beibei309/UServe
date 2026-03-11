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
    public function up(): void
    {
        // h2u_sessions
        Schema::table('h2u_sessions', function (Blueprint $table) {
            $table->renameColumn('hs_id', 'id');
            $table->renameColumn('hs_user_id', 'user_id');
            $table->renameColumn('hs_ip_address', 'ip_address');
            $table->renameColumn('hs_user_agent', 'user_agent');
            $table->renameColumn('hs_payload', 'payload');
            $table->renameColumn('hs_last_activity', 'last_activity');
        });

        // h2u_cache
        Schema::table('h2u_cache', function (Blueprint $table) {
            $table->renameColumn('hc_key', 'key');
            $table->renameColumn('hc_value', 'value');
            $table->renameColumn('hc_expiration', 'expiration');
        });

        // h2u_cache_locks
        Schema::table('h2u_cache_locks', function (Blueprint $table) {
            $table->renameColumn('hcl_key', 'key');
            $table->renameColumn('hcl_owner', 'owner');
            $table->renameColumn('hcl_expiration', 'expiration');
        });

        // h2u_jobs
        Schema::table('h2u_jobs', function (Blueprint $table) {
            $table->renameColumn('hj_id', 'id');
            $table->renameColumn('hj_queue', 'queue');
            $table->renameColumn('hj_payload', 'payload');
            $table->renameColumn('hj_attempts', 'attempts');
            $table->renameColumn('hj_reserved_at', 'reserved_at');
            $table->renameColumn('hj_available_at', 'available_at');
            $table->renameColumn('hj_created_at', 'created_at');
        });

        // h2u_job_batches
        Schema::table('h2u_job_batches', function (Blueprint $table) {
            $table->renameColumn('hjb_id', 'id');
            $table->renameColumn('hjb_name', 'name');
            $table->renameColumn('hjb_total_jobs', 'total_jobs');
            $table->renameColumn('hjb_pending_jobs', 'pending_jobs');
            $table->renameColumn('hjb_failed_jobs', 'failed_jobs');
            $table->renameColumn('hjb_failed_job_ids', 'failed_job_ids');
            $table->renameColumn('hjb_options', 'options');
            $table->renameColumn('hjb_cancelled_at', 'cancelled_at');
            $table->renameColumn('hjb_created_at', 'created_at');
            $table->renameColumn('hjb_finished_at', 'finished_at');
        });

        // h2u_failed_jobs (only the prefixed ones, some columns already had standard names)
        Schema::table('h2u_failed_jobs', function (Blueprint $table) {
            $table->renameColumn('hfj_id', 'id');
            $table->renameColumn('hfj_uuid', 'uuid');
            $table->renameColumn('hfj_connection', 'connection');
        });
    }

    public function down(): void
    {
        // h2u_sessions
        Schema::table('h2u_sessions', function (Blueprint $table) {
            $table->renameColumn('id', 'hs_id');
            $table->renameColumn('user_id', 'hs_user_id');
            $table->renameColumn('ip_address', 'hs_ip_address');
            $table->renameColumn('user_agent', 'hs_user_agent');
            $table->renameColumn('payload', 'hs_payload');
            $table->renameColumn('last_activity', 'hs_last_activity');
        });

        // h2u_cache
        Schema::table('h2u_cache', function (Blueprint $table) {
            $table->renameColumn('key', 'hc_key');
            $table->renameColumn('value', 'hc_value');
            $table->renameColumn('expiration', 'hc_expiration');
        });

        // h2u_cache_locks
        Schema::table('h2u_cache_locks', function (Blueprint $table) {
            $table->renameColumn('key', 'hcl_key');
            $table->renameColumn('owner', 'hcl_owner');
            $table->renameColumn('expiration', 'hcl_expiration');
        });

        // h2u_jobs
        Schema::table('h2u_jobs', function (Blueprint $table) {
            $table->renameColumn('id', 'hj_id');
            $table->renameColumn('queue', 'hj_queue');
            $table->renameColumn('payload', 'hj_payload');
            $table->renameColumn('attempts', 'hj_attempts');
            $table->renameColumn('reserved_at', 'hj_reserved_at');
            $table->renameColumn('available_at', 'hj_available_at');
            $table->renameColumn('created_at', 'hj_created_at');
        });

        // h2u_job_batches
        Schema::table('h2u_job_batches', function (Blueprint $table) {
            $table->renameColumn('id', 'hjb_id');
            $table->renameColumn('name', 'hjb_name');
            $table->renameColumn('total_jobs', 'hjb_total_jobs');
            $table->renameColumn('pending_jobs', 'hjb_pending_jobs');
            $table->renameColumn('failed_jobs', 'hjb_failed_jobs');
            $table->renameColumn('failed_job_ids', 'hjb_failed_job_ids');
            $table->renameColumn('options', 'hjb_options');
            $table->renameColumn('cancelled_at', 'hjb_cancelled_at');
            $table->renameColumn('created_at', 'hjb_created_at');
            $table->renameColumn('finished_at', 'hjb_finished_at');
        });

        // h2u_failed_jobs
        Schema::table('h2u_failed_jobs', function (Blueprint $table) {
            $table->renameColumn('id', 'hfj_id');
            $table->renameColumn('uuid', 'hfj_uuid');
            $table->renameColumn('connection', 'hfj_connection');
        });
    }
};
