<x-guest-layout>
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $legalPage->hlp_title }}</h1>

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

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">Last updated: {{ date('F j, Y') }}</p>
                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        ← Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>