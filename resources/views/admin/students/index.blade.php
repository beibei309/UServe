@extends('admin.layout')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div>
        <h1 class="text-3xl font-bold mb-4">Manage Students</h1>
        <p class="text-xs text-gray-500 mb-4">
            This page manages student/helper suspension. Seller blocking is handled in Feedback, and community blacklist is handled in Community Management.
        </p>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
            <form method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <input type="text" name="search" placeholder="Search name, email, student ID..."
                    class="w-full md:w-80 px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    value="{{ request('search') }}">

                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <button
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    Search
                </button>
            </form>

            <a href="{{ route('admin.students.export', ['format' => 'csv'] + request()->all()) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                <i class="fa-solid fa-file-csv"></i>
                Export CSV
            </a>
        </div>

        <div class="flex flex-wrap gap-2 mb-6">
            @php
                $pill = 'px-4 py-2 rounded-full text-sm font-medium transition';
                $active = 'bg-blue-600 text-white';
                $inactive = 'bg-gray-100 text-gray-700 hover:bg-gray-200';
            @endphp

            <a href="{{ route('admin.students.index', request()->except('status')) }}"
                class="{{ $pill }} {{ request('status') == null ? $active : $inactive }}">
                All
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'student'] + request()->except('page')) }}"
                class="{{ $pill }} {{ request('status') == 'student' ? $active : $inactive }}">
                Students
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'helper'] + request()->except('page')) }}"
                class="{{ $pill }} {{ request('status') == 'helper' ? $active : $inactive }}">
                Sellers
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'suspended'] + request()->except('page')) }}"
                class="{{ $pill }} {{ request('status') == 'suspended' ? $active : $inactive }}">
                Suspended
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'blacklisted'] + request()->except('page')) }}"
                class="{{ $pill }} {{ request('status') == 'blacklisted' ? $active : $inactive }}">
                Blacklisted
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'blocked'] + request()->except('page')) }}"
                class="{{ $pill }} {{ request('status') == 'blocked' ? $active : $inactive }}">
                Blocked
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-100 text-sm text-gray-600">
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
                        <tr class="border-b">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full border border-gray-200"
                                        src="{{ $student->hu_profile_photo_path ? asset($student->hu_profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($student->hu_name) }}"
                                        alt="Profile">

                                    <div>
                                        <div class="font-medium text-gray-900">{{ $student->hu_name }}</div>
                                        @if ($student->hu_role === 'helper')
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full bg-blue-200 text-gray-700">Seller</span>
                                        @endif
                                        @if ($student->hu_role === 'student')
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full bg-yellow-200 text-gray-700">Student</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-3 px-4 text-sm">{{ $student->hu_email }}</td>
                            <td class="py-3 px-4 text-sm">{{ $student->hu_phone ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm">{{ $student->hu_student_id ?? '-' }}</td>

                            <td class="py-3 px-4">
                                @if ($student->hu_is_blacklisted)
                                    <span class="px-3 py-1 text-xs bg-red-200 text-red-800 rounded-full">Blacklisted</span>
                                @elseif ($student->hu_is_suspended)
                                    <span class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full">Suspended</span>
                                @elseif ($student->hu_is_blocked)
                                    <span class="px-3 py-1 text-xs bg-amber-100 text-amber-800 rounded-full">Blocked</span>
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
                                        class="text-indigo-600 hover:text-indigo-900 transition" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.students.edit', $student->hu_id) }}"
                                        class="text-blue-600 hover:text-blue-900 transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- BAN / UNBAN --}}
                                    @if ($student->hu_is_suspended || $student->hu_is_blacklisted || $student->hu_is_blocked)
                                        {{-- Tambah class 'unban-form' pada form dan 'unban-btn' pada button --}}
                                        <form action="{{ route('admin.students.unban', $student->hu_id) }}" method="POST"
                                            class="unban-form inline">
                                            @csrf
                                            <button type="button"
                                                class="text-green-600 hover:text-green-900 transition unban-btn"
                                                title="Reactivate">
                                                <i class="fa-solid fa-unlock"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button onclick="openBanModal({{ $student->hu_id }})"
                                            class="text-red-600 hover:text-red-900 transition" title="Suspend">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    @endif

                                    {{-- DELETE --}}
                                    {{-- <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}" class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-900 transition delete-student-btn" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">No records found.</td>
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
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-xl">
            <h2 class="text-xl font-bold mb-4">Suspend Student</h2>
            <p class="text-gray-600 mb-3">Please provide a reason for suspending this student:</p>
            <textarea id="banReason" rows="3" class="w-full border rounded p-2 focus:ring focus:ring-red-300"
                placeholder="Write reason..."></textarea>

            <form id="banForm" method="POST" class="hidden">
                @csrf
            </form>

            <div class="mt-5 flex justify-end gap-3">
                <button onclick="closeBanModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Cancel</button>
                <button onclick="submitBan()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Confirm
                    Suspension</button>
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
                alert("Please enter a suspension reason.");
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
