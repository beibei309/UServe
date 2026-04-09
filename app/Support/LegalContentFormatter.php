<?php

namespace App\Support;

class LegalContentFormatter
{
    public static function toHtml(?string $content): string
    {
        $trimmed = trim((string) $content);

        if ($trimmed === '') {
            return '<p>This page is currently being prepared.</p>';
        }

        if ($trimmed !== strip_tags($trimmed)) {
            return $trimmed;
        }

        $lines = preg_split('/\R+/', $trimmed) ?: [];
        $chunks = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/^\d+\.\s+.+$/', $line)) {
                $chunks[] = '<h2>' . e($line) . '</h2>';
            } else {
                $chunks[] = '<p>' . e($line) . '</p>';
            }
        }

        return implode("\n", $chunks);
    }
}
