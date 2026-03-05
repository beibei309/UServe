@extends('admin.layout')

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
            <a href="{{ route('admin.students.index', request()->except('status')) }}"
                class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ request('status') == null ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white' : 'border' }}"
                @if(request('status') != null)
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                @endif>
                All
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'student'] + request()->except('page')) }}"
                class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ request('status') == 'student' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white' : 'border' }}"
                @if(request('status') != 'student')
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                @endif>
                Students
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'helper'] + request()->except('page')) }}"
                class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ request('status') == 'helper' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white' : 'border' }}"
                @if(request('status') != 'helper')
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                @endif>
                Sellers
            </a>

            <a href="{{ route('admin.students.index', ['status' => 'banned'] + request()->except('page')) }}"
                class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ request('status') == 'banned' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white' : 'border' }}"
                @if(request('status') != 'banned')
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
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
                            <tr class="border-b transition-colors duration-300 surface-hover"
                                style="border-color: var(--border-color);">
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
                                        <button type="button" data-ban-open data-student-id="{{ $student->hu_id }}"
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
                      placeholder="Write reason..."></textarea>

            <form id="banForm" method="POST" class="hidden">
                @csrf
            </form>

            <div class="mt-5 flex justify-end gap-3">
                <button type="button" data-ban-close
                        class="hover:text-cyan-400 transition-colors duration-300 border rounded px-4 py-2"
                        style="color: var(--text-muted); border-color: var(--border-color);">Cancel</button>
                <button type="button" data-ban-submit
                        class="text-red-500 hover:text-red-400 transition-colors duration-300 border rounded px-4 py-2"
                        style="border-color: var(--border-color);">Confirm Ban</button>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <div id="adminModuleStudentsIndexConfig"
        data-ban-route-template="{{ route('admin.students.ban', ':id') }}"
        data-success-message="{{ session('success') }}"></div>
    <script src="{{ asset('js/admin-students-index.js') }}"></script>
@endsection
