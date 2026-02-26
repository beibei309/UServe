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
        Schema::table('h2u_users', function (Blueprint $table) {
            if (!Schema::hasColumn('h2u_users', 'hu_verification_document_path')) {
                $table->string('hu_verification_document_path')->nullable()->after('hu_verification_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            $table->dropColumn('hu_verification_document_path');
        });
    }
};
