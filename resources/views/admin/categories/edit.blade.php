@extends('admin.layout')

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-8">

    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">
            {{ isset($category) ? 'Edit Category' : 'Create New Category' }}
        </h2>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
            &larr; Back to List
        </a>
    </div>

    <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
        @csrf
        @if(isset($category))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none">{{ old('description', $category->description ?? '') }}</textarea>
                </div>

                <div class="flex items-center gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <input type="color" name="color" value="{{ old('color', $category->color ?? '#3b82f6') }}" 
                               class="h-10 w-20 cursor-pointer border rounded-lg p-1">
                    </div>
                    
                    <div class="flex items-center pt-6">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" 
                               {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}
                               class="h-5 w-5 text-blue-600 rounded">
                        <label for="is_active" class="ml-2 text-sm text-gray-900 font-medium">Is Active?</label>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                <label class="block text-sm font-bold text-gray-800 mb-4">Select Icon</label>
                
                <div class="mb-4 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                        <i id="previewIcon" class="{{ old('icon', $category->icon ?? 'fa fa-folder') }}"></i>
                    </span>
                    <input type="text" id="iconInput" name="icon" 
                           value="{{ old('icon', $category->icon ?? '') }}" 
                           placeholder="fa fa-user"
                           class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 outline-none text-sm">
                </div>

                <div class="grid grid-cols-6 gap-2 max-h-64 overflow-y-auto p-1 custom-scrollbar">
                    @php
                        // List of FontAwesome classes
                        $icons = [
                            'fa fa-user', 'fa fa-users', 'fa fa-user-circle', 'fa fa-id-card',
                            'fa fa-home', 'fa fa-building', 'fa fa-store', 'fa fa-laptop-code',
                            'fa fa-graduation-cap', 'fa fa-book', 'fa fa-pencil', 'fa fa-university',
                            'fa fa-cog', 'fa fa-cogs', 'fa fa-wrench', 'fa fa-check-circle',
                            'fa fa-paint-brush', 'fa fa-folder-open', 'fa fa-file', 'fa fa-file-text',
                            'fa fa-calendar', 'fa fa-bell', 'fa fa-envelope',
                            'fa fa-comments', 'fa fa-commenting', 'fa fa-search', 'fa fa-filter',
                            'fa fa-soap', 'fa fa-credit-card', 'fa fa-shopping-cart', 'fa fa-tag',
                            'fa fa-star', 'fa fa-heart', 'fa fa-thumbs-up', 'fa fa-flag',
                            'fa fa-globe', 'fa fa-map-marker', 'fa fa-car', 'fa fa-bicycle'
                        ];
                    @endphp

                    @foreach($icons as $icon)
                    <div onclick="selectIcon('{{ $icon }}')" 
                         class="cursor-pointer h-10 w-10 flex items-center justify-center rounded border bg-white hover:bg-blue-100 hover:text-blue-600 hover:border-blue-400 transition">
                        <i class="{{ $icon }}"></i>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t mt-6">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-md transition">
                {{ isset($category) ? 'Update Category' : 'Save Category' }}
            </button>
        </div>

    </form>
</div>

<script>
    function selectIcon(iconClass) {
        // Update Input
        document.getElementById('iconInput').value = iconClass;
        // Update Preview
        document.getElementById('previewIcon').className = iconClass;
    }

    // Live preview when typing
    document.getElementById('iconInput').addEventListener('input', function() {
        document.getElementById('previewIcon').className = this.value;
    });
</script>
@endsection