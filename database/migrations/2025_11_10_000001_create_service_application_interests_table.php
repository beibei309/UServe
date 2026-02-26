<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Guard against a partially-created table from a previous failed migration run
        // (e.g., due to overly long auto-generated index names in MySQL).
        Schema::dropIfExists('h2u_service_application_interests');

        Schema::create('h2u_service_application_interests', function (Blueprint $table) {
            $table->bigIncrements('hsai_id');
            $table->foreignId('hsai_service_application_id')->constrained('h2u_service_applications', 'hsa_id')->onDelete('cascade');
            $table->foreignId('hsai_student_id')->constrained('h2u_users', 'hu_id')->onDelete('cascade');
            $table->text('hsai_message')->nullable();
            $table->enum('hsai_status', ['interested', 'selected', 'declined'])->default('interested');
            $table->timestamp('hsai_selected_at')->nullable();
            $table->timestamp('hsai_declined_at')->nullable();
            $table->timestamps();

            $table->unique(['hsai_service_application_id', 'hsai_student_id'], 'application_student_unique');
            // Use a shorter, explicit index name to avoid MySQL's 64-char identifier limit
            $table->index(['hsai_service_application_id', 'hsai_status'], 'app_interest_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_service_application_interests');
    }
};
