@extends('admin.layout')
@section('content')
    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Manage FAQs</h1>
            <a href="{{ route('admin.faqs.create') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium rounded shadow transition-all duration-300 whitespace-nowrap">
                <i class="fa-solid fa-plus text-xs"></i> Add FAQ
            </a>
        </div>

        @foreach ($faqs as $category => $items)
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-3 transition-colors duration-300" style="color: var(--text-secondary);">{{ $category }}</h2>

                <div class="rounded-xl shadow-xl border transition-all duration-300 divide-y overflow-hidden"
                     style="background-color: var(--bg-secondary); border-color: var(--border-color); --tw-divide-opacity: 1; divide-color: var(--border-color);">
                    @foreach ($items as $faq)
                        <div class="p-4 flex flex-col lg:flex-row justify-between items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $faq->hfq_question }}</p>
                                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">
                                    {{ Str::limit(strip_tags($faq->hfq_answer), 120) }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <form method="POST" action="{{ route('admin.faqs.toggle', $faq) }}" class="toggle-form inline-flex" data-faq-id="{{ $faq->hfq_id }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold toggle-btn transition-all duration-200
                                        {{ $faq->hfq_is_active ? 'bg-green-100 hover:bg-green-200 text-green-700' : 'bg-gray-100 hover:bg-gray-200 text-gray-600' }}"
                                        data-active="{{ $faq->hfq_is_active ? '1' : '0' }}">
                                        {{ $faq->hfq_is_active ? 'Active' : 'Hidden' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="inline-flex items-center justify-center w-8 h-8 bg-cyan-100 hover:bg-cyan-200 text-cyan-700 rounded-lg text-xs font-semibold transition-all duration-200" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="delete-form inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-semibold transition-all duration-200 delete-faq-btn" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

@endsection

@section('scripts')
    <div id="adminModuleFaqsIndexConfig"
        data-csrf-token="{{ csrf_token() }}"
        data-success-message="{{ session('success') }}"></div>
    <script src="{{ asset('js/admin-faqs-index.js') }}"></script>
@endsection
