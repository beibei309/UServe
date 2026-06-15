<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AvatarController extends Controller
{
    public function initials(Request $request)
    {
        $name = trim((string) $request->query('name', 'User'));
        $name = $name !== '' ? $name : 'User';

        $words = collect(preg_split('/\s+/', $name) ?: [])
            ->filter()
            ->take(2)
            ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');

        $initials = $words !== '' ? $words : 'U';
        $hue = abs(crc32($name)) % 360;
        $background = "hsl({$hue}, 72%, 42%)";

        $safeInitials = e($initials);
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160" role="img" aria-label="{$safeInitials}">
  <rect width="160" height="160" rx="80" fill="{$background}"/>
  <text x="50%" y="53%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="Inter, Arial, sans-serif" font-size="58" font-weight="700">{$safeInitials}</text>
</svg>
SVG;

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=604800');
    }
}
