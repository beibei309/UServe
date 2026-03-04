@extends('admin.layout')

@section('content')
<div class="px-4 sm:px-6">
    
<div class="max-w-5xl mx-auto rounded-lg shadow-md p-8 transition-all duration-300"
     style="background-color: var(--bg-secondary);">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 border-b pb-4 transition-colors duration-300"
         style="border-color: var(--border-color);">
        <h2 class="text-xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
            {{ isset($category) ? 'Edit Category' : 'Create New Category' }}
        </h2>
        <a href="{{ route('admin.categories.index') }}" 
           class="hover:text-cyan-400 text-sm flex items-center gap-1 transition-colors duration-300"
           style="color: var(--text-secondary);">
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
                    <label class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-primary);">Category Name</label>
                    <input type="text" name="name" value="{{ old('name', $category->hc_name ?? '') }}" 
                           class="w-full px-4 py-2 border rounded-lg outline-none transition-colors duration-300" 
                           style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);"
                           onfocus="this.style.borderColor = '#06b6d4'; this.style.boxShadow = '0 0 0 2px rgba(6, 182, 212, 0.2)';" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-primary);">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->hc_slug ?? '') }}" 
                           class="w-full px-4 py-2 border rounded-lg transition-colors duration-300"
                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-primary);">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-2 border rounded-lg outline-none transition-colors duration-300"
                              style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);"
                              onfocus="this.style.borderColor = '#06b6d4'; this.style.boxShadow = '0 0 0 2px rgba(6, 182, 212, 0.2)';">{{ old('description', $category->hc_description ?? '') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-primary);">Color</label>
                        <input type="color" name="color" value="{{ old('color', $category->hc_color ?? '#3b82f6') }}" 
                               class="h-10 w-20 cursor-pointer border rounded-lg p-1 transition-colors duration-300"
                               style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" 
                               {{ old('is_active', $category->hc_is_active ?? true) ? 'checked' : '' }}
                               class="h-5 w-5 text-cyan-600 rounded">
                        <label for="is_active" class="ml-2 text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">Is Active?</label>
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-xl border transition-all duration-300"
                 style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                <label class="block text-sm font-bold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Select Icon</label>
                
                <div class="mb-4 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center transition-colors duration-300" style="color: var(--text-muted);">
                        <i id="previewIcon" class="{{ old('icon', $category->hc_icon ?? 'fa fa-folder') }}"></i>
                    </span>
                    <input type="text" id="iconInput" name="icon" 
                           value="{{ old('icon', $category->hc_icon ?? '') }}" 
                           placeholder="fa fa-user"
                           class="w-full pl-10 px-4 py-2 border rounded-lg outline-none text-sm transition-colors duration-300"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                           onfocus="this.style.borderColor = '#06b6d4'; this.style.boxShadow = '0 0 0 2px rgba(6, 182, 212, 0.2)';">
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
                         class="cursor-pointer h-10 w-10 flex items-center justify-center rounded border hover:text-cyan-500 transition-all duration-300"
                         style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-secondary);"
                         onmouseover="this.style.borderColor = '#06b6d4'; this.style.backgroundColor = 'var(--hover-bg)';"
                         onmouseout="this.style.borderColor = 'var(--border-color)'; this.style.backgroundColor = 'var(--bg-secondary)';">
                        <i class="{{ $icon }}"></i>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t mt-6 transition-colors duration-300"
             style="border-color: var(--border-color);">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white rounded-lg text-sm font-medium shadow-md transition-all duration-300">
                <i class="fa-solid fa-save mr-2"></i>
                {{ isset($category) ? 'Update Category' : 'Save Category' }}
            </button>
        </div>

    </form>
</div>

</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: var(--bg-primary);
        border-radius: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: var(--text-muted);
        border-radius: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #06b6d4;
    }
</style>

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