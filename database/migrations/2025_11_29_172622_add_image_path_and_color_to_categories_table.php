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
    Schema::table('h2u_categories', function (Blueprint $table) {
        if (!Schema::hasColumn('h2u_categories', 'image_path')) {
            $table->string('image_path')->nullable();
        }
        if (!Schema::hasColumn('h2u_categories', 'color')) {
            $table->string('color')->nullable();
        }
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_categories', function (Blueprint $table) {
        $table->dropColumn(['image_path', 'color']);
        });
    }
};
