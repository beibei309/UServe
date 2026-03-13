@extends('admin.layout')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Manage Terms & Privacy</h1>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border px-4 py-3 text-sm" style="border-color: #22c55e; color: #16a34a; background-color: rgba(34, 197, 94, 0.1);">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6">
            <div class="rounded-xl shadow-xl border transition-all duration-300 p-6" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <h2 class="text-lg font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Terms of Service</h2>

                <form method="POST" action="{{ route('admin.legal-pages.update', $termsPage) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium mb-1 transition-colors duration-300" style="color: var(--text-secondary);">Title</label>
                        <input type="text" name="title" value="{{ old('title', $termsPage->hlp_title) }}"
                               class="w-full rounded-lg border px-3 py-2 text-sm transition-colors duration-300"
                               style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 transition-colors duration-300" style="color: var(--text-secondary);">Content</label>
                        <div id="terms-editor" class="bg-white rounded-lg"></div>
                        <input type="hidden" name="content" id="terms-content-input" value="{{ old('content', $termsPage->hlp_content) }}">
                        <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">Use toolbar for headings, bold, bullets, numbered lists, and links.</p>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm" style="color: var(--text-secondary);">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $termsPage->hlp_is_active) ? 'checked' : '' }}>
                        Active
                    </label>

                    <div>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium rounded shadow transition-all duration-300">
                            Save Terms
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-xl shadow-xl border transition-all duration-300 p-6" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <h2 class="text-lg font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Privacy Policy</h2>

                <form method="POST" action="{{ route('admin.legal-pages.update', $privacyPage) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium mb-1 transition-colors duration-300" style="color: var(--text-secondary);">Title</label>
                        <input type="text" name="title" value="{{ old('title', $privacyPage->hlp_title) }}"
                               class="w-full rounded-lg border px-3 py-2 text-sm transition-colors duration-300"
                               style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 transition-colors duration-300" style="color: var(--text-secondary);">Content</label>
                        <div id="privacy-editor" class="bg-white rounded-lg"></div>
                        <input type="hidden" name="content" id="privacy-content-input" value="{{ old('content', $privacyPage->hlp_content) }}">
                        <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">Use toolbar for headings, bold, bullets, numbered lists, and links.</p>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm" style="color: var(--text-secondary);">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $privacyPage->hlp_is_active) ? 'checked' : '' }}>
                        Active
                    </label>

                    <div>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium rounded shadow transition-all duration-300">
                            Save Privacy Policy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        function initLegalEditor(editorId, inputId) {
            const editorElement = document.getElementById(editorId);
            const hiddenInput = document.getElementById(inputId);
            if (!editorElement || !hiddenInput) {
                return;
            }

            const quill = new Quill(editorElement, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link', 'clean']
                    ]
                }
            });

            quill.root.innerHTML = hiddenInput.value || '';

            const form = editorElement.closest('form');
            if (form) {
                form.addEventListener('submit', function () {
                    hiddenInput.value = quill.root.innerHTML;
                });
            }
        }

        initLegalEditor('terms-editor', 'terms-content-input');
        initLegalEditor('privacy-editor', 'privacy-content-input');
    </script>
@endsection
