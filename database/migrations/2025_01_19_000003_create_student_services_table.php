<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('h2u_student_services', function (Blueprint $table) {
            $table->bigIncrements('hss_id');
            $table->unsignedBigInteger('hss_user_id');
            $table->foreign('hss_user_id')->references('hu_id')->on('h2u_users')->cascadeOnDelete();
            $table->unsignedBigInteger('hss_category_id')->nullable();
            $table->foreign('hss_category_id')->references('hc_id')->on('h2u_categories')->nullOnDelete();
            $table->string('hss_title');
            $table->text('hss_description')->nullable();
            $table->decimal('hss_suggested_price', 10, 2)->nullable();
            $table->boolean('hss_is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_student_services');
    }
};
