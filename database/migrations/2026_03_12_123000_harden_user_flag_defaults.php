<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('h2u_users')) {
            return;
        }

        $this->applyBooleanHardening('hu_is_available');
        $this->applyBooleanHardening('hu_is_verified');
        $this->applyBooleanHardening('helper_status');
        $this->applyBooleanHardening('hu_is_blocked');
        $this->applyBooleanHardening('hu_is_suspended');
        $this->applyBooleanHardening('hu_is_blacklisted');

        $this->applyNumericHardening('hu_warning_count');
        $this->applyNumericHardening('hu_reports_count');
    }

    public function down(): void
    {
        // Intentionally no-op: this migration enforces defensive defaults for production safety.
    }

    private function applyBooleanHardening(string $column): void
    {
        if (! Schema::hasColumn('h2u_users', $column)) {
            return;
        }

        DB::statement("ALTER TABLE h2u_users ALTER COLUMN {$column} SET DEFAULT false");
        DB::statement("UPDATE h2u_users SET {$column} = false WHERE {$column} IS NULL");
        DB::statement("ALTER TABLE h2u_users ALTER COLUMN {$column} SET NOT NULL");
    }

    private function applyNumericHardening(string $column): void
    {
        if (! Schema::hasColumn('h2u_users', $column)) {
            return;
        }

        DB::statement("ALTER TABLE h2u_users ALTER COLUMN {$column} SET DEFAULT 0");
        DB::statement("UPDATE h2u_users SET {$column} = 0 WHERE {$column} IS NULL");
        DB::statement("ALTER TABLE h2u_users ALTER COLUMN {$column} SET NOT NULL");
    }
};
