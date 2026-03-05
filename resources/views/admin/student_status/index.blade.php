@extends('admin.layout')

@section('content')
<div class="px-4 sm:px-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Student Status Management</h1>
        <a href="{{ route('admin.student_status.create') }}"
            class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium rounded shadow transition-all duration-300">
            + Assign New Status
        </a>
    </div>

    <div class="mb-6">
        <form method="GET" action="{{ route('admin.student_status.index') }}" class="flex flex-col md:flex-row gap-4">

            {{-- Search Input --}}
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by Name or Matric No..."
                    class="w-full border rounded-md px-4 py-2 transition-colors duration-300"
                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
            </div>

            {{-- Graduation Filter Dropdown --}}
            <div class="w-full md:w-64">
                <select name="grad_filter" data-auto-submit-filter
                    class="w-full border rounded-md px-4 py-2 transition-colors duration-300"
                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    <option value="">All Graduation Dates</option>
                    <option value="expired" {{ request('grad_filter') == 'expired' ? 'selected' : '' }}>
                        Overdue (Date Passed)
                    </option>
                    <option value="3_months" {{ request('grad_filter') == '3_months' ? 'selected' : '' }}>
                        Less than 3 Months
                    </option>
                    <option value="6_months" {{ request('grad_filter') == '6_months' ? 'selected' : '' }}>
                        Less than 6 Months
                    </option>
                    <option value="12_months" {{ request('grad_filter') == '12_months' ? 'selected' : '' }}>
                        Less than 1 Year
                    </option>
                </select>
            </div>

            {{-- Reset Button --}}
            <a href="{{ route('admin.student_status.index') }}"
                class="w-full md:w-auto px-4 py-2 border rounded-md text-center transition-all duration-300 hover:bg-cyan-500 hover:text-white"
                style="background-color: var(--bg-tertiary); color: var(--text-secondary); border-color: var(--border-color);">
                Reset
            </a>

        </form>
    </div>

    @if (session('success'))
        <div class="p-4 bg-green-900 bg-opacity-30 border border-green-700 rounded-md mb-6 transition-all duration-300">
            <p class="text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('info'))
        <div class="p-4 bg-cyan-900 bg-opacity-30 border border-cyan-700 rounded-md mb-6 transition-all duration-300">
            <p class="text-cyan-200">{{ session('info') }}</p>
        </div>
    @endif

    {{-- MOBILE RESPONSIVE TABLE --}}
    <div class="hidden md:block">
        <div class="shadow-xl rounded-lg border overflow-hidden transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left border-collapse">
                <thead class="text-xs uppercase tracking-wider border-b transition-colors duration-300"
                       style="background-color: var(--bg-tertiary); color: var(--text-secondary); border-color: var(--border-color);">
                    <tr>
                        <th class="py-3 px-6 font-semibold">Student Name</th>
                        <th class="py-3 px-6 font-semibold">Matric No</th>
                        <th class="py-3 px-6 font-semibold">Current Semester</th>
                        <th class="py-3 px-6 font-semibold">Graduation Date</th>
                        <th class="py-3 px-6 font-semibold">Status</th>
                        <th class="py-3 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y transition-colors duration-300" style="border-color: var(--border-color);">
                    @forelse($students as $student)
                        <tr class="hover:opacity-80 transition-opacity duration-150">

                            {{-- NAME --}}
                            <td class="py-4 px-6 font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                {{ $student->hu_name }}
                            </td>

                            {{-- MATRIC NO --}}
                            <td class="py-4 px-6 transition-colors duration-300" style="color: var(--text-secondary);">
                                {{ $student->hu_student_id ?? '-' }}
                            </td>

                            {{-- SEMESTER --}}
                            <td class="py-4 px-6 transition-colors duration-300" style="color: var(--text-secondary);">
                                @if ($student->semester_display === '-')
                                    <span class="italic transition-colors duration-300" style="color: var(--text-muted);">{{ $student->semester_display }}</span>
                                @else
                                    {{ $student->semester_display }}
                                @endif
                            </td>

                            {{-- GRADUATION DATE --}}
                            <td class="py-4 px-6 transition-colors duration-300" style="color: var(--text-secondary);">
                                @if ($student->graduation_date_display === '-')
                                    <span class="italic transition-colors duration-300" style="color: var(--text-muted);">{{ $student->graduation_date_display }}</span>
                                @else
                                    {{ $student->graduation_date_display }}
                                @endif
                            </td>

                        {{-- STATUS BADGE --}}
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->status_badge_class }}">
                                {{ $student->status_label }}
                            </span>
                        </td>

                        {{-- ACTIONS COLUMN --}}
                        {{-- ACTIONS COLUMN --}}
