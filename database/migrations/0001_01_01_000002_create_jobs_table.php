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
        Schema::create('h2u_jobs', function (Blueprint $table) {
            $table->bigIncrements('hj_id');
            $table->string('hj_queue')->index();
            $table->longText('hj_payload');
            $table->unsignedTinyInteger('hj_attempts');
            $table->unsignedInteger('hj_reserved_at')->nullable();
            $table->unsignedInteger('hj_available_at');
            $table->unsignedInteger('hj_created_at');
        });

        Schema::create('h2u_job_batches', function (Blueprint $table) {
            $table->string('hjb_id')->primary();
            $table->string('hjb_name');
            $table->integer('hjb_total_jobs');
            $table->integer('hjb_pending_jobs');
            $table->integer('hjb_failed_jobs');
            $table->longText('hjb_failed_job_ids');
            $table->mediumText('hjb_options')->nullable();
            $table->integer('hjb_cancelled_at')->nullable();
            $table->integer('hjb_created_at');
            $table->integer('hjb_finished_at')->nullable();
        });

        Schema::create('h2u_failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('hfj_id');
            $table->string('hfj_uuid')->unique();
            $table->text('hfj_connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
