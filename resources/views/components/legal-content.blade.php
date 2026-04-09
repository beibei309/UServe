@props(['content'])

<div class="legal-content-wrapper">
    {!! \App\Support\LegalContentFormatter::toHtml($content) !!}
</div>
