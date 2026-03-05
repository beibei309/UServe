@extends('admin.layout')

@section('content')
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                    Add New Student Status
                </h1>
                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">
                    Create academic status record for a student
                </p>
            </div>
            <a href="{{ route('admin.student_status.index') }}"
               class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-400 hover:to-gray-500 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl transition-all duration-300" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl transition-all duration-300" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <div>
                        <p class="font-medium mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.student_status.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN : STUDENT SELECTION --}}
                <div class="lg:col-span-1">
                    <div class="rounded-xl shadow-xl border transition-all duration-300" 
                         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 px-6 py-4 rounded-t-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-search text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-white font-bold text-lg">1. Select Student</h3>
                                    <p class="text-purple-100 text-sm">Choose target student for status update</p>
                                </div>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-6">
                            @if($selectedStudent)
                                {{-- SELECTED STUDENT DISPLAY --}}
                                <div class="p-6 rounded-xl border transition-all duration-300" 
                                     style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user-check text-green-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-semibold uppercase tracking-wider mb-1 transition-colors duration-300" style="color: var(--text-muted);">
                                                Selected Student
                                            </p>
                                            <p class="font-bold text-lg transition-colors duration-300" style="color: var(--text-primary);">
                                                {{ $selectedStudent->hu_name }}
                                            </p>
                                            <p class="text-sm font-mono transition-colors duration-300" style="color: var(--text-secondary);">
                                                {{ $selectedStudent->hu_student_id ?? 'No Student ID' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="student_id" value="{{ $selectedStudent->hu_id }}">

                            @else
                                {{-- STUDENT SELECTION LIST --}}
                                <div class="h-96 overflow-y-auto rounded-lg border transition-all duration-300"
                                     style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                    <div class="divide-y transition-colors duration-300" style="border-color: var(--border-color);">
                                        @foreach($students as $student)
                                            <label class="flex items-center justify-between p-4 cursor-pointer hover:bg-opacity-50 transition-all duration-300"
                                                   style="background-color: transparent;">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-user text-purple-600 text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                                            {{ $student->hu_name }}
                                                        </p>
                                                        <p class="text-xs font-mono transition-colors duration-300" style="color: var(--text-secondary);">
                                                            {{ $student->hu_student_id ?? 'No Student ID' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <input type="radio"
                                                       name="student_id"
                                                       value="{{ $student->hu_id }}"
                                                       required
                                                       class="w-4 h-4 text-purple-600 border-2 focus:ring-purple-500/20 transition-colors duration-300"
                                                       style="border-color: var(--border-color);">
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @error('student_id')
                                <p class="text-red-500 text-sm mt-3 flex items-center gap-1">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN : STATUS DETAILS --}}
                <div class="lg:col-span-2">
                    <div class="rounded-xl shadow-xl border transition-all duration-300" 
                         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        
                        {{-- Form Header --}}
                        <div class="p-6 sm:p-8 border-b transition-colors duration-300" style="border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-clipboard-list text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Status Details</h2>
                                    <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Configure academic status and timeline</p>
                                </div>
                            </div>
                        </div>

                        {{-- Form Body --}}
                        <div class="p-6 sm:p-8">
                            <div class="space-y-8">

                                {{-- 2. STATUS --}}
                                <div>
                                    <label for="status" class="block text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-graduation-cap mr-2"></i>2. Academic Status
                                    </label>
                                    <select name="status"
                                            id="status"
                                            required
                                            class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-green-500/20 @error('status') border-red-500 @enderror"
                                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                        <option value="" disabled selected>Select Academic Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Probation">Probation</option>
                                        <option value="Deferred">Deferred</option>
                                        <option value="Graduated">Graduated</option>
                                        <option value="Dismissed">Dismissed</option>
                                    </select>
                                    <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Select status first to adjust other field requirements.
                                    </p>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- 3. SEMESTER --}}
                                <div id="semester-container">
                                    <label for="semester" class="block text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-calendar-alt mr-2"></i>3. Current Semester
                                    </label>
                                    <select name="semester"
                                            id="semester"
                                            class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-green-500/20 @error('semester') border-red-500 @enderror"
                                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                        <option value="">Select Semester</option>
                                        @for ($i = 1; $i <= 8; $i++)
                                            <option value="Semester {{ $i }}">Semester {{ $i }}</option>
                                        @endfor
                                        <option value="Final">Final Semester</option>
                                        <option value="N/A">N/A</option>
                                    </select>
                                    @error('semester')
                                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- 4. GRADUATION / COMPLETION DATE --}}
                                <div id="graduation-date-container">
                                    <label for="graduation_date" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-calendar-check mr-2"></i>4. Expected Completion / Graduation Date
                                    </label>
                                    
                                    <div class="mb-3 p-3 rounded-lg border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                        <p class="text-xs flex items-center gap-2 transition-colors duration-300" style="color: var(--text-muted);">
                                            <i class="fas fa-lightbulb text-yellow-500"></i>
                                            If status is Active: Select expected graduation date. If Graduated: Select actual graduation date.
                                        </p>
                                    </div>

                                    <input type="date"
                                           name="graduation_date"
                                           id="graduation_date"
                                           class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-green-500/20 @error('graduation_date') border-red-500 @enderror"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">

                                    @error('graduation_date')
                                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>

                            {{-- SUBMIT BUTTON --}}
                            <div class="mt-8 pt-8 border-t flex flex-col sm:flex-row gap-3 transition-colors duration-300" style="border-color: var(--border-color);">
                                <button type="submit"
                                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-save"></i>
                                    Save Status Record
                                </button>
                                
                                <a href="{{ route('admin.student_status.index') }}" 
                                   class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-400 hover:to-gray-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <div id="adminStudentStatusFormConfig" data-mode="create"></div>
    <script src="{{ asset('js/admin-student-status-form.js') }}"></script>
@endsection
