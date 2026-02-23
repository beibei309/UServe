@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">

    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manage Categories</h2>
            <p class="text-gray-500 text-sm">Organize your content with categories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" 
           class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-plus"></i> Add Category
        </a>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 font-medium text-gray-900">Icon</th>
                    <th class="px-6 py-4 font-medium text-gray-900">Name / Slug</th>
                    <th class="px-6 py-4 font-medium text-gray-900">Description</th>
                    <th class="px-6 py-4 font-medium text-gray-900">Color</th>
                    <th class="px-6 py-4 font-medium text-gray-900">Status</th>
                    <th class="px-6 py-4 font-medium text-gray-900 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                @foreach($categories as $category)
                <tr class="hover:bg-gray-50 transition duration-150">
                    
                    {{-- 1. ICON COLUMN --}}
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-lg">
                            <i class="{{ $category->icon ? $category->icon : 'fa-solid fa-folder' }}"></i>
                        </div>
                    </td>

                    {{-- 2. NAME COLUMN --}}
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $category->name }}</div>
                        <div class="text-xs text-gray-500 bg-gray-100 inline-block px-2 py-0.5 rounded mt-1">
                            /{{ $category->slug }}
                        </div>
                    </td>

                    {{-- 3. DESCRIPTION --}}
                    <td class="px-6 py-4">
                        <p class="truncate max-w-xs">{{ $category->description ?? 'N/A' }}</p>
                    </td>

                    {{-- 4. COLOR --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full border shadow-sm" style="background-color: {{ $category->color }};"></span>
                        </div>
                    </td>

                    {{-- 5. STATUS --}}
                    <td class="px-6 py-4">
                        @if($category->is_active)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Active</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">Inactive</span>
                        @endif
                    </td>

                    {{-- 6. ACTIONS (Buttons) --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" 
                               class="text-blue-600 hover:text-blue-900 transition" 
                               title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            
                            {{-- SWEET ALERT DELETE FORM --}}
                            {{-- 1. Give the form a unique ID --}}
                            {{-- 2. Remove the standard onsubmit --}}
                            <form id="delete-form-{{ $category->id }}" 
                                  action="{{ route('admin.categories.destroy', $category) }}" 
                                  method="POST" 
                                  class="inline">
                                @csrf 
                                @method('DELETE')
                                {{-- 3. Change button type to 'button' and add onclick handler --}}
                                <button type="button" 
                                        onclick="confirmDelete({{ $category->id }})"
                                        class="text-red-600 hover:text-red-900 transition" 
                                        title="Delete">
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
    
    <div class="mt-6">
        @if(method_exists($categories, 'links'))
            {{ $categories->links() }} 
        @endif
    </div>
</div>

{{-- SWEETALERT SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Handle Delete Confirmation
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form programmatically
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    // 2. Handle Success Message (Toast)
    @if(session('success'))
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        })
    @endif
</script>

@endsection