<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncSequenceWithTable('h2u_users', 'hu_id');
        $this->syncSequenceWithTable('h2u_student_statuses', 'hss_id');
    }

    public function down(): void
    {
        // No-op. Sequence alignment is a safety operation.
    }

    private function syncSequenceWithTable(string $table, string $column): void
    {
        DB::statement(str_replace(
            ['__TABLE__', '__COLUMN__'],
            [$table, $column],
            <<<'SQL'
DO $$
DECLARE
    seq_name text;
    max_id bigint;
BEGIN
    seq_name := pg_get_serial_sequence('__TABLE__', '__COLUMN__');

    IF seq_name IS NULL THEN
        RETURN;
    END IF;

    EXECUTE format('SELECT COALESCE(MAX(%I), 0) FROM %I', '__COLUMN__', '__TABLE__') INTO max_id;

    EXECUTE format('SELECT setval(%L, %s, %s)', seq_name, GREATEST(max_id, 1), CASE WHEN max_id > 0 THEN 'true' ELSE 'false' END);
END
$$;
SQL
        ));
    }
};
