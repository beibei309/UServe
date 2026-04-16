@extends('admin.layout')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    @php
        $locationParts = function ($slug) {
            $parts = explode('.', (string) $slug);
            $section = $parts[1] ?? 'content';
            $field = $parts[2] ?? $parts[1] ?? 'text';
            return ucfirst(str_replace('_', ' ', $section)) . ' > ' . ucfirst(str_replace('_', ' ', $field));
        };

        $fieldHint = function ($slug) {
            if ($slug === 'welcome.hero_title_highlight_color') {
                return 'Use a HEX value like #818cf8.';
            }

            return null;
        };

        $textBlocks = $groupedBlocks['text'];

        if ($page === 'welcome') {
            $priority = [
                'welcome.hero_title_line_1' => 0,
                'welcome.hero_title_highlight' => 1,
                'welcome.hero_title_highlight_color' => 2,
                'welcome.hero_title_line_2' => 3,
            ];

            $textBlocks = $textBlocks
                ->reject(fn ($block) => $block->hpc_slug === 'welcome.hero_title')
                ->sortBy(function ($block) use ($priority) {
                    return ($priority[$block->hpc_slug] ?? 1000) * 100000 + (int) $block->hpc_id;
                })
                ->values();
        }
    @endphp

    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold capitalize" style="color: var(--text-primary);">Edit {{ str_replace('_', ' ', $page) }}</h1>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Update text, descriptions, and media without touching Blade files.</p>
        </div>

        <a href="{{ route('admin.page-content.index') }}" class="text-sm font-semibold px-4 py-2 rounded-lg border" style="border-color: var(--border-color); color: var(--text-secondary);">
            Back
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg border px-4 py-3 text-sm" style="border-color: #22c55e; color: #16a34a; background-color: rgba(34, 197, 94, 0.1);">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 rounded-xl border px-4 py-3 text-sm" style="border-color: #bae6fd; color: #0f172a; background-color: #f0f9ff;">
        <p class="font-semibold mb-1">Editing guide for non-technical admins</p>
        <p>Write plain text only. You do not need to write HTML code. Each field shows where it appears on the page.</p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-xl shadow-xl border p-6" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h2 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Text Blocks</h2>

            <form method="POST" action="{{ route('admin.page-content.update', $page) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @forelse ($textBlocks as $block)
                    <div class="rounded-lg border p-4" style="border-color: var(--border-color);">
                        <label class="block text-sm font-semibold mb-1" style="color: var(--text-primary);">{{ $block->hpc_label }}</label>
                        <p class="text-xs mb-2" style="color: var(--text-muted);">Appears at: {{ $locationParts($block->hpc_slug) }}</p>
                        @php($hint = $fieldHint($block->hpc_slug))
                        @if (!empty($hint))
                            <p class="text-xs mb-2" style="color: #0369a1;">{{ $hint }}</p>
                        @endif

                        @if ($block->hpc_type === 'textarea')
                            <textarea name="blocks[{{ $block->hpc_slug }}]" rows="4" maxlength="2000" data-char-count="count-{{ $block->hpc_slug }}"
                                class="w-full rounded-lg border px-3 py-2 text-sm" style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">{{ old('blocks.' . $block->hpc_slug, $block->hpc_value) }}</textarea>
                            <p id="count-{{ $block->hpc_slug }}" class="text-xs mt-1" style="color: var(--text-muted);"></p>
                        @else
                            <input type="text" name="blocks[{{ $block->hpc_slug }}]" value="{{ old('blocks.' . $block->hpc_slug, $block->hpc_value) }}"
                                class="w-full rounded-lg border px-3 py-2 text-sm" style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                        @endif

                        <div class="mt-3">
                            <button type="submit" form="reset-text-{{ str_replace('.', '-', $block->hpc_slug) }}" class="text-xs px-3 py-1.5 rounded border" style="border-color: var(--border-color); color: var(--text-secondary);">
                                Reset to Default
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border px-4 py-3 text-sm" style="border-color: #bae6fd; color: #0f172a; background-color: #f0f9ff;">
                        No text blocks are configured for this page yet.
                    </div>
                @endforelse

                @if ($textBlocks->count() > 0)
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 transition-all duration-300">
                        Save Text Blocks
                    </button>
                @endif
            </form>

            @foreach ($textBlocks as $block)
                <form id="reset-text-{{ str_replace('.', '-', $block->hpc_slug) }}" method="POST" action="{{ route('admin.page-content.reset', $block->hpc_slug) }}" class="hidden">
                    @csrf
                </form>
            @endforeach
        </div>

        @if ($groupedBlocks['media']->count() > 0)
            <div class="rounded-xl shadow-xl border p-6" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <h2 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Media Blocks</h2>

                <div class="space-y-5">
                    @foreach ($groupedBlocks['media'] as $block)
                        <div class="rounded-lg border p-4" style="border-color: var(--border-color);">
                            <label class="block text-sm font-semibold mb-1" style="color: var(--text-primary);">{{ $block->hpc_label }}</label>
                            <p class="text-xs mb-3" style="color: var(--text-muted);">{{ $block->hpc_slug }}</p>

                            <div class="mb-3 rounded border p-2" style="border-color: var(--border-color); background-color: var(--bg-primary);">
                                @if ($block->hpc_type === 'image')
                                    <img src="{{ asset($block->hpc_value ?: $block->hpc_default) }}" alt="{{ $block->hpc_label }}" class="max-h-40 rounded">
                                @else
                                    <video controls class="max-h-48 rounded w-full">
                                        <source src="{{ asset($block->hpc_value ?: $block->hpc_default) }}" type="video/mp4">
                                    </video>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.page-content.upload-media', $block->hpc_slug) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2 mb-2">
                                @csrf
                                <input type="file" name="file" class="text-xs" required>
                                <button type="submit" class="text-xs px-3 py-1.5 rounded text-white bg-indigo-600 hover:bg-indigo-700">Replace File</button>
                            </form>

                            <form method="POST" action="{{ route('admin.page-content.reset', $block->hpc_slug) }}">
                                @csrf
                                <button type="submit" class="text-xs px-3 py-1.5 rounded border" style="border-color: var(--border-color); color: var(--text-secondary);">
                                    Reset to Default
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    document.querySelectorAll('textarea[data-char-count]').forEach(function (textarea) {
        var counter = document.getElementById(textarea.dataset.charCount);
        if (!counter) return;

        var renderCount = function () {
            counter.textContent = textarea.value.length + ' characters';
        };

        textarea.addEventListener('input', renderCount);
        renderCount();
    });
})();
</script>
@endsection
