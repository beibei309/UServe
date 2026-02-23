<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('title'); // or after any column you like
        });
    }

    public function down(): void
    {
        Schema::table('student_services', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
