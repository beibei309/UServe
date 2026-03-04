@extends('admin.layout')
@section('content')
    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Manage FAQs</h1>
            <a href="{{ route('admin.faqs.create') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-4 py-2 rounded-lg transition-all duration-300 whitespace-nowrap">
                + Add FAQ
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
                                <form method="POST" action="{{ route('admin.faqs.toggle', $faq) }}" class="toggle-form" data-faq-id="{{ $faq->hfq_id }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="text-xs px-3 py-1 rounded-full toggle-btn transition-colors duration-300 border
                                        {{ $faq->hfq_is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}"
                                        data-active="{{ $faq->hfq_is_active ? '1' : '0' }}">
                                        {{ $faq->hfq_is_active ? 'Active' : 'Hidden' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-cyan-400 hover:text-cyan-300 transition-colors duration-300 p-2" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="delete-form inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-500 hover:text-red-400 transition-colors duration-300 delete-faq-btn p-2" title="Delete">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all delete buttons with our class
            const deleteButtons = document.querySelectorAll('.delete-faq-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Find the parent form
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This FAQ will be permanently deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5', // Matches your indigo-600
                        cancelButtonColor: '#ef4444', // Matches red-500
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // AJAX Toggle functionality to prevent reordering
            const toggleForms = document.querySelectorAll('.toggle-form');

            toggleForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const faqId = this.dataset.faqId;
                    const button = this.querySelector('.toggle-btn');
                    const isActive = button.dataset.active === '1';
                    
                    // Disable button during request
                    button.disabled = true;
                    button.style.opacity = '0.6';
                    
                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            _method: 'PATCH'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Toggle the visual state
                            if (isActive) {
                                button.textContent = 'Hidden';
                                button.className = 'text-xs px-3 py-1 rounded-full toggle-btn transition-colors duration-300 border bg-gray-100 text-gray-600 border-gray-200';
                                button.dataset.active = '0';
                            } else {
                                button.textContent = 'Active';
                                button.className = 'text-xs px-3 py-1 rounded-full toggle-btn transition-colors duration-300 border bg-green-100 text-green-800 border-green-200';
                                button.dataset.active = '1';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update FAQ status'
                        });
                    })
                    .finally(() => {
                        // Re-enable button
                        button.disabled = false;
                        button.style.opacity = '1';
                    });
                });
            });
        });
    </script>
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000, // Closes after 2 seconds
                iconColor: '#10b981', // Emerald green
            });
        });
    </script>
@endif
@endsection
