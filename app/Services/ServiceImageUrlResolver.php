<?php

namespace App\Services;

use Illuminate\Support\Str;

class ServiceImageUrlResolver
{
    public function resolveCardImageUrl(?string $path, ?string $serviceTitle): string
    {
        $fallback = 'https://ui-avatars.com/api/?name='.urlencode($serviceTitle ?? 'Service');

        if (! $path) {
            return $fallback;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    public function resolveGeneralImageUrl(?string $path, ?string $fallback = null): string
    {
        $resolvedFallback = $fallback ?? 'https://via.placeholder.com/400x300?text=Service+Image';

        if (! $path) {
            return $resolvedFallback;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Str::startsWith($path, 'services/')) {
            return asset('storage/'.$path);
        }

        if (file_exists(public_path('storage/'.$path))) {
            return asset('storage/'.$path);
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return $resolvedFallback;
    }
}
