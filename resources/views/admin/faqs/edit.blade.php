@extends('admin.layout')

@section('content')
    <div class="max-w-3xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">
            {{ isset($faq) ? 'Edit FAQ' : 'Add FAQ' }}
        </h1>

        <form method="POST"
              action="{{ isset($faq) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}">
            @csrf
            @isset($faq) @method('PUT') @endisset

            <div class="space-y-4">
                <input name="category" placeholder="Category"
                       value="{{ old('category', $faq->category ?? '') }}"
                       class="w-full border rounded-lg px-4 py-2">

                <input name="question" placeholder="Question"
                       value="{{ old('question', $faq->question ?? '') }}"
                       class="w-full border rounded-lg px-4 py-2">

                <textarea name="answer" rows="5"
                          class="w-full border rounded-lg px-4 py-2"
                          placeholder="Answer">{{ old('answer', $faq->answer ?? '') }}</textarea>

                <input name="display_order" type="number"
                       value="{{ old('display_order', $faq->display_order ?? 0) }}"
                       class="w-full border rounded-lg px-4 py-2">

                <button class="bg-indigo-600 text-white px-6 py-2 rounded-lg">
                    Save
                </button>
            </div>
        </form>
    </div>
@endsection