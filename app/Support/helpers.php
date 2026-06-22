<?php

if (! function_exists('upsi2u_avatar_url')) {
    function upsi2u_avatar_url(?string $name = 'User'): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            $name = 'User';
        }

        return route('avatar.initials', ['name' => $name]);
    }
}

if (! function_exists('upsi2u_setting')) {
    function upsi2u_setting(string $slug, ?string $fallback = null): ?string
    {
        return \App\Models\PageContent::get($slug, $fallback);
    }
}

if (! function_exists('upsi2u_asset_setting')) {
    function upsi2u_asset_setting(string $slug, string $fallback): string
    {
        $path = trim((string) upsi2u_setting($slug, $fallback));

        if ($path === '') {
            $path = $fallback;
        }

        if (preg_match('/^(https?:)?\/\//', $path) === 1 || str_starts_with($path, 'data:')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}

if (! function_exists('upsi2u_platform_name')) {
    function upsi2u_platform_name(): string
    {
        return upsi2u_setting('settings.platform_name', 'UPSI2u') ?: 'UPSI2u';
    }
}

if (! function_exists('upsi2u_platform_tagline')) {
    function upsi2u_platform_tagline(): string
    {
        return upsi2u_setting('settings.platform_tagline', 'UPSI Service Circle') ?: 'UPSI Service Circle';
    }
}

if (! function_exists('upsi2u_platform_title')) {
    function upsi2u_platform_title(?string $prefix = null): string
    {
        $base = upsi2u_platform_name() . ' | ' . upsi2u_platform_tagline();

        return $prefix ? $prefix . ' | ' . $base : $base;
    }
}

if (! function_exists('upsi2u_platform_logo_url')) {
    function upsi2u_platform_logo_url(): string
    {
        return upsi2u_asset_setting('settings.platform_logo', 'images/upsi2u-logo-generated.png');
    }
}

if (! function_exists('upsi2u_platform_favicon_url')) {
    function upsi2u_platform_favicon_url(): string
    {
        return upsi2u_asset_setting('settings.platform_favicon', 'images/upsi2u-favicon-generated.png');
    }
}

if (! function_exists('upsi2u_institution_logo_url')) {
    function upsi2u_institution_logo_url(): string
    {
        return upsi2u_asset_setting('settings.institution_logo', 'images/upsilogo.png');
    }
}
