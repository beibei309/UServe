<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('h2u_reports', function (Blueprint $table) {
            $table->bigIncrements('hrp_id');
            $table->unsignedBigInteger('hrp_reporter_id');
            $table->foreign('hrp_reporter_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->unsignedBigInteger('hrp_target_user_id');
            $table->foreign('hrp_target_user_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->string('hrp_reason');
            $table->text('hrp_details')->nullable();
            $table->enum('hrp_status', ['open', 'warning', 'banned', 'resolved', 'rejected'])->default('open')->index();
            $table->text('hrp_action_taken')->nullable();
            $table->timestamp('hrp_resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_reports');
    }
};
