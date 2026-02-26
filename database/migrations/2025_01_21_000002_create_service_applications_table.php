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
        Schema::create('h2u_service_applications', function (Blueprint $table) {
            $table->bigIncrements('hsa_id');
            $table->unsignedBigInteger('hsa_user_id');
            $table->foreign('hsa_user_id')->references('hu_id')->on('h2u_users')->onDelete('cascade');
            $table->string('hsa_service_type');
            $table->string('hsa_title');
            $table->text('hsa_description');
            $table->string('hsa_budget_range')->nullable();
            $table->string('hsa_timeline');
            $table->json('hsa_contact_methods')->nullable();
            $table->enum('hsa_status', ['open', 'closed', 'completed'])->default('open');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hsa_status', 'created_at']);
            $table->index(['hsa_user_id', 'hsa_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_service_applications');
    }
};
