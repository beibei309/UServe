@props(['content'])

@php
    $trimmed = trim((string) $content);
    $htmlContent = '';

    if ($trimmed === '') {
        $htmlContent = '<p>This page is currently being prepared.</p>';
    } else {
        // If it already contains HTML tags, we assume it's formatted.
        // strip_tags check is similar to the one previously in the controller.
        if ($trimmed !== strip_tags($trimmed)) {
            $htmlContent = $trimmed;
        } else {
            // Otherwise, apply the same line-by-line formatting logic.
            $lines = preg_split('/\R+/', $trimmed) ?: [];
            $chunks = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;

                // Simple check for numbered headings (e.g., "1. Introduction")
                if (preg_match('/^\d+\.\s+.+$/', $line)) {
                    $chunks[] = '<h2>' . e($line) . '</h2>';
                } else {
                    $chunks[] = '<p>' . e($line) . '</p>';
                }
            }
            $htmlContent = implode("\n", $chunks);
        }
    }
@endphp

<div class="legal-content-wrapper">
    {!! $htmlContent !!}
</div>
