<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reviews ALTER COLUMN conversation_id DROP NOT NULL');
        } else {
            Schema::table('reviews', function (Blueprint $table) {
                $table->foreignId('conversation_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reviews ALTER COLUMN conversation_id SET NOT NULL');
        } else {
            Schema::table('reviews', function (Blueprint $table) {
                $table->foreignId('conversation_id')->nullable(false)->change();
            });
        }
    }
};
