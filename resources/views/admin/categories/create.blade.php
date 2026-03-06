@extends('admin.layout')

@section('content')
<div class="px-4 sm:px-6">

<div class="max-w-6xl mx-auto">

    {{-- Back Navigation --}}
    <div class="mb-8">
        <a href="{{ route('admin.categories.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-300 hover:shadow-lg"
           style="background-color: var(--bg-secondary); color: var(--text-secondary); border: 1px solid var(--border-color);">
            <i class="fas fa-arrow-left"></i>
            <span class="font-medium">Back to Categories</span>
        </a>
    </div>

    {{-- Page Header --}}
    <div class="rounded-xl shadow-xl border transition-all duration-300 mb-8" 
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 px-6 py-6 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plus-circle text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-2xl">Create New Category</h1>
                    <p class="text-purple-100 text-sm">Add a new service category to the UServe platform</p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            {{-- BASIC INFORMATION SECTION --}}
            <div class="xl:col-span-2 rounded-xl shadow-xl border transition-all duration-300" 
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-info-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-xl">Basic Information</h2>
                            <p class="text-emerald-100 text-sm">Category name, description, and settings</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="space-y-6">
                        
                        {{-- Category Name --}}
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                <i class="fas fa-list text-emerald-500"></i>
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <select name="name" 
                                   class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" 
                                   style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" 
                                   required>
                                <option value="" disabled {{ empty(old('name')) ? 'selected' : '' }}>Select a category...</option>
                                <option value="Academic Tutoring" {{ old('name') == 'Academic Tutoring' ? 'selected' : '' }}>Academic Tutoring</option>
                                <option value="Programming & Tech" {{ old('name') == 'Programming & Tech' ? 'selected' : '' }}>Programming & Tech</option>
                                <option value="Design & Creative" {{ old('name') == 'Design & Creative' ? 'selected' : '' }}>Design & Creative</option>
                                <option value="Housechores" {{ old('name') == 'Housechores' ? 'selected' : '' }}>Housechores</option>
                                <option value="Event Planning" {{ old('name') == 'Event Planning' ? 'selected' : '' }}>Event Planning</option>
                                <option value="Runner & Errands" {{ old('name') == 'Runner & Errands' ? 'selected' : '' }}>Runner & Errands</option>
                            </select>
                            <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">Choose from existing UServe service categories</p>
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                <i class="fas fa-link text-blue-500"></i>
                                URL Slug
                            </label>
                            <input type="text" name="slug" value="{{ old('slug') }}" 
                                   class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono text-sm"
                                   style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" 
                                   placeholder="category-url-slug">
                            <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">Leave empty to auto-generate from category name</p>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                <i class="fas fa-align-left text-purple-500"></i>
                                Description
                            </label>
                            <textarea name="description" rows="4" 
                                      class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none"
                                      style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" 
                                      placeholder="Describe what this category is for...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Color and Status --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            
                            {{-- Color Picker --}}
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                    <i class="fas fa-palette text-pink-500"></i>
                                    Category Color
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="color" value="{{ old('color', '#3b82f6') }}" 
                                           class="h-12 w-16 cursor-pointer border rounded-xl p-1 transition-all duration-300"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                    <div class="flex-1">
                                        <p class="text-xs font-medium transition-colors duration-300" style="color: var(--text-primary);" id="colorValue">{{ old('color', '#3b82f6') }}</p>
                                        <p class="text-xs transition-colors duration-300" style="color: var(--text-muted);">Used for category badges and UI elements</p>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Active Status --}}
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                    <i class="fas fa-toggle-on text-green-500"></i>
                                    Status
                                </label>
                                <div class="p-4 rounded-xl border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input id="is_active" name="is_active" type="checkbox" value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}
                                               class="w-5 h-5 text-emerald-600 border-2 rounded focus:ring-emerald-500">
                                        <div>
                                            <span class="text-sm font-semibold transition-colors duration-300" style="color: var(--text-primary);">Active Category</span>
                                            <p class="text-xs transition-colors duration-300" style="color: var(--text-muted);">Users can select this category for their services</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ICON SELECTION SECTION --}}
            <div class="rounded-xl shadow-xl border transition-all duration-300" 
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-amber-600 to-orange-700 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-icons text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-xl">Icon Selection</h2>
                            <p class="text-amber-100 text-sm">Choose a visual icon for this category</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Icon Preview & Input --}}
                    <div class="mb-6">
                        <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                            <i class="fas fa-eye text-amber-500"></i>
                            Current Icon
                        </label>
                        
                        <div class="flex items-center gap-4 p-4 rounded-xl border transition-all duration-300" 
                             style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="w-16 h-16 flex items-center justify-center rounded-xl"
                                 style="background-color: {{ old('color', '#3b82f6') }}20; color: {{ old('color', '#3b82f6') }};">
                                <i id="previewIcon" class="{{ old('icon', 'fa fa-folder') }} text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <input type="text" id="iconInput" name="icon" 
                                       value="{{ old('icon') }}" 
                                       placeholder="fa fa-user"
                                       class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent font-mono text-sm"
                                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">Enter FontAwesome class name or click an icon below</p>
                            </div>
                        </div>
                    </div>

                    {{-- Icon Grid --}}
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">
                            <i class="fas fa-th text-amber-500"></i>
                            Available Icons
                        </label>
                        
                        <div class="p-4 rounded-xl border transition-all duration-300" 
                             style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="grid grid-cols-6 gap-4 max-h-80 overflow-y-auto custom-scrollbar p-3">
                                @foreach($icons as $icon)
                                <div data-icon="{{ $icon }}"
                                     class="icon-option cursor-pointer h-10 w-10 flex items-center justify-center rounded-lg border-2 border-transparent hover:border-amber-500 transition-all duration-300 group"
                                     style="background-color: var(--bg-secondary); color: var(--text-secondary);" 
                                     title="{{ $icon }}">
                                    <i class="{{ $icon }} group-hover:text-amber-500 transition-colors duration-300"></i>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUBMIT SECTION --}}
        <div class="rounded-xl shadow-xl border transition-all duration-300 mt-8" 
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-plus text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg transition-colors duration-300" style="color: var(--text-primary);">Ready to create?</h3>
                            <p class="text-sm transition-colors duration-300" style="color: var(--text-muted);">Review your category information before creating</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <a href="{{ route('admin.categories.index') }}"
                           class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl shadow-lg transition-all duration-300 flex items-center gap-2 font-semibold">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-700 hover:from-purple-500 hover:to-indigo-600 text-white rounded-xl shadow-lg transition-all duration-300 flex items-center gap-2 font-semibold">
                            <i class="fas fa-plus"></i>
                            Create Category
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: var(--bg-primary);
        border-radius: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #f59e0b, #d97706);
        border-radius: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #d97706, #b45309);
    }

    .icon-option.selected {
        border-color: #f59e0b !important;
        background-color: #fef3c7 !important;
        color: #f59e0b !important;
    }
