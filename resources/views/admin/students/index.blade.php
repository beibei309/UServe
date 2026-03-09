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
                                    <span class="px-3 py-1 text-xs font-bold bg-red-100 text-red-700 rounded-full">Suspended</span>
                                @elseif ($student->hu_verification_status === 'approved')
                                        <span class="px-3 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">Verified</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs font-bold bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- VIEW --}}
                                    <a href="{{ route('admin.students.view', $student->hu_id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-cyan-100 hover:bg-cyan-200 text-cyan-700 rounded-lg text-xs font-semibold transition-all duration-200" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.students.edit', $student->hu_id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-semibold transition-all duration-200" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- BAN / UNBAN --}}
                                    @if ($student->hu_is_suspended)
                                        <form action="{{ route('admin.students.unban', $student->hu_id) }}" method="POST"
                                            class="unban-form inline-flex">
                                            @csrf
                                            <button type="button"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-semibold transition-all duration-200 unban-btn"
                                                title="Unban">
                                                <i class="fa-solid fa-unlock"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" data-ban-open data-student-id="{{ $student->hu_id }}"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-semibold transition-all duration-200" title="Ban">
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

    {{-- BAN MODAL --}}
    <div id="banModal"
        class="modal-overlay fixed inset-0 bg-slate-900 bg-opacity-60 backdrop-blur-md flex items-center justify-center z-50 p-4">
        
        <div class="w-full max-w-lg rounded-2xl shadow-2xl border transition-all duration-500 transform scale-95 hover:scale-100"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-red-600 to-pink-700 px-6 py-6 rounded-t-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-ban text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-2xl">Ban Student</h2>
                        <p class="text-red-100 text-sm">Suspend account access immediately</p>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-8">
                {{-- Warning Alert --}}
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-red-800 text-sm mb-1">Important Warning</h3>
                            <p class="text-red-700 text-xs leading-relaxed">
                                This action will immediately suspend the student's account and prevent access to all services.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Reason Input --}}
                <div class="mb-8">
                    <label for="banReason" class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                        <i class="fas fa-gavel text-red-500"></i>
                        Reason for Ban <span class="text-red-500">*</span>
                    </label>
                    <textarea id="banReason" rows="4" 
                              class="w-full border rounded-xl px-4 py-3 transition-all duration-300 focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none" 
                              style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" 
                              placeholder="Please provide a detailed and specific reason for this ban..."
                              required></textarea>
                    <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">
                        Be specific and professional. This reason will be visible to the student.
                    </p>
                </div>

                <form id="banForm" method="POST" class="hidden">
                    @csrf
                </form>

                {{-- Action Buttons --}}
                <div class="flex gap-4">
                    <button type="button" data-ban-close
                            class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl transition-all duration-300 font-semibold flex items-center justify-center gap-2 shadow-lg">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="button" data-ban-submit
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white rounded-xl transition-all duration-300 font-semibold flex items-center justify-center gap-2 shadow-lg">
                        <i class="fas fa-ban"></i>
                        Confirm Ban
                    </button>
                </div>
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
