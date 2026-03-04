@extends('admin.layout')

@section('content')
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6 transition-colors duration-300" style="color: var(--text-primary);">
            {{ isset($faq) ? 'Edit FAQ' : 'Add FAQ' }}
        </h1>

        <form method="POST"
              action="{{ isset($faq) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}"
              class="p-6 rounded-lg shadow-xl border transition-all duration-300"
              style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            @csrf
            @isset($faq) @method('PUT') @endisset

            <div class="space-y-6">
                <div>
                    <label for="category" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Category</label>
                    <input id="category" name="category" placeholder="Enter FAQ category"
                           value="{{ old('category', $faq->hfq_category ?? '') }}"
                           class="w-full border rounded-lg px-4 py-2 transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                           style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <div>
                    <label for="question" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Question</label>
                    <input id="question" name="question" placeholder="Enter the FAQ question"
                           value="{{ old('question', $faq->hfq_question ?? '') }}"
                           class="w-full border rounded-lg px-4 py-2 transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                           style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <div>
                    <label for="answer" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Answer</label>
                    <textarea id="answer" name="answer" rows="5"
                              class="w-full border rounded-lg px-4 py-2 transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                              style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);"
                              placeholder="Enter the detailed answer">{{ old('answer', $faq->hfq_answer ?? '') }}</textarea>
                </div>

                <div>
                    <label for="display_order" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Display Order</label>
                    <input id="display_order" name="display_order" type="number" placeholder="0"
                           value="{{ old('display_order', $faq->hfq_display_order ?? 0) }}"
                           class="w-full border rounded-lg px-4 py-2 transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                           style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-6 py-2 rounded-lg transition-all duration-300 font-medium">
                        {{ isset($faq) ? 'Update FAQ' : 'Create FAQ' }}
                    </button>
                    <a href="{{ route('admin.faqs.index') }}" class="px-6 py-2 border rounded-lg transition-all duration-300"
                       style="color: var(--text-secondary); border-color: var(--border-color);"
                       onmouseover="this.style.backgroundColor = 'var(--hover-bg)';"
                       onmouseout="this.style.backgroundColor = 'transparent';">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
