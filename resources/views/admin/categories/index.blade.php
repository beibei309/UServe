@extends('admin.layout')

@section('content')
    <div class="px-4 sm:px-6 py-4">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Manage Categories</h1>
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium rounded shadow transition-all duration-300 whitespace-nowrap">
                <i class="fa-solid fa-plus text-xs"></i> Add Category
            </a>
        </div>

        {{-- Data Table --}}
        <div class="p-4 rounded-lg shadow-xl border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr style="background-color: var(--bg-tertiary);">
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Icon</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Name / Slug</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Description</th>
                            <th class="py-3 px-3 text-center text-xs font-medium" style="color: var(--text-secondary);">Color</th>
                            <th class="py-3 px-3 text-center text-xs font-medium" style="color: var(--text-secondary);">Status</th>
                            <th class="py-3 px-3 text-center text-xs font-medium" style="color: var(--text-secondary);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                @foreach($categories as $category)
                <tr class="border-b transition-all duration-300" style="border-color: var(--border-color);">
                    
                    {{-- 1. ICON COLUMN --}}
                    <td class="py-4 px-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg shadow-md"
                             style="background-color: {{ $category->hc_color }}; color: white;">
                            <i class="{{ $category->hc_icon ? $category->hc_icon : 'fa-solid fa-folder' }}"></i>
                        </div>
                    </td>

                    {{-- 2. NAME COLUMN --}}
                    <td class="py-4 px-3">
                        <div>
                            <div class="font-semibold text-sm transition-colors duration-300" style="color: var(--text-primary);">{{ $category->hc_name }}</div>
                            <div class="text-xs inline-block px-2 py-1 rounded mt-1 transition-colors duration-300"
                                 style="color: var(--text-secondary); background-color: var(--bg-tertiary);">
                                <i class="fa-solid fa-link text-xs mr-1"></i>/{{ $category->hc_slug }}
                            </div>
                        </div>
                    </td>

                    {{-- 3. DESCRIPTION --}}
                    <td class="py-4 px-3">
                        <p class="text-xs max-w-xs transition-colors duration-300" style="color: var(--text-secondary);">
                            {{ $category->hc_description ?? 'No description available' }}
                        </p>
                    </td>

                    {{-- 4. COLOR --}}
                    <td class="py-4 px-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-6 h-6 rounded border shadow-sm"
                                 style="background-color: {{ $category->hc_color }}; border-color: var(--border-color);"></div>
                            <code class="text-xs px-1 rounded transition-colors duration-300 font-mono"
                                  style="background-color: var(--bg-tertiary); color: var(--text-secondary);">{{ $category->hc_color }}</code>
                        </div>
                    </td>

                    {{-- 5. STATUS --}}
                    <td class="py-4 px-3 text-center">
                        @if($category->hc_is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                Inactive
                            </span>
                        @endif
                    </td>

                    {{-- 6. ACTIONS (Buttons) --}}
                    <td class="py-4 px-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="inline-flex items-center justify-center w-8 h-8 bg-cyan-100 hover:bg-cyan-200 text-cyan-700 rounded-lg text-xs font-semibold transition-all duration-200"
                               title="Edit Category">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            {{-- SWEET ALERT DELETE FORM --}}
                            <form id="delete-form-{{ $category->hc_id }}"
                                  action="{{ route('admin.categories.destroy', $category) }}"
                                  method="POST"
                                  class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        data-category-delete data-category-id="{{ $category->hc_id }}"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-semibold transition-all duration-200"
                                        title="Delete Category">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Pagination --}}
    @if(method_exists($categories, 'links'))
        <div class="mt-4 px-4">
            {{ $categories->links() }}
        </div>
    @endif

</div>

@endsection

@section('scripts')
    <div id="adminCategoriesIndexConfig"
        data-success-message="{{ session('success') }}"
        data-error-message="{{ session('error') }}"></div>
    <script src="{{ asset('js/admin-categories-index.js') }}"></script>
@endsection
