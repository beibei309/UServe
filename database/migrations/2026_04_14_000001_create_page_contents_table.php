<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('h2u_page_contents', function (Blueprint $table) {
            $table->bigIncrements('hpc_id');
            $table->string('hpc_page', 50);
            $table->string('hpc_slug', 100)->unique();
            $table->string('hpc_label', 150);
            $table->enum('hpc_type', ['text', 'textarea', 'image', 'video']);
            $table->longText('hpc_value')->nullable();
            $table->longText('hpc_default')->nullable();
            $table->boolean('hpc_is_active')->default(true);
            $table->timestamps();

            $table->index('hpc_page');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h2u_page_contents');
    }
};
