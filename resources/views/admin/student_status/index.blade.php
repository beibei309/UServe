@extends('admin.layout')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Student Status Management</h1>
        <a href="{{ route('admin.student_status.create') }}"
            class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded shadow hover:bg-blue-700 transition">
            + Assign New Status
        </a>
    </div>

    <div class="mb-6">
        <form method="GET" action="{{ route('admin.student_status.index') }}" class="flex flex-col md:flex-row gap-4">

            {{-- Search Input --}}
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by Name or Matric No..."
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Graduation Filter Dropdown --}}
            <div class="w-full md:w-64">
                <select name="grad_filter" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
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
                class="px-4 py-2 bg-gray-100 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-200 text-center">
                Reset
            </a>

        </form>
    </div>

    @if (session('success'))
        <div class="p-4 bg-green-50 text-green-800 rounded-md border border-green-200 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="p-4 bg-blue-50 text-blue-800 rounded-md border border-blue-200 mb-6">
            {{ session('info') }}
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-6 font-semibold">Student Name</th>
                    <th class="py-3 px-6 font-semibold">Matric No</th>
                    <th class="py-3 px-6 font-semibold">Current Semester</th>
                    <th class="py-3 px-6 font-semibold">Graduation Date</th>
                    <th class="py-3 px-6 font-semibold">Status</th>
                    <th class="py-3 px-6 font-semibold text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($students as $student)
                    <tr class="hover:bg-gray-50 transition duration-150">

                        {{-- NAME --}}
                        <td class="py-4 px-6 font-medium text-gray-900">
                            {{ $student->name }}
                        </td>

                        {{-- MATRIC NO --}}
                        <td class="py-4 px-6 text-gray-500">
                            {{ $student->student_id ?? '-' }}
                        </td>

                        {{-- SEMESTER --}}
                        <td class="py-4 px-6 text-gray-600">
                            @if ($student->studentStatus && $student->studentStatus->status === 'Graduated')
                                <span class="text-gray-400 italic">-</span>
                            @else
                                {{ $student->studentStatus->semester ?? '-' }}
                            @endif
                        </td>

                        {{-- GRADUATION DATE --}}
                        <td class="py-4 px-6 text-gray-600">
                            @if ($student->studentStatus && $student->studentStatus->graduation_date)
                                {{-- Use the format you prefer here. If you want the EST label, replace this part with the code from previous steps --}}
                                {{ \Carbon\Carbon::parse($student->studentStatus->graduation_date)->format('d M Y') }}
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>

                        {{-- STATUS BADGE --}}
                        <td class="py-4 px-6">
                            @php $status = strtolower($student->studentStatus->status ?? ''); @endphp

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
                $gradDate = \Carbon\Carbon::parse($student->studentStatus->graduation_date);
                $now = now();
                $threeMonthsLimit = now()->addMonths(3);
                
                // Show ONLY if: Date exists AND is in future AND is within next 3 months
                $showReminder = !empty($student->studentStatus->graduation_date) 
                                && $student->studentStatus->status !== 'Graduated'
                                && $gradDate->gte($now) 
                                && $gradDate->lte($threeMonthsLimit);
            @endphp

            @if ($showReminder)
                {{-- Functional Form to Send Email --}}
                {{-- Functional Form to Send Email --}}
<form id="reminder-form-{{ $student->id }}" 
      action="{{ route('admin.student_status.send_reminder', $student->id) }}" 
      method="POST" 
      class="inline-block">
    @csrf
    
    <button type="button" 
        onclick="confirmSendReminder({{ $student->id }}, '{{ addslashes($student->name) }}')"
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
            <a href="{{ route('admin.student_status.edit', $student->studentStatus->id) }}"
                class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                Edit
            </a>

            {{-- DELETE BUTTON --}}
            <form action="{{ route('admin.student_status.delete', $student->studentStatus->id) }}"
                method="POST" class="inline-block"
                onsubmit="return confirm('Remove status record?');">
                @csrf
                @method('DELETE')
                <button class="text-red-500 hover:text-red-700 font-medium text-sm">
                    Delete
                </button>
            </form>
        @else
            {{-- ADD BUTTON --}}
            <a href="{{ route('admin.student_status.create', ['student_id' => $student->id]) }}"
                class="text-blue-600 hover:underline">
                + Add Status
            </a>
        @endif
    </div>
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $students->links() }}</div>
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
