@extends('admin.layout')

@section('content')
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                    {{ isset($faq) ? 'Edit FAQ' : 'Create New FAQ' }}
                </h1>
                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">
                    {{ isset($faq) ? 'Update the selected FAQ entry' : 'Add a new frequently asked question' }}
                </p>
            </div>
            <a href="{{ route('admin.faqs.index') }}"
               class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-400 hover:to-gray-500 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-arrow-left"></i>
                Back to FAQs
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl transition-all duration-300" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl transition-all duration-300" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <div>
                        <p class="font-medium mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="rounded-xl shadow-xl border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="p-6 sm:p-8">
                <form method="POST"
                      action="{{ isset($faq) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}"
                      class="space-y-6">
                    @csrf
                    @isset($faq) @method('PUT') @endisset

                    <!-- Category Field -->
                    <div>
                        <label for="category" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                            <i class="fas fa-folder mr-2"></i>Category
                        </label>
                        <input type="text"
                               id="category"
                               name="category"
                               placeholder="e.g., General, Account, Services"
                               value="{{ old('category', $faq->hfq_category ?? '') }}"
                               class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 @error('category') border-red-500 @enderror"
                               style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                               required>
                        @error('category')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Question Field -->
                    <div>
                        <label for="question" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                            <i class="fas fa-question-circle mr-2"></i>Question
                        </label>
                        <input type="text"
                               id="question"
                               name="question"
                               placeholder="Enter the frequently asked question"
                               value="{{ old('question', $faq->hfq_question ?? '') }}"
                               class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 @error('question') border-red-500 @enderror"
                               style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                               required>
                        @error('question')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Answer Field -->
                    <div>
                        <label for="answer" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                            <i class="fas fa-comment-alt mr-2"></i>Answer
                        </label>
                        <textarea id="answer"
                                  name="answer"
                                  rows="6"
                                  placeholder="Provide a detailed and helpful answer"
                                  class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 resize-vertical @error('answer') border-red-500 @enderror"
                                  style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                  required>{{ old('answer', $faq->hfq_answer ?? '') }}</textarea>
                        @error('answer')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Display Order Field -->
                    <div>
                        <label for="display_order" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                            <i class="fas fa-sort-numeric-up mr-2"></i>Display Order
                        </label>
                        <input type="number"
                               id="display_order"
                               name="display_order"
                               placeholder="0"
                               value="{{ old('display_order', $faq->hfq_display_order ?? 0) }}"
                               min="0"
                               class="w-full sm:w-32 rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 @error('display_order') border-red-500 @enderror"
                               style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                        <p class="text-xs mt-1 transition-colors duration-300" style="color: var(--text-muted);">
                            Lower numbers appear first. Use 0 for automatic ordering.
                        </p>
                        @error('display_order')
                            <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit"
                                class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium shadow-lg hover:shadow-xl">
                            <i class="fas fa-save"></i>
                            {{ isset($faq) ? 'Update FAQ' : 'Create FAQ' }}
                        </button>

                        <a href="{{ route('admin.faqs.index') }}"
                           class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-400 hover:to-gray-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 rounded-xl shadow-lg border transition-all duration-300 p-6"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h3 class="font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                <i class="fas fa-lightbulb mr-2"></i>Tips for Writing Great FAQs
            </h3>
            <ul class="space-y-2 text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                    Keep questions clear and specific to what users commonly ask
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                    Provide comprehensive answers that resolve the issue completely
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                    Use simple language that's easy for all users to understand
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-0.5"></i>
                    Organize FAQs into logical categories for better navigation
                </li>
            </ul>
        </div>
    </div>
@endsection
