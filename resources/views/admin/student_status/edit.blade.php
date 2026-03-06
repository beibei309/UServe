@extends('admin.layout')

@section('content')
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                    Edit Student Status
                </h1>
                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">
                    Update academic status and graduation timeline for the student
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

        <form action="{{ route('admin.student_status.update', $status->hss_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN: STUDENT INFO --}}
                <div class="lg:col-span-1">
                    <div class="rounded-xl shadow-xl border transition-all duration-300" 
                         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        
                        {{-- Card Header --}}
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 rounded-t-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-graduate text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-white font-bold text-lg">Student Information</h3>
                                    <p class="text-blue-100 text-sm">Read-only student details</p>
                                </div>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2 transition-colors duration-300" style="color: var(--text-muted);">
                                        Full Name
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-lg border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                        <i class="fas fa-user text-blue-600"></i>
                                        <p class="font-semibold text-lg transition-colors duration-300" style="color: var(--text-primary);">{{ $status->student->hu_name }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2 transition-colors duration-300" style="color: var(--text-muted);">
                                        Student ID
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-lg border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                        <i class="fas fa-id-card text-green-600"></i>
                                        <p class="font-mono font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $status->student->hu_student_id ?? 'Not provided' }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2 transition-colors duration-300" style="color: var(--text-muted);">
                                        Email Address
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-lg border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                        <i class="fas fa-envelope text-purple-600"></i>
                                        <p class="text-sm break-all transition-colors duration-300" style="color: var(--text-secondary);">{{ $status->student->hu_email }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t transition-colors duration-300" style="border-color: var(--border-color);">
                                <div class="flex items-start gap-3 p-3 rounded-lg" style="background-color: #dbeafe; border: 1px solid #93c5fd;">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                                    <p class="text-xs text-blue-800 leading-relaxed">
                                        Student information cannot be changed here. If incorrect, delete this record and create a new one with the correct student.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: STATUS FORM --}}
                <div class="lg:col-span-2">
                    <div class="rounded-xl shadow-xl border transition-all duration-300" 
                         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        
                        {{-- Form Header --}}
                        <div class="p-6 sm:p-8 border-b transition-colors duration-300" style="border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-edit text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Update Status Details</h2>
                                    <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Modify academic status and timeline information</p>
                                </div>
                            </div>
                        </div>

                        {{-- Form Body --}}
                        <div class="p-6 sm:p-8">
                            <div class="space-y-8">
                                
                                {{-- 1. STATUS --}}
                                <div>
                                    <label for="status" class="block text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-graduation-cap mr-2"></i>1. Academic Status
                                    </label>
                                    <select name="status" 
                                            id="status"
                                            class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-green-500/20 @error('status') border-red-500 @enderror"
                                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                            required>
                                        <option value="" disabled>Select Academic Status</option>
                                        <option value="Active" {{ old('status', $status->hss_status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Probation" {{ old('status', $status->hss_status) == 'Probation' ? 'selected' : '' }}>Probation</option>
                                        <option value="Deferred" {{ old('status', $status->hss_status) == 'Deferred' ? 'selected' : '' }}>Deferred</option>
                                        <option value="Graduated" {{ old('status', $status->hss_status) == 'Graduated' ? 'selected' : '' }}>Graduated</option>
                                        <option value="Dismissed" {{ old('status', $status->hss_status) == 'Dismissed' ? 'selected' : '' }}>Dismissed</option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- 2. SEMESTER --}}
                                <div id="semester-container">
                                    <label for="semester" class="block text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-calendar-alt mr-2"></i>2. Current Semester
                                    </label>
                                    <select name="semester" 
                                            id="semester"
                                            class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-green-500/20 @error('semester') border-red-500 @enderror"
                                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                        <option value="" disabled>Select Semester</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="Semester {{ $i }}"
                                                {{ old('semester', $status->hss_semester) == "Semester $i" ? 'selected' : '' }}>
                                                Semester {{ $i }}
                                            </option>
                                        @endfor
                                        <option value="Extended" {{ old('semester', $status->hss_semester) == 'Extended' ? 'selected' : '' }}>Extended Semester</option>
                                    </select>
                                    @error('semester')
                                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- 3. EXPECTED COMPLETION / GRADUATION DATE --}}
                                <div id="graduation-date-container">
                                    <label for="graduation_date" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-calendar-check mr-2"></i>3. Expected Completion / Graduation Date
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
                                           value="{{ old('graduation_date', $status->hss_graduation_date) }}"
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

                            {{-- Actions --}}
                            <div class="mt-8 pt-8 border-t flex flex-col sm:flex-row gap-3 transition-colors duration-300" style="border-color: var(--border-color);">
                                <button type="submit" 
                                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-save"></i>
                                    Update Status
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
    <div id="adminStudentStatusFormConfig" data-mode="edit"></div>
    <script src="{{ asset('js/admin-student-status-form.js') }}"></script>
@endsection
