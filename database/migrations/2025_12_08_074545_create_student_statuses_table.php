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
    Schema::create('h2u_student_statuses', function (Blueprint $table) {
        $table->bigIncrements('hss_id');
        $table->unsignedBigInteger('hss_student_id');   // FK to h2u_users table
        $table->string('hss_matric_no')->nullable();    // (optional)
        $table->string('hss_semester');                 // e.g. "Semester 1 2025"
        $table->string('hss_status');                   // Active / Inactive / Deferred
        $table->date('hss_effective_date');             // When this status starts
        $table->timestamps();

        // Foreign key: when user deleted → remove status
        $table->foreign('hss_student_id')
              ->references('hu_id')
              ->on('h2u_users')
              ->onDelete('cascade');
    });
    }

    public function down()
    {
        Schema::dropIfExists('h2u_student_statuses');
    }
};
