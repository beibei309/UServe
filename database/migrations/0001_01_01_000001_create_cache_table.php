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
        Schema::create('h2u_cache', function (Blueprint $table) {
            $table->string('hc_key')->primary();
            $table->mediumText('hc_value');
            $table->integer('hc_expiration');
        });

        Schema::create('h2u_cache_locks', function (Blueprint $table) {
            $table->string('hcl_key')->primary();
            $table->string('hcl_owner');
            $table->integer('hcl_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_cache');
        Schema::dropIfExists('h2u_cache_locks');
    }
};
