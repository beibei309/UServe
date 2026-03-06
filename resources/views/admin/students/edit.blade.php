@extends('admin.layout')

@section('content')
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                    Edit Student Profile
                </h1>
                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">
                    Update student information and account settings
                </p>
            </div>
            <a href="{{ route('admin.students.index') }}"
               class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-400 hover:to-gray-500 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-arrow-left"></i>
                Back to Students
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

        <form action="{{ route('admin.students.update', $student->hu_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- PERSONAL INFORMATION CARD --}}
                    <div class="rounded-xl shadow-xl border transition-all duration-300" 
                         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-user text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Personal Information</h2>
                                    <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Basic student details and contact information</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-user-circle mr-2"></i>Full Name
                                    </label>
                                    <input type="text" 
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $student->hu_name) }}"
                                           class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('name') border-red-500 @enderror"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                           required>
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-envelope mr-2"></i>Email Address
                                    </label>
                                    <input type="email" 
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $student->hu_email) }}"
                                           class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-500 @enderror"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                           required>
                                    @error('email')
                                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-phone mr-2"></i>Phone Number
                                    </label>
                                    <input type="text" 
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $student->hu_phone) }}"
                                           class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                           placeholder="e.g., +60123456789">
                                </div>

                                <div>
                                    <label for="student_id" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-id-card mr-2"></i>Student ID
                                    </label>
                                    <input type="text" 
                                           id="student_id"
                                           name="student_id"
                                           value="{{ old('student_id', $student->hu_student_id) }}"
                                           class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                           placeholder="e.g., A12345678">
                                </div>

                                <div>
                                    <label for="faculty" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-university mr-2"></i>Faculty
                                    </label>
                                    <select id="faculty"
                                            name="faculty" 
                                            class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                        <option value="">Select Faculty</option>
                                        <option value="Fakulti Komputeran dan Meta-Teknologi" {{ old('faculty', $student->faculty_display) == 'Fakulti Komputeran dan Meta-Teknologi' ? 'selected' : '' }}>Fakulti Komputeran dan Meta-Teknologi</option>
                                        <option value="Fakulti Bahasa dan Komunikasi" {{ old('faculty', $student->faculty_display) == 'Fakulti Bahasa dan Komunikasi' ? 'selected' : '' }}>Fakulti Bahasa dan Komunikasi</option>
                                        <option value="Fakulti Pembangunan Manusia" {{ old('faculty', $student->faculty_display) == 'Fakulti Pembangunan Manusia' ? 'selected' : '' }}>Fakulti Pembangunan Manusia</option>
                                        <option value="Fakulti Sains dan Matematik" {{ old('faculty', $student->faculty_display) == 'Fakulti Sains dan Matematik' ? 'selected' : '' }}>Fakulti Sains dan Matematik</option>
                                        <option value="Fakulti Pengurusan dan Ekonomi" {{ old('faculty', $student->faculty_display) == 'Fakulti Pengurusan dan Ekonomi' ? 'selected' : '' }}>Fakulti Pengurusan dan Ekonomi</option>
                                        <option value="Fakulti Sains Kemanusiaan" {{ old('faculty', $student->faculty_display) == 'Fakulti Sains Kemanusiaan' ? 'selected' : '' }}>Fakulti Sains Kemanusiaan</option>
                                        <option value="Fakulti Muzik dan Seni Persembahan" {{ old('faculty', $student->faculty_display) == 'Fakulti Muzik dan Seni Persembahan' ? 'selected' : '' }}>Fakulti Muzik dan Seni Persembahan</option>
                                        <option value="Fakulti Seni, Komputeran dan Industri Kreatif" {{ old('faculty', $student->faculty_display) == 'Fakulti Seni, Komputeran dan Industri Kreatif' ? 'selected' : '' }}>Fakulti Seni, Komputeran dan Industri Kreatif</option>
                                        <option value="Fakulti Sains Sukan dan Kejurulatihan" {{ old('faculty', $student->faculty_display) == 'Fakulti Sains Sukan dan Kejurulatihan' ? 'selected' : '' }}>Fakulti Sains Sukan dan Kejurulatihan</option>
                                        <option value="Fakulti Teknikal dan Vokasional" {{ old('faculty', $student->faculty_display) == 'Fakulti Teknikal dan Vokasional' ? 'selected' : '' }}>Fakulti Teknikal dan Vokasional</option>
                                    </select>
                                    @error('faculty')
                                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="course" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-graduation-cap mr-2"></i>Course/Program
                                    </label>
                                    <input type="text" 
                                           id="course"
                                           name="course"
                                           value="{{ old('course', $student->hu_course) }}"
                                           class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                           placeholder="e.g., Bachelor of Computer Science">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- HELPER/SELLER PROFILE CARD --}}
                    <div class="rounded-xl shadow-xl border transition-all duration-300" 
                         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-star text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Seller Profile</h2>
                                    <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Professional skills and services information</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="skills" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-tools mr-2"></i>Skills & Expertise
                                    </label>
                                    <input type="text" 
                                           id="skills"
                                           name="skills"
                                           value="{{ old('skills', $student->skills) }}"
                                           placeholder="e.g., Tutoring, Graphic Design, Web Development, Data Analysis"
                                           class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                    <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Separate multiple skills with commas. These help students find relevant services.
                                    </p>
                                </div>

                                <div>
                                    <label for="work_experience_message" class="block text-sm font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-briefcase mr-2"></i>Experience & Description
                                    </label>
                                    <textarea id="work_experience_message"
                                              name="work_experience_message" 
                                              rows="5"
                                              class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-green-500/20 resize-vertical"
                                              style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                              placeholder="Describe your experience, qualifications, and the services you offer to fellow students...">{{ old('work_experience_message', $student->hu_work_experience_message) }}</textarea>
                                    <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">
                                        <i class="fas fa-lightbulb mr-1"></i>
                                        This description is visible to students browsing services.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="lg:col-span-1 space-y-8">

                    {{-- ACCOUNT STATUS CARD --}}
                    <div class="rounded-xl shadow-xl border transition-all duration-300" 
                         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cog text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold transition-colors duration-300" style="color: var(--text-primary);">Account Status</h2>
                                    <p class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">Verification and account settings</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="verification_status" class="block text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-shield-check mr-2"></i>Verification Status
                                    </label>
                                    <select id="verification_status"
                                            name="verification_status"
                                            class="w-full rounded-lg px-4 py-3 transition-all duration-300 border-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                        <option value="pending"
                                            @if($student->hu_verification_status === 'approved') disabled @endif
                                            {{ $student->hu_verification_status === 'pending' ? 'selected' : '' }}>
                                            🕐 Pending Verification
                                        </option>
                                        <option value="approved"
                                            {{ $student->hu_verification_status === 'approved' ? 'selected' : '' }}>
                                            ✅ Approved (Verified)
                                        </option>
                                        <option value="rejected"
                                            {{ $student->hu_verification_status === 'rejected' ? 'selected' : '' }}>
                                            ❌ Rejected
                                        </option>
                                    </select>

                                    @if($student->hu_verification_status === 'approved')
                                        <div class="mt-3 p-3 rounded-lg border transition-all duration-300" style="background-color: #f0f9ff; border-color: #bae6fd; color: #0c4a6e;">
                                            <p class="text-xs flex items-center gap-2">
                                                <i class="fas fa-lock"></i>
                                                Once approved, the status cannot be reverted to pending.
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="border-t pt-6 transition-colors duration-300" style="border-color: var(--border-color);">
                                    <h3 class="text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                        <i class="fas fa-user-check mr-2"></i>Current Standing
                                    </h3>
                                    
                                    @if($student->hu_is_blacklisted || $student->hu_is_suspended)
                                        <div class="p-4 rounded-lg border border-red-200 transition-all duration-300" style="background-color: #fee2e2; color: #991b1b;">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <span class="font-bold">{{ ucfirst($student->moderationStatusKey()) }}</span>
                                            </div>
                                            <p class="text-sm">
                                                <strong>Reason:</strong> {{ $student->hu_blacklist_reason }}
                                            </p>
                                        </div>
                                    @elseif($student->hu_is_blocked)
                                        <div class="p-4 rounded-lg border border-amber-200 transition-all duration-300" style="background-color: #fef3c7; color: #92400e;">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-pause-circle"></i>
                                                <span class="font-bold">Blocked</span>
                                            </div>
                                            <p class="text-sm">Seller actions are temporarily restricted.</p>
                                        </div>
                                    @else
                                        <div class="p-4 rounded-lg border border-green-200 transition-all duration-300" style="background-color: #dcfce7; color: #166534;">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-check-circle"></i>
                                                <span class="font-bold">Active Account</span>
                                            </div>
                                            <p class="text-sm mt-1">Student account is in good standing.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS CARD --}}
                    <div class="rounded-xl shadow-xl border transition-all duration-300" 
                         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                        <div class="p-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider mb-4 transition-colors duration-300" style="color: var(--text-muted);">
                                Actions
                            </h3>
                            
                            <div class="space-y-3">
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-save"></i>
                                    Save Changes
                                </button>

                                <a href="{{ route('admin.students.index') }}"
                                   class="w-full bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-400 hover:to-gray-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
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
