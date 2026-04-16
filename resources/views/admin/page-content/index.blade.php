@extends('admin.layout')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Page Content Management</h1>
        <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">Manage editable content blocks for public pages and site settings.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg border px-4 py-3 text-sm" style="border-color: #22c55e; color: #16a34a; background-color: rgba(34, 197, 94, 0.1);">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($pages as $page)
            <div class="rounded-xl shadow-xl border transition-all duration-300 p-6" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <h2 class="text-lg font-semibold mb-2 capitalize" style="color: var(--text-primary);">{{ str_replace('_', ' ', $page->hpc_page) }}</h2>
                <p class="text-xs mb-4" style="color: var(--text-muted);">{{ $page->block_count }} editable blocks</p>

                <a href="{{ route('admin.page-content.edit', $page->hpc_page) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 transition-all duration-300">
                    Edit Content
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
