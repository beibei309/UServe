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
                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                    onfocus="this.style.borderColor = '#06b6d4'; this.style.boxShadow = '0 0 0 1px #06b6d4';">
            </div>

            {{-- Graduation Filter Dropdown --}}
            <div class="w-full md:w-64">
                <select name="grad_filter" onchange="this.form.submit()"
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
                                @if ($student->studentStatus && $student->studentStatus->hss_status === 'Graduated')
                                    <span class="italic transition-colors duration-300" style="color: var(--text-muted);">-</span>
                                @else
                                    {{ $student->studentStatus->hss_semester ?? '-' }}
                                @endif
                            </td>

                            {{-- GRADUATION DATE --}}
                            <td class="py-4 px-6 transition-colors duration-300" style="color: var(--text-secondary);">
                                @if ($student->studentStatus && $student->studentStatus->hss_graduation_date)
                                    {{ \Carbon\Carbon::parse($student->studentStatus->hss_graduation_date)->format('d M Y') }}
                                @else
                                    <span class="italic transition-colors duration-300" style="color: var(--text-muted);">-</span>
                                @endif
                            </td>

                        {{-- STATUS BADGE --}}
                        <td class="py-4 px-6">
                            @php $status = strtolower($student->studentStatus->hss_status ?? ''); @endphp

                            @if ($status == 'active')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @elseif($status == 'probation')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Probation</span>
                            @elseif($status == 'graduated')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Graduated</span>
                            @elseif($status == 'deferred')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Deferred</span>
                            @elseif($status == '')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400">Not
                                    Set</span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($status) }}</span>
                            @endif
                        </td>

                        {{-- ACTIONS COLUMN --}}
                        {{-- ACTIONS COLUMN --}}
<td class="py-4 px-6 text-right">
    <div class="flex items-center justify-end gap-3">

        @if ($student->studentStatus)
            
            {{-- 1. REMINDER BUTTON (LOGIC ADDED) --}}
            @php
                $gradDate = \Carbon\Carbon::parse($student->studentStatus->hss_graduation_date);
                $now = now();
                $threeMonthsLimit = now()->addMonths(3);
                
                // Show ONLY if: Date exists AND is in future AND is within next 3 months
                $showReminder = !empty($student->studentStatus->hss_graduation_date) 
                                && $student->studentStatus->hss_status !== 'Graduated'
                                && $gradDate->gte($now) 
                                && $gradDate->lte($threeMonthsLimit);
            @endphp

            @if ($showReminder)
                {{-- Functional Form to Send Email --}}
                {{-- Functional Form to Send Email --}}
<form id="reminder-form-{{ $student->hu_id }}" 
    action="{{ route('admin.student_status.send_reminder', $student->hu_id) }}" 
      method="POST" 
      class="inline-block">
    @csrf
    
    <button type="button" 
        onclick="confirmSendReminder({{ $student->hu_id }}, '{{ addslashes($student->hu_name) }}')"
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
                onsubmit="return confirm('Remove status record?');">
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
                    
                    @php $status = strtolower($student->studentStatus->hss_status ?? ''); @endphp
                    @if ($status == 'active')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                    @elseif($status == 'probation')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Probation</span>
                    @elseif($status == 'graduated')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Graduated</span>
                    @elseif($status == 'deferred')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Deferred</span>
                    @elseif($status == '')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400">Not Set</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($status) }}</span>
                    @endif
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                    <div>
                        <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Semester:</span>
                        <span class="transition-colors duration-300" style="color: var(--text-secondary);">
                            @if ($student->studentStatus && $student->studentStatus->hss_status === 'Graduated')
                                <span class="italic">-</span>
                            @else
                                {{ $student->studentStatus->hss_semester ?? '-' }}
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">Graduation:</span>
                        <span class="transition-colors duration-300" style="color: var(--text-secondary);">
                            @if ($student->studentStatus && $student->studentStatus->hss_graduation_date)
                                {{ \Carbon\Carbon::parse($student->studentStatus->hss_graduation_date)->format('d M Y') }}
                            @else
                                <span class="italic">-</span>
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
                            onsubmit="return confirm('Remove status record?');">
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
    <script>
    function confirmSendReminder(studentId, studentName) {
        Swal.fire({
            title: 'Send Graduation Reminder?',
            text: `Are you sure you want to send an email reminder to ${studentName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#eab308', // Yellow-500 to match your UI
            cancelButtonColor: '#6b7280', // Gray
            confirmButtonText: 'Yes, send email',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            borderRadius: '0.5rem' // Optional: Match Tailwind rounded corners
        }).then((result) => {
            if (result.isConfirmed) {
                // Show a loading state immediately after confirming
                Swal.fire({
                    title: 'Sending...',
                    text: 'Please wait while we send the email.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the form programmatically
                document.getElementById('reminder-form-' + studentId).submit();
            }
        });
    }
</script>
@endsection
