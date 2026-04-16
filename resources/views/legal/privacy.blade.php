<x-guest-layout>
    <div class="min-h-screen bg-white py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-50 blur-[120px] opacity-60 pointer-events-none"></div>
        <div class="absolute bottom-[0%] right-[-10%] w-[35%] h-[35%] rounded-full bg-indigo-50 blur-[120px] opacity-60 pointer-events-none"></div>

        <div class="max-w-4xl mx-auto relative z-10">
            <div class="text-center mb-10">
                <span class="inline-flex items-center px-3 py-1 text-xs font-bold tracking-widest uppercase bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100">
                    Legal
                </span>
                <h1 class="mt-4 text-3xl md:text-4xl font-black text-slate-900 tracking-tight">{{ $legalPage->hlp_title }}</h1>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 md:p-8">
                @if (isset($isPublished) && !$isPublished)
                    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        This page is currently unpublished. You are viewing the latest draft content.
                    </div>
                @endif

            <style>
                .legal-content h1, .legal-content h2, .legal-content h3 { color: #0f172a; font-weight: 700; }
                .legal-content h1 { font-size: 1.5rem; margin: 1.2rem 0 0.75rem; }
                .legal-content h2 { font-size: 1.15rem; margin: 1rem 0 0.5rem; }
                .legal-content p { color: #334155; margin-bottom: 0.75rem; line-height: 1.75; }
                .legal-content ul, .legal-content ol { margin: 0.75rem 0 1rem 1.25rem; color: #334155; }
                .legal-content li { margin: 0.25rem 0; }
            </style>

                <div class="legal-content max-w-none">
                    <x-legal-content :content="$legalPage->hlp_content" />
                </div>

                <div class="mt-8 pt-6 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-slate-500">Last updated: {{ date('F j, Y') }}</p>
                    <div>
                        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            ← Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>