<x-guest-layout>
    <div class="min-h-screen bg-slate-50/50 py-8 sm:py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="max-w-4xl mx-auto relative z-10">
            <div class="text-center mb-6 sm:mb-8">
                <span class="inline-flex items-center px-3 py-1 text-xs font-bold tracking-widest uppercase bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100">
                    Legal
                </span>
                <h1 class="mt-4 text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">{{ $legalPage->hlp_title }}</h1>
            </div>

            <div class="upsi-card p-5 sm:p-6 md:p-8">
                @if (isset($isPublished) && !$isPublished)
                    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        This page is currently unpublished. You are viewing the latest draft content.
                    </div>
                @endif

            <style>
                .legal-content h1, .legal-content h2, .legal-content h3 { color: #0f172a; font-weight: 700; }
                .legal-content h1 { font-size: 1.35rem; margin: 1rem 0 0.75rem; }
                .legal-content h2 { font-size: 1.15rem; margin: 1rem 0 0.5rem; }
                .legal-content p { color: #334155; margin-bottom: 0.75rem; line-height: 1.7; }
                .legal-content ul, .legal-content ol { margin: 0.75rem 0 1rem 1.25rem; color: #334155; }
                .legal-content li { margin: 0.25rem 0; }
            </style>

                <div class="legal-content max-w-none">
                    <x-legal-content :content="$legalPage->hlp_content" />
                </div>

                <div class="mt-8 pt-6 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-slate-500">Last updated: {{ date('F j, Y') }}</p>
                    <div>
                        <a href="{{ url()->previous() }}" class="upsi-secondary-action">
                            ← Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
