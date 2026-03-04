@extends('admin.layout')

@section('content')
<div class="px-4 sm:px-6">

<div class="max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Add New Student Status</h2>
        <a href="{{ route('admin.student_status.index') }}"
           class="transition-colors duration-300 text-sm hover:text-cyan-400"
           style="color: var(--text-secondary);">
            &larr; Back to List
        </a>
    </div>

    <form action="{{ route('admin.student_status.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- LEFT COLUMN : STUDENT SELECTION --}}
            <div class="md:col-span-1">
    <label class="block font-bold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
        1. Select Student
    </label>

    <div class="shadow-xl rounded-lg p-4 border transition-all duration-300"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">

        @if($selectedStudent)
            {{-- AUTO-SELECTED STUDENT --}}
            <div class="p-4 border rounded-lg transition-all duration-300"
                 style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                <p class="text-sm mb-1 transition-colors duration-300" style="color: var(--text-secondary);">Selected Student</p>
                <p class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                    {{ $selectedStudent->hu_name }}
                </p>
                <p class="text-xs transition-colors duration-300" style="color: var(--text-muted);">
                    {{ $selectedStudent->hu_student_id ?? 'No Matric' }}
                </p>
            </div>

            {{-- HIDDEN INPUT --}}
            <input type="hidden" name="student_id" value="{{ $selectedStudent->hu_id }}">

        @else
            {{-- NORMAL SELECTION LIST --}}
            <div class="h-[420px] overflow-y-auto border rounded-lg transition-all duration-300"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <ul class="divide-y transition-colors duration-300" style="border-color: var(--border-color);">
                    @foreach($students as $student)
                        <li class="hover:opacity-80 transition-opacity duration-300">
                            <label class="flex items-center justify-between px-4 py-3 cursor-pointer">
                                <div>
                                    <span class="block font-medium text-sm transition-colors duration-300" style="color: var(--text-primary);">
                                        {{ $student->hu_name }}
                                    </span>
                                    <span class="block text-xs transition-colors duration-300" style="color: var(--text-secondary);">
                                        {{ $student->hu_student_id ?? 'No Matric' }}
                                    </span>
                                </div>

                                <input type="radio"
                                       name="student_id"
                                       value="{{ $student->hu_id }}"
                                       required
                                       class="form-radio text-cyan-500 h-4 w-4 transition-colors duration-300"
                                       style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @error('student_id')
        <p class="text-red-400 text-xs mt-1">Please select a student.</p>
    @enderror
</div>

            {{-- RIGHT COLUMN : STATUS DETAILS --}}
            <div class="md:col-span-2">
                <div class="p-6 rounded-lg shadow-xl border h-full transition-all duration-300"
                     style="background-color: var(--bg-secondary); border-color: var(--border-color);">

                    <h3 class="text-lg font-semibold mb-6 border-b pb-2 transition-colors duration-300"
                        style="color: var(--text-primary); border-color: var(--border-color);">
                        Status Details
                    </h3>

                    <div class="space-y-6">

                        {{-- STATUS --}}
                        <div>
                            <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                2. Academic Status
                            </label>

                            <select name="status"
                                    id="status"
                                    required
                                    class="w-full border rounded-md shadow-sm p-2.5 transition-colors duration-300"
                                    style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);"
                                    onfocus="this.style.borderColor = '#06b6d4'; this.style.boxShadow = '0 0 0 2px rgba(6, 182, 212, 0.2)';">
                                <option value="" disabled selected>
                                    -- Select Academic Status --
                                </option>
                                <option value="Active">Active</option>
                                <option value="Probation">Probation</option>
                                <option value="Deferred">Deferred</option>
                                <option value="Graduated">Graduated</option>
                                <option value="Dismissed">Dismissed</option>
                            </select>

                            <p class="text-xs mt-1 transition-colors duration-300" style="color: var(--text-muted);">
                                Select status first to adjust other fields.
                            </p>

                            @error('status')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SEMESTER --}}
                        <div id="semester-container">
    <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-primary);">
        3. Current Semester
    </label>

    <select name="semester"
            id="semester"
            class="w-full border rounded-md shadow-sm p-2.5 transition-colors duration-300"
            style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);"
            onfocus="this.style.borderColor = '#06b6d4'; this.style.boxShadow = '0 0 0 2px rgba(6, 182, 212, 0.2)';">
        <option value="" style="background-color: var(--bg-tertiary); color: var(--text-primary);">-- Select Semester --</option>

        @for ($i = 1; $i <= 8; $i++)
            <option value="Semester {{ $i }}" style="background-color: var(--bg-tertiary); color: var(--text-primary);">
                Semester {{ $i }}
            </option>
        @endfor

        <option value="Final" style="background-color: var(--bg-tertiary); color: var(--text-primary);">Final</option>
        <option value="N/A" style="background-color: var(--bg-tertiary); color: var(--text-primary);">N/A</option>
    </select>

    @error('semester')
        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>


                        {{-- GRADUATION / COMPLETION DATE --}}
                        <div id="graduation-date-container">
                            <label class="block font-medium mb-1 transition-colors duration-300" style="color: var(--text-primary);">
                                4. Expected Completion / Graduation Date
                            </label>

                            <p class="text-xs mb-2 transition-colors duration-300" style="color: var(--text-muted);">
                                If active, select expected completion date.
                                If graduated, select actual graduation date.
                            </p>

                            <input type="date"
                                   name="graduation_date"
                                   class="w-full border rounded-md shadow-sm p-2.5 transition-colors duration-300"
                                   style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);"
                                   onfocus="this.style.borderColor = '#06b6d4'; this.style.boxShadow = '0 0 0 2px rgba(6, 182, 212, 0.2)';">

                            @error('graduation_date')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- SUBMIT --}}
                    <div class="mt-8 pt-6 border-t flex justify-end transition-colors duration-300"
                         style="border-color: var(--border-color);">
                        <button type="submit"
                                class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold py-2.5 px-6 rounded
                                       transition-all duration-300 shadow-sm">
                            Save Status Record
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>

</div>

{{-- TOGGLE SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const status = document.getElementById('status');
    const semesterBox = document.getElementById('semester-container');
    const semester = document.getElementById('semester');
    const dateBox = document.getElementById('graduation-date-container');

    function toggleFields() {
        const value = status.value;

        // ===== SEMESTER RULES =====
        if (value === 'Graduated') {
            semester.value = 'Final';
            semester.disabled = true;
            semesterBox.classList.add('opacity-60');
        } 
        else if (value === 'Dismissed') {
            semester.value = 'N/A';
            semester.disabled = true;
            semesterBox.classList.add('opacity-60');
        } 
        else {
            semester.disabled = false;
            semester.value = '';
            semesterBox.classList.remove('opacity-60');
        }

        // ===== DATE RULES =====
        if (value === 'Dismissed') {
            dateBox.style.display = 'none';
        } else {
            dateBox.style.display = 'block';
        }
    }

    toggleFields();
    status.addEventListener('change', toggleFields);
});
</script>

@endsection