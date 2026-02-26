<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            // Kita cuma tambah column baru sahaja.
            // JANGAN letak 'after' dan JANGAN letak 'dropColumn'.

            if (!Schema::hasColumn('h2u_users', 'hu_work_experience_message')) {
                $table->text('hu_work_experience_message')->nullable();
            }

            if (!Schema::hasColumn('h2u_users', 'hu_work_experience_file')) {
                $table->string('hu_work_experience_file')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('h2u_users', function (Blueprint $table) {
            if (Schema::hasColumn('h2u_users', 'hu_work_experience_message')) {
                $table->dropColumn('hu_work_experience_message');
            }

            if (Schema::hasColumn('h2u_users', 'hu_work_experience_file')) {
                $table->dropColumn('hu_work_experience_file');
            }

            // Optional: return old column
            // $table->text('hu_work_experience')->nullable();
        });
    }
};
