<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('h2u_service_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('hsa_service_id')->nullable()->after('hsa_user_id');
            $table->foreign('hsa_service_id')->references('hss_id')->on('h2u_student_services')->nullOnDelete();
            $table->unsignedBigInteger('hsa_conversation_id')->nullable()->after('hsa_service_id');
            $table->foreign('hsa_conversation_id')->references('hc_id')->on('h2u_conversations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('h2u_service_applications', function (Blueprint $table) {
            $table->dropForeign(['hsa_service_id']);
            $table->dropForeign(['hsa_conversation_id']);
            $table->dropColumn(['hsa_service_id', 'hsa_conversation_id']);
        });
    }
};
