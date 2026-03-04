@extends('admin.layout')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="px-4 sm:px-6">
        <h1 class="text-3xl font-bold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Manage Students</h1>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
            <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                <input type="text" name="search" placeholder="Search name, email, student ID..."
                    class="w-full md:w-80 px-4 py-2 border rounded-lg text-sm transition-colors duration-300
                      focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);"
                    value="{{ request('search') }}">

                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <button
                    class="px-5 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-lg hover:from-cyan-400 hover:to-blue-500 transition-all duration-300 text-sm font-medium">
                    Search
                </button>
            </form>

            <a href="{{ route('admin.students.export', ['format' => 'csv'] + request()->all()) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 text-sm">
                <i class="fa-solid fa-file-csv"></i>
                Export CSV
            </a>
        </div>

        <div class="flex flex-wrap gap-2 mb-6">
            @php
                $pill = 'px-4 py-2 rounded-full text-sm font-medium transition-all duration-300';
                $active = 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white';
                $inactive = 'border';
            @endphp

            <a href="{{ route('admin.students.index', request()->except('status')) }}"
                class="{{ $pill }} {{ request('status') == null ? $active : $inactive }}"
                @if(request('status') != null)
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                onmouseover="this.style.backgroundColor = 'var(--hover-bg)';"
                onmouseout="this.style.backgroundColor = 'var(--bg-tertiary)';"
                @endif>
                All
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'student'] + request()->except('page')) }}"
                class="{{ $pill }} {{ request('status') == 'student' ? $active : $inactive }}"
                @if(request('status') != 'student')
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                onmouseover="this.style.backgroundColor = 'var(--hover-bg)';"
                onmouseout="this.style.backgroundColor = 'var(--bg-tertiary)';"
                @endif>
                Students
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'helper'] + request()->except('page')) }}"
                class="{{ $pill }} {{ request('status') == 'helper' ? $active : $inactive }}"
                @if(request('status') != 'helper')
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                onmouseover="this.style.backgroundColor = 'var(--hover-bg)';"
                onmouseout="this.style.backgroundColor = 'var(--bg-tertiary)';"
                @endif>
                Sellers
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'banned'] + request()->except('page')) }}"
                class="{{ $pill }} {{ request('status') == 'banned' ? $active : $inactive }}"
                @if(request('status') != 'banned')
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                onmouseover="this.style.backgroundColor = 'var(--hover-bg)';"
                onmouseout="this.style.backgroundColor = 'var(--bg-tertiary)';"
                @endif>
                Suspended
            </a>
        </div>

        <div class="shadow-xl rounded-lg p-6 border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-sm border-b transition-colors duration-300"
                            style="background-color: var(--bg-tertiary); color: var(--text-secondary); border-color: var(--border-color);">
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Phone</th>
                            <th class="py-3 px-4">Student ID</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-b transition-colors duration-300"
                                style="border-color: var(--border-color);"
                                onmouseover="this.style.backgroundColor = 'var(--hover-bg)';"
                                onmouseout="this.style.backgroundColor = 'transparent';">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full border transition-colors duration-300"
                                        style="border-color: var(--border-color);"
                                        src="{{ $student->hu_profile_photo_path ? asset($student->hu_profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($student->hu_name) }}"
                                        alt="Profile">

                                    <div>
                                        <div class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $student->hu_name }}</div>
                                        @if ($student->hu_role === 'helper')
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full bg-cyan-700 text-cyan-100">Seller</span>
                                        @endif
                                        @if ($student->hu_role === 'student')
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full bg-yellow-700 text-yellow-100">Student</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-3 px-4 text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $student->hu_email }}</td>
                            <td class="py-3 px-4 text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $student->hu_phone ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $student->hu_student_id ?? '-' }}</td>

                            <td class="py-3 px-4">
                                @if ($student->hu_is_suspended)
                                    <span class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full">Suspended</span>
                                @elseif ($student->hu_verification_status === 'approved')
                                        <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">Verified</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    {{-- VIEW --}}
                                    <a href="{{ route('admin.students.view', $student->hu_id) }}"
                                        class="text-cyan-400 hover:text-cyan-300 transition-colors duration-300" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.students.edit', $student->hu_id) }}"
                                        class="text-blue-400 hover:text-blue-300 transition-colors duration-300" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- BAN / UNBAN --}}
                                    @if ($student->hu_is_suspended)
                                        {{-- Tambah class 'unban-form' pada form dan 'unban-btn' pada button --}}
                                        <form action="{{ route('admin.students.unban', $student->hu_id) }}" method="POST"
                                            class="unban-form inline">
                                            @csrf
                                            <button type="button"
                                                class="text-green-400 hover:text-green-300 transition-colors duration-300 unban-btn"
                                                title="Unban">
                                                <i class="fa-solid fa-unlock"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button onclick="openBanModal({{ $student->hu_id }})"
                                            class="text-red-500 hover:text-red-400 transition-colors duration-300" title="Ban">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 transition-colors duration-300" style="color: var(--text-muted);">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    <div id="banModal"
        class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="w-full max-w-md p-6 rounded-lg shadow-xl transition-all duration-300"
             style="background-color: var(--bg-primary);">
            <h2 class="text-xl font-bold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Ban Student</h2>
            <p class="mb-3 transition-colors duration-300" style="color: var(--text-secondary);">Please provide a reason for banning this student:</p>
            <textarea id="banReason" rows="3" 
                      class="w-full border rounded p-2 transition-colors duration-300"
                      style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                      onfocus="this.style.borderColor = '#ef4444'; this.style.boxShadow = '0 0 0 2px rgba(239, 68, 68, 0.2)';"
                      placeholder="Write reason..."></textarea>

            <form id="banForm" method="POST" class="hidden">
                @csrf
            </form>

            <div class="mt-5 flex justify-end gap-3">
                <button onclick="closeBanModal()" 
                        class="hover:text-cyan-400 transition-colors duration-300 border rounded px-4 py-2"
                        style="color: var(--text-muted); border-color: var(--border-color);">Cancel</button>
                <button onclick="submitBan()" 
                        class="text-red-500 hover:text-red-400 transition-colors duration-300 border rounded px-4 py-2"
                        style="border-color: var(--border-color);">Confirm Ban</button>
            </div>
        </div>
    </div>

    <script>
        let selectedStudentId = null;

        function openBanModal(id) {
            selectedStudentId = id;
            document.getElementById("banModal").classList.remove("hidden");
        }

        function closeBanModal() {
            document.getElementById("banModal").classList.add("hidden");
            document.getElementById("banReason").value = "";
        }

        function submitBan() {
            const reason = document.getElementById("banReason").value.trim();
            if (!reason) {
                alert("Please enter a ban reason.");
                return;
            }

            const form = document.getElementById("banForm");
            form.action = "{{ route('admin.students.ban', ':id') }}".replace(':id', selectedStudentId);
            form.innerHTML = `@csrf <input type="hidden" name="blacklist_reason" value="${reason}">`;
            form.submit();
        }

        // DELETE CONFIRMATION
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-student-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This student record will be permanently deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#ef4444',
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

       document.addEventListener('DOMContentLoaded', function() {
    // Select all buttons with the .unban-btn class
    const unbanButtons = document.querySelectorAll('.unban-btn');

    unbanButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Find the form associated with this button
            const form = this.closest('form');

            Swal.fire({
                title: 'Reactivate student account?',
                text: "This student will regain access to the system immediately.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981', // Green
                cancelButtonColor: '#6b7280', // Gray
                confirmButtonText: 'Yes, Reactivate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit the form if the admin clicks "Yes"
                }
            });
        });
    });
});
    </script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000,
                    iconColor: '#10b981',
                });
            });
        </script>
    @endif
@endsection
