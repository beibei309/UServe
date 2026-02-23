<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_applications', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('student_services')->nullOnDelete()->after('user_id');
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->nullOnDelete()->after('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_applications', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['conversation_id']);
            $table->dropColumn(['service_id', 'conversation_id']);
        });
    }
};