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
        Schema::create('h2u_notifications', function (Blueprint $table) {
            $table->uuid('hn_id')->primary();
            $table->string('hn_type');
            $table->morphs('hn_notifiable');
            $table->text('hn_data');
            $table->timestamp('hn_read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_notifications');
    }
};
