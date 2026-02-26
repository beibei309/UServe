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
        Schema::create('h2u_admins', function (Blueprint $table) {
        $table->bigIncrements('ha_id');
        $table->string('ha_name');
        $table->string('ha_email')->unique();
        $table->string('ha_password'); // stored as plain text (your preference)
        $table->string('ha_role')->default('admin'); // admin or superadmin
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_admins');
    }
};
