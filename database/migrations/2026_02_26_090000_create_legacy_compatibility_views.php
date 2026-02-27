<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $schema = DB::selectOne('SELECT current_schema() AS schema_name')->schema_name;

        $tables = DB::table('information_schema.tables')
            ->where('table_schema', $schema)
            ->where('table_type', 'BASE TABLE')
            ->where('table_name', 'like', 'h2u\_%')
            ->orderBy('table_name')
            ->pluck('table_name');

        foreach ($tables as $tableName) {
            $legacyName = substr($tableName, 4); // strip "h2u_"
            if (!$legacyName) {
                continue;
            }

            $columns = DB::table('information_schema.columns')
                ->where('table_schema', $schema)
                ->where('table_name', $tableName)
                ->orderBy('ordinal_position')
                ->pluck('column_name')
                ->all();

            if (empty($columns)) {
                continue;
            }

            $aliasToColumn = [];
            foreach ($columns as $column) {
                $alias = preg_replace('/^h[a-z]{1,3}_(.+)$/', '$1', $column) ?? $column;
                // If duplicates exist, keep the latest column definition (typically legacy column added later).
                $aliasToColumn[$alias] = $column;
            }

            $selectParts = [];
            foreach ($aliasToColumn as $alias => $column) {
                $quotedColumn = '"' . str_replace('"', '""', $column) . '"';
                $quotedAlias = '"' . str_replace('"', '""', $alias) . '"';
                $selectParts[] = $quotedColumn . ' AS ' . $quotedAlias;
            }

            $quotedView = '"' . str_replace('"', '""', $legacyName) . '"';
            $quotedTable = '"' . str_replace('"', '""', $tableName) . '"';

            DB::statement(sprintf(
                'CREATE OR REPLACE VIEW %s AS SELECT %s FROM %s',
                $quotedView,
                implode(', ', $selectParts),
                $quotedTable
            ));
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $schema = DB::selectOne('SELECT current_schema() AS schema_name')->schema_name;

        $tables = DB::table('information_schema.tables')
            ->where('table_schema', $schema)
            ->where('table_type', 'BASE TABLE')
            ->where('table_name', 'like', 'h2u\_%')
            ->pluck('table_name');

        foreach ($tables as $tableName) {
            $legacyName = substr($tableName, 4);
            if (!$legacyName) {
                continue;
            }

            $quotedView = '"' . str_replace('"', '""', $legacyName) . '"';
            DB::statement('DROP VIEW IF EXISTS ' . $quotedView . ' CASCADE');
        }
    }
};
