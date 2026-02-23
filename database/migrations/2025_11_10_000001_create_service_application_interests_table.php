<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Guard against a partially-created table from a previous failed migration run
        // (e.g., due to overly long auto-generated index names in MySQL).
        Schema::dropIfExists('service_application_interests');

        Schema::create('service_application_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_application_id')->constrained('service_applications')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('status', ['interested', 'selected', 'declined'])->default('interested');
            $table->timestamp('selected_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamps();

            $table->unique(['service_application_id', 'student_id'], 'application_student_unique');
            // Use a shorter, explicit index name to avoid MySQL's 64-char identifier limit
            $table->index(['service_application_id', 'status'], 'app_interest_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_application_interests');
    }
};