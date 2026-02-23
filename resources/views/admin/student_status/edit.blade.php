@extends('admin.layout')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Student Status</h2>
        <a href="{{ route('admin.student_status.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">
            &larr; Back to List
        </a>
    </div>

    <form action="{{ route('admin.student_status.update', $status->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- LEFT COLUMN: STUDENT INFO (READ ONLY) --}}
            <div class="md:col-span-1">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-sm">
                    <h3 class="text-blue-800 font-bold text-sm uppercase tracking-wide mb-3">Student Information</h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs text-gray-500 uppercase font-bold">Full Name</label>
                        <p class="text-gray-900 font-medium text-lg">{{ $status->student->name }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs text-gray-500 uppercase font-bold">Matric No</label>
                        <p class="text-gray-900 font-mono">{{ $status->student->student_id ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 uppercase font-bold">Email</label>
                        <p class="text-gray-600 text-sm break-all">{{ $status->student->email }}</p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <span class="text-xs text-blue-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            You cannot change the student here. Delete and create new if incorrect.
                        </span>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: STATUS FORM --}}
            <div class="md:col-span-2">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 h-full">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b pb-2">Update Status Details</h3>

                    <div class="grid grid-cols-1 gap-6">
                        
                        {{-- 1. STATUS --}}
                        <div>
                            <label for="status" class="block text-gray-700 font-medium mb-2">1. Status</label>
                            <select name="status" 
                                    id="status"
                                    class="w-full border-gray-300 rounded-md shadow-sm p-2.5 focus:ring-blue-500 focus:border-blue-500 bg-white" 
                                    required>
                                <option value="" disabled>-- Select Academic Status --</option>
                                <option value="Active" {{ old('status', $status->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Probation" {{ old('status', $status->status) == 'Probation' ? 'selected' : '' }}>Probation</option>
                                <option value="Deferred" {{ old('status', $status->status) == 'Deferred' ? 'selected' : '' }}>Deferred</option>
                                <option value="Graduated" {{ old('status', $status->status) == 'Graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="Dismissed" {{ old('status', $status->status) == 'Dismissed' ? 'selected' : '' }}>Dismissed</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 2. SEMESTER --}}
                        <div id="semester-container">
                            <label for="semester" class="block text-gray-700 font-medium mb-2">2. Semester</label>
                            <select name="semester" 
                                    id="semester"
                                    class="w-full border-gray-300 rounded-md shadow-sm p-2.5 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="" disabled selected>-- Select Semester --</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    @php $val = "Semester $i"; @endphp
                                    <option value="{{ $val }}" 
                                        {{ old('semester', $status->semester) == $val ? 'selected' : '' }}>
                                        {{ $val }}
                                    </option>
                                @endfor
                                <option value="Extended" {{ old('semester', $status->semester) == 'Extended' ? 'selected' : '' }}>Extended</option>
                            </select>
                            @error('semester') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 3. EXPECTED COMPLETION / GRADUATION DATE --}}
                        <div id="graduation-date-container">
                            <label for="graduation_date" class="block text-gray-700 font-medium mb-1">
                                3. Expected Completion / Graduation Date
                            </label>
                            
                            {{-- Helper Text Updated --}}
                            <p class="text-xs text-gray-500 mb-2">
                                If active, select expected date. If graduated, select actual date.
                            </p>

                            <input type="date" 
                                   name="graduation_date" 
                                   {{-- Pull date from DB --}}
                                   value="{{ old('graduation_date', $status->graduation_date) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm p-2.5 focus:ring-blue-500 focus:border-blue-500 text-gray-700">
                            
                            @error('graduation_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-4">
                        <a href="{{ route('admin.student_status.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2.5 px-6 rounded hover:bg-blue-700 transition shadow">
                            Update Status
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- SCRIPT TO HANDLE TOGGLING --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusDropdown = document.getElementById('status');
        const semesterContainer = document.getElementById('semester-container');
        const dateContainer = document.getElementById('graduation-date-container');

        function toggleFields() {
            const status = statusDropdown.value;

            // 1. Logic for SEMESTER Field
            if (status === 'Graduated' || status === 'Dismissed') {
                // No semester needed for Graduated or Dismissed students
                if(semesterContainer) semesterContainer.style.display = 'none';
            } else {
                // Show semester for Active, Probation, Deferred
                if(semesterContainer) semesterContainer.style.display = 'block';
            }

            // 2. Logic for DATE Field
            if (status === 'Dismissed') {
                // Hide date only if Dismissed
                if(dateContainer) dateContainer.style.display = 'none';
            } else {
                // SHOW date for everyone else (Active to set Expected Date, Graduated for Actual Date)
                if(dateContainer) dateContainer.style.display = 'block';
            }
        }

        // Run on page load (to set initial state based on DB value)
        toggleFields();

        // Run whenever user changes the dropdown
        statusDropdown.addEventListener('change', toggleFields);
    });
</script>

@endsection