<td class="py-4 px-6 text-right">
    <div class="flex items-center justify-end gap-3">

        @if ($student->studentStatus)
            @if ($student->show_reminder)
                {{-- Functional Form to Send Email --}}
                {{-- Functional Form to Send Email --}}
<form id="reminder-form-{{ $student->hu_id }}" 
    action="{{ route('admin.student_status.send_reminder', $student->hu_id) }}" 
      method="POST" 
      class="inline-block">
    @csrf
    
    <button type="button"
        data-reminder-send
        data-student-id="{{ $student->hu_id }}"
        data-student-name="{{ $student->hu_name }}"
        class="text-yellow-500 hover:text-yellow-700 p-1 rounded hover:bg-yellow-50 transition relative group"
        title="Send Graduation Reminder">
        
        {{-- Bell Icon --}}
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>

        {{-- Ping Indicator --}}
        <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
        </span>
    </button>
</form>
            @endif

            {{-- EDIT BUTTON --}}
            <a href="{{ route('admin.student_status.edit', $student->studentStatus->hss_id) }}"
                class="text-cyan-400 hover:text-cyan-300 transition-colors duration-300 font-medium text-sm">
                <i class="fa-solid fa-edit"></i>
            </a>

            {{-- DELETE BUTTON --}}
            <form action="{{ route('admin.student_status.delete', $student->studentStatus->hss_id) }}"
                method="POST" class="inline-block"
                data-confirm-message="Remove status record?">
                @csrf
                @method('DELETE')
                <button class="text-red-500 hover:text-red-400 transition-colors duration-300 ml-3" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        @else
            {{-- ADD BUTTON --}}
            <a href="{{ route('admin.student_status.create', ['student_id' => $student->hu_id]) }}"
                class="text-cyan-400 hover:text-cyan-300 transition-colors duration-300 hover:underline">
                + Add Status
            </a>
        @endif
    </div>
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center transition-colors duration-300" style="color: var(--text-muted);">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
            </div>
        <div class="p-4 transition-colors duration-300" style="background-color: var(--bg-primary); border-top: 1px solid; border-color: var(--border-color);">{{ $students->links() }}</div>
    </div>

    {{-- MOBILE RESPONSIVE CARDS --}}
    <div class="block md:hidden space-y-4">
        @forelse($students as $student)
            <div class="rounded-lg border p-4 transition-all duration-300"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $student->hu_name }}</h3>
                        <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $student->hu_student_id ?? 'No Matric' }}</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->status_badge_class }}">{{ $student->status_label }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                    <div>
                        <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Semester:</span>
                        <span class="transition-colors duration-300" style="color: var(--text-secondary);">
                            @if ($student->semester_display === '-')
                                <span class="italic">{{ $student->semester_display }}</span>
                            @else
                                {{ $student->semester_display }}
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Graduation:</span>
                        <span class="transition-colors duration-300" style="color: var(--text-secondary);">
                            @if ($student->graduation_date_display === '-')
                                <span class="italic">{{ $student->graduation_date_display }}</span>
                            @else
                                {{ $student->graduation_date_display }}
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    @if ($student->studentStatus)
                        <a href="{{ route('admin.student_status.edit', $student->studentStatus->hss_id) }}"
                            class="text-cyan-400 hover:text-cyan-300 transition-colors duration-300 text-sm">
                            <i class="fa-solid fa-edit"></i> Edit
                        </a>
                        
                        <form action="{{ route('admin.student_status.delete', $student->studentStatus->hss_id) }}"
                            method="POST" class="inline-block"
                            data-confirm-message="Remove status record?">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-400 transition-colors duration-300 text-sm">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.student_status.create', ['student_id' => $student->hu_id]) }}"
                            class="text-cyan-400 hover:text-cyan-300 transition-colors duration-300 text-sm">
                            + Add Status
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 transition-colors duration-300" style="color: var(--text-muted);">
                No students found.
            </div>
        @endforelse
        
        <div class="mt-4">{{ $students->links() }}</div>
    </div>
@endsection

@section('scripts')
    <div id="adminStudentStatusIndexConfig"></div>
    <script src="{{ asset('js/admin-student-status-index.js') }}"></script>
@endsection
