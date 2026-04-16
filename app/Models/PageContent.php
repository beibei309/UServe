<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PageContent extends Model
{
    protected $table = 'h2u_page_contents';
    protected $primaryKey = 'hpc_id';

    protected $fillable = [
        'hpc_page',
        'hpc_slug',
        'hpc_label',
        'hpc_type',
        'hpc_value',
        'hpc_default',
        'hpc_is_active',
    ];

    protected function casts(): array
    {
        return [
            'hpc_is_active' => 'boolean',
        ];
    }

    private static ?array $runtimeCache = null;

    public static function clearRuntimeCache(): void
    {
        static::$runtimeCache = null;
    }

    public static function get(string $slug, ?string $fallback = null): ?string
    {
        $blocks = static::allBySlug();
        $block = $blocks[$slug] ?? null;

        if (! $block) {
            return $fallback;
        }

        $isSettingsBlock = ($block['hpc_page'] ?? '') === 'settings';
        if (! $isSettingsBlock && ! ($block['hpc_is_active'] ?? true)) {
            return $fallback ?? ($block['hpc_default'] ?? null);
        }

        $value = $block['hpc_value'] ?? null;
        if ($value === null || $value === '') {
            return $fallback ?? ($block['hpc_default'] ?? null);
        }

        return $value;
    }

    private static function allBySlug(): array
    {
        if (! Schema::hasTable('h2u_page_contents')) {
            return [];
        }

        if (static::$runtimeCache !== null) {
            return static::$runtimeCache;
        }

        static::$runtimeCache = static::query()
            ->get(['hpc_page', 'hpc_slug', 'hpc_value', 'hpc_default', 'hpc_is_active'])
            ->mapWithKeys(fn (PageContent $block) => [$block->hpc_slug => $block->toArray()])
            ->all();

        return static::$runtimeCache;
    }
}
