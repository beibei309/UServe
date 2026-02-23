@extends('admin.layout')
@section('content')
    <div class="max-w-5xl mx-auto py-10">

        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Manage FAQs</h1>
            <a href="{{ route('admin.faqs.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">
                + Add FAQ
            </a>
        </div>

        @foreach ($faqs as $category => $items)
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-3">{{ $category }}</h2>

                <div class="bg-white rounded-xl shadow divide-y">
                    @foreach ($items as $faq)
                        <div class="p-4 flex justify-between items-start">
                            <div>
                                <p class="font-medium">{{ $faq->question }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ Str::limit(strip_tags($faq->answer), 120) }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.faqs.toggle', $faq) }}">
                                    @csrf
                                    <button
                                        class="text-xs px-3 py-1 rounded
                                        {{ $faq->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                        {{ $faq->is_active ? 'Active' : 'Hidden' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-indigo-600 text-sm">Edit</a>

                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 text-sm delete-faq-btn hover:underline">
                                        Delete
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