</style>

@endsection

@section('scripts')
    <div id="adminCategoriesFormConfig"></div>
    <script>
        // Color value display
        const colorInput = document.querySelector('input[name="color"]');
        const colorValue = document.getElementById('colorValue');
        const previewIcon = document.getElementById('previewIcon');
        
        if (colorInput && colorValue) {
            colorInput.addEventListener('input', function() {
                colorValue.textContent = this.value;
                // Update icon preview color
                const preview = previewIcon.closest('div');
                if (preview) {
                    preview.style.backgroundColor = this.value + '20';
                    preview.style.color = this.value;
                }
            });
        }
        
        // Icon selection handling
        const iconInput = document.getElementById('iconInput');
        const iconOptions = document.querySelectorAll('.icon-option');
        
        if (iconInput && iconOptions.length > 0) {
            // Update preview when input changes
            iconInput.addEventListener('input', function() {
                if (previewIcon) {
                    previewIcon.className = this.value || 'fa fa-folder';
                }
                // Remove selection from all options
                iconOptions.forEach(option => option.classList.remove('selected'));
                // Add selection to matching option
                const matchingOption = document.querySelector(`[data-icon="${this.value}"]`);
                if (matchingOption) {
                    matchingOption.classList.add('selected');
                }
            });
            
            // Handle icon option clicks
            iconOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const iconClass = this.dataset.icon;
                    iconInput.value = iconClass;
                    if (previewIcon) {
                        previewIcon.className = iconClass;
                    }
                    // Update selection
                    iconOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
            
            // Set initial selection
            const currentIcon = iconInput.value;
            if (currentIcon) {
                const currentOption = document.querySelector(`[data-icon="${currentIcon}"]`);
                if (currentOption) {
                    currentOption.classList.add('selected');
                }
            }
        }
    </script>
@endsection
