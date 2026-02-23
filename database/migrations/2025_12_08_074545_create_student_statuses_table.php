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
    Schema::create('student_statuses', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('student_id');   // FK to users table
    $table->string('matric_no')->nullable();    // (optional)
    $table->string('semester');                 // e.g. "Semester 1 2025"
    $table->string('status');                   // Active / Inactive / Deferred
    $table->date('effective_date');             // When this status starts
    $table->timestamps();

    // Foreign key: when user deleted → remove status
            $table->foreign('student_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_statuses');
    }
};