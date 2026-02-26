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
        Schema::create('h2u_faqs', function (Blueprint $table) {
            $table->bigIncrements('hfq_id');
            $table->string('hfq_category');
            $table->string('hfq_question');
            $table->text('hfq_answer');
            $table->boolean('hfq_is_active')->default(true);
            $table->integer('hfq_display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_faqs');
    }
};
