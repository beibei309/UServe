<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('h2u_warnings', function (Blueprint $table) {
        $table->bigIncrements('hw_id');
        $table->foreignId('hw_user_id')->constrained('h2u_users', 'hu_id')->onDelete('cascade'); // Student
        $table->foreignId('hw_service_id')->constrained('h2u_student_services', 'hss_id')->onDelete('cascade'); // Service
        $table->text('hw_reason'); // Mesej warning
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h2u_warnings');
    }
};
