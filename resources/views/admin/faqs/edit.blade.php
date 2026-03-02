@extends('admin.layout')

@section('content')
    <div class="max-w-3xl mx-auto py-10">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">
                {{ isset($faq) ? 'Edit FAQ' : 'Add FAQ' }}
            </h1>
            <a href="{{ route('admin.faqs.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to FAQs
            </a>
        </div>

        <form method="POST"
              action="{{ isset($faq) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}">
            @csrf
            @isset($faq) @method('PUT') @endisset

            <div class="space-y-4">
                <input name="category" placeholder="Category"
                       value="{{ old('category', $faq->hfq_category ?? '') }}"
                       class="w-full border rounded-lg px-4 py-2">

                <input name="question" placeholder="Question"
                       value="{{ old('question', $faq->hfq_question ?? '') }}"
                       class="w-full border rounded-lg px-4 py-2">

                <textarea name="answer" rows="5"
                          class="w-full border rounded-lg px-4 py-2"
                          placeholder="Answer">{{ old('answer', $faq->hfq_answer ?? '') }}</textarea>

                <input name="display_order" type="number"
                       value="{{ old('display_order', $faq->hfq_display_order ?? 0) }}"
                       class="w-full border rounded-lg px-4 py-2">

                <button class="bg-indigo-600 text-white px-6 py-2 rounded-lg">
                    Save
                </button>
            </div>
        </form>
    </div>
@endsection
