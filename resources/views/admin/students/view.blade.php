@extends('admin.layout')

@section('content')
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                    Student Profile
                </h1>
                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">
                    View and manage student information and verification status
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

        {{-- BASIC INFO CARD --}}
        <div class="rounded-xl shadow-xl border transition-all duration-300 mb-8" 
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    
                    {{-- Profile Photo --}}
                    <div class="flex-shrink-0 text-center lg:text-left">
                        <img src="{{ asset($student->hu_profile_photo_path) }}" 
                             alt="{{ $student->hu_name }}"                    
                             class="w-32 h-32 lg:w-40 lg:h-40 rounded-full object-cover border-4 shadow-lg mx-auto lg:mx-0 transition-transform hover:scale-105" 
                             style="border-color: var(--border-color);" />
                    </div>

                    {{-- Student Details --}}
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
                            <div>
                                <h2 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                                    {{ $student->hu_name }}
                                </h2>
                                <p class="text-lg mt-1 transition-colors duration-300" style="color: var(--text-muted);">
                                    {{ $student->hu_email }}
                                </p>
                            </div>

                            {{-- VERIFICATION STATUS --}}
                            <div class="flex flex-col sm:flex-row gap-2">
                                @if ($student->hu_verification_status === 'approved')
                                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-bold flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Verified Student
                                    </span>
                                @else
                                    <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-bold flex items-center gap-2">
                                        <i class="fas fa-clock"></i>
                                        Pending Verification
                                    </span>
                                @endif
                                
                                @if ($student->hu_role === 'helper')
                                    <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-bold flex items-center gap-2">
                                        <i class="fas fa-star"></i>
                                        Student Seller
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- STUDENT DETAILS GRID --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--bg-tertiary);">
                                        <i class="fas fa-id-card text-cyan-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Student ID</p>
                                        <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $student->hu_student_id ?? 'Not provided' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--bg-tertiary);">
                                        <i class="fas fa-phone text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Phone Number</p>
                                        <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $student->hu_phone ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--bg-tertiary);">
                                        <i class="fas fa-university text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Faculty</p>
                                        <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $student->hu_faculty ?? 'Not provided' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: var(--bg-tertiary);">
                                        <i class="fas fa-graduation-cap text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Course & Graduation</p>
                                        <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $student->hu_course ?? 'Not provided' }}</p>
                                        @if ($student->graduation_date_display)
                                            <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Graduating: {{ $student->graduation_date_display }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BANNED STATUS --}}
                        @if ($student->hu_is_suspended)
                            <div class="mt-6 p-4 rounded-lg border border-red-200" style="background-color: #fee2e2;">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-ban text-red-600 mt-1"></i>
                                    <div>
                                        <h3 class="font-bold text-red-700">Account Suspended</h3>
                                        <p class="text-sm text-red-600 mt-1">
                                            <strong>Reason:</strong> {{ $student->hu_blacklist_reason }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ACTION BUTTONS --}}
                        <div class="flex flex-col sm:flex-row gap-3 mt-8">
                            <a href="{{ route('admin.students.edit', $student->hu_id) }}"
                               class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
                                <i class="fas fa-edit"></i>
                                Edit Profile
                            </a>

                            @if ($student->hu_is_suspended)
                                <form action="{{ route('admin.students.unban', $student->hu_id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
                                        <i class="fas fa-unlock"></i>
                                        Unban Student
                                    </button>
                                </form>
                            @else
                                <button type="button" data-ban-open data-student-id="{{ $student->hu_id }}"
                                        class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
                                    <i class="fas fa-ban"></i>
                                    Ban Student
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- HELPER VERIFICATION INFO --}}
        @if ($student->hu_role === 'helper')
            <div class="rounded-xl shadow-xl border transition-all duration-300 overflow-hidden mb-8"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-lg">Student Seller Verification</h2>
                            <p class="text-emerald-100 text-sm">Identity verification and service authorization details</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        {{-- Verification Date & Location --}}
                        <div class="space-y-6">
                            <div class="rounded-xl p-6 border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar-check text-emerald-600"></i>
                                    </div>
                                    <h3 class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">Verification Date</h3>
                                </div>
                                
                                @if ($student->hu_helper_verified_at)
                                    <div class="space-y-2">
                                        <p class="font-bold text-lg transition-colors duration-300" style="color: var(--text-primary);">{{ $student->hu_helper_verified_at->format('d M Y') }}</p>
                                        <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $student->hu_helper_verified_at->format('h:i A') }}</p>
                                        <p class="text-xs text-emerald-600 font-medium">✓ Verified and approved</p>
                                    </div>
                                @else
                                    <p class="text-sm italic transition-colors duration-300" style="color: var(--text-muted);">No verification date recorded</p>
                                @endif
                            </div>

                            <div class="rounded-xl p-6 border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                                    </div>
                                    <h3 class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">Location</h3>
                                </div>
                                
                                @if ($student->hu_latitude && $student->hu_longitude)
                                    <div class="space-y-3">
                                        <p class="text-sm font-mono transition-colors duration-300" style="color: var(--text-secondary);">
                                            {{ $student->address ?? 'Coordinates: ' . number_format($student->hu_latitude, 5) . ', ' . number_format($student->hu_longitude, 5) }}
                                        </p>
                                        <a href="https://www.google.com/maps?q={{ $student->hu_latitude }},{{ $student->hu_longitude }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-2 text-sm bg-blue-50 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all">
                                           <i class="fas fa-external-link-alt"></i>
                                           View on Google Maps
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm italic transition-colors duration-300" style="color: var(--text-muted);">No GPS data available</p>
                                @endif
                            </div>
                        </div>

                        {{-- Selfie Verification --}}
                        <div class="lg:border-x lg:px-8 transition-colors duration-300" style="border-color: var(--border-color);">
                            <div class="text-center">
                                <h3 class="font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Identity Verification</h3>
                                
                                @if ($student->hu_selfie_media_path)
                                    <div class="relative group w-56 mx-auto">
                                        <img src="{{ route('admin.verifications.selfie', $student->hu_id) }}"
                                             class="w-full h-72 rounded-xl object-cover border-4 border-white shadow-xl transition-transform group-hover:scale-105"
                                             alt="Identity Verification Photo">
                                        
                                        <button type="button" data-selfie-open data-selfie-url="{{ route('admin.verifications.selfie', $student->hu_id) }}"
                                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-xl flex items-center justify-center">
                                            <span class="bg-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-expand-alt mr-2"></i>
                                                View Full Size
                                            </span>
                                        </button>
                                    </div>
                                    
                                    @if ($student->hu_verification_note)
                                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-sticky-note text-amber-600 mt-1"></i>
                                                <div class="text-left">
                                                    <p class="text-xs font-bold text-amber-800 mb-1">Verification Note:</p>
                                                    <p class="text-sm text-amber-700">{{ $student->hu_verification_note }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-56 h-72 mx-auto flex flex-col items-center justify-center rounded-xl border-2 border-dashed transition-all duration-300"
                                         style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                                        <i class="fas fa-camera text-4xl mb-3 transition-colors duration-300" style="color: var(--text-muted);"></i>
                                        <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-muted);">No Verification Photo</p>
                                        <p class="text-xs mt-1 transition-colors duration-300" style="color: var(--text-muted);">Awaiting identity upload</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Management Actions --}}
                        <div class="rounded-xl p-6 border transition-all duration-300"
                             style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cogs text-red-600"></i>
                                </div>
                                <h3 class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">Management</h3>
                            </div>
                            
                            <div class="space-y-4">
                                <p class="text-sm leading-relaxed transition-colors duration-300" style="color: var(--text-secondary);">
                                    Revoking seller status will immediately disable all service listings and hide the seller profile from the marketplace.
                                </p>
                                
                                <form action="{{ route('admin.students.revoke_helper', $student->hu_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            data-confirm-message="Revoke Seller Status? This user will become a normal student again."
                                            class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white px-4 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium shadow-lg hover:shadow-xl">
                                        <i class="fas fa-user-slash"></i>
                                        Revoke Seller Status
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- SELFIE MODAL --}}
        <div id="selfieModal" 
             class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-sm p-4 transition-all duration-300">
            
            <div class="absolute inset-0 cursor-pointer" data-selfie-close></div>

            <div class="relative max-w-5xl w-full flex flex-col items-center">
                <button type="button" data-selfie-close
                        class="absolute -top-16 right-0 bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 font-medium">
                    <i class="fas fa-times"></i> Close
                </button>

                <img id="modalSelfieImage" src="" 
                     class="w-full h-auto max-h-[85vh] rounded-xl shadow-2xl object-contain border-4 border-white">
                    
                <div class="mt-6 text-center">
                    <p class="text-white text-sm font-medium opacity-90">Student Identity Verification Photo</p>
                    <p class="text-white text-xs opacity-70 mt-1">This image was submitted during the seller verification process</p>
                </div>
            </div>
        </div>
        {{-- HELPER PROFILE --}}
        @if ($student->hu_role === 'helper')
            <div class="rounded-xl shadow-xl border transition-all duration-300 mb-8"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-tie text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Seller Profile</h2>
                            <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Professional skills and experience details</p>
                        </div>
                    </div>

                    @if ($student->work_experience_message)
                        <div class="mb-8 p-6 rounded-xl border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-briefcase text-blue-600"></i>
                                </div>
                                <h3 class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                                    Experience & Description
                                </h3>
                            </div>
                            <div class="text-sm leading-relaxed whitespace-pre-line transition-colors duration-300" style="color: var(--text-secondary);">
                                {{ $student->work_experience_message }}
                            </div>
                        </div>
                    @endif

                    @if ($student->skills)
                        <div class="mb-8 p-6 rounded-xl border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tools text-purple-600"></i>
                                </div>
                                <h3 class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                                    Skills & Expertise
                                </h3>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach (explode(',', $student->skills) as $skill)
                                    <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium border border-purple-200">{{ trim($skill) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- RESUME / CV --}}
                    @if ($student->work_experience_file)
                        <div class="p-6 rounded-xl border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-green-600"></i>
                                </div>
                                <h3 class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                                    Resume / CV
                                </h3>
                            </div>
                            <a href="{{ asset('storage/' . $student->work_experience_file) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white rounded-lg transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                                <i class="fas fa-download"></i>
                                Download Resume (PDF)
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ABOUT --}}
        <div class="rounded-xl shadow-xl border transition-all duration-300 mb-8"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold transition-colors duration-300" style="color: var(--text-primary);">About Student</h2>
                        <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Personal information and bio</p>
                    </div>
                </div>
                
                @if ($student->hu_bio)
                    <div class="p-6 rounded-xl border transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                        <p class="whitespace-pre-line leading-relaxed transition-colors duration-300" style="color: var(--text-secondary);">{{ $student->hu_bio }}</p>
                    </div>
                @else
                    <div class="p-6 rounded-xl border-2 border-dashed text-center transition-all duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                        <i class="fas fa-user-edit text-3xl mb-3 transition-colors duration-300" style="color: var(--text-muted);"></i>
                        <p class="italic transition-colors duration-300" style="color: var(--text-muted);">No bio provided by the student yet.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SYSTEM INFO --}}
        <div class="rounded-xl shadow-xl border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold transition-colors duration-300" style="color: var(--text-primary);">System Information</h2>
                        <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Account creation and update history</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-hashtag text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Student ID</p>
                            <p class="font-bold transition-colors duration-300" style="color: var(--text-primary);">#{{ $student->hu_id }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-plus text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Registered</p>
                            <p class="font-bold transition-colors duration-300" style="color: var(--text-primary);">{{ $student->created_at->format('d M Y') }}</p>
                            <p class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">{{ $student->created_at->format('h:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sync-alt text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Last Updated</p>
                            <p class="font-bold transition-colors duration-300" style="color: var(--text-primary);">{{ $student->updated_at->format('d M Y') }}</p>
                            <p class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">{{ $student->updated_at->format('h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- BAN MODAL --}}
        <div id="banModal" class="hidden fixed inset-0 bg-slate-900 bg-opacity-60 backdrop-blur-md flex items-center justify-center z-50 p-4">
            
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
                                    An email notification will be sent to inform them of this action.
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
                                  placeholder="Please provide a detailed and specific reason for this ban. This will be included in the notification email to the student."
                                  required></textarea>
                        <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">
                            Be specific and professional. This reason will be visible to the student.
                        </p>
                    </div>

                    <form id="banForm" method="POST">@csrf</form>

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
    <div id="adminModuleStudentsViewConfig"
        data-ban-route-template="{{ route('admin.students.ban', 'ID_PLACEHOLDER') }}"
        data-selfie-base-url="{{ url('/admin/students') }}"></div>
    <script src="{{ asset('js/admin-students-view.js') }}"></script>
@endsection
