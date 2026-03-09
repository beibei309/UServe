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
        Schema::table('h2u_seller_points', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['hsp_service_request_id']);
            
            // Make the column nullable
            $table->unsignedBigInteger('hsp_service_request_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('hsp_service_request_id')->references('hsr_id')->on('h2u_service_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h2u_seller_points', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['hsp_service_request_id']);
            
            // Make the column not nullable again
            $table->unsignedBigInteger('hsp_service_request_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('hsp_service_request_id')->references('hsr_id')->on('h2u_service_requests')->onDelete('cascade');
        });
    }
};
