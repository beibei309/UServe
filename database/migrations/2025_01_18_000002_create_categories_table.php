<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('h2u_categories', function (Blueprint $table) {
            $table->bigIncrements('hc_id');
            $table->string('hc_name');
            $table->string('hc_slug')->unique();
            $table->text('hc_description')->nullable();
            $table->text('hc_image_path')->nullable();
            $table->text('hc_color')->nullable();
            $table->boolean('hc_is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_categories');
    }
};
