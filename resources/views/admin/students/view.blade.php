@extends('admin.layout')

@section('content')

    <div class="max-w-4xl mx-auto">

        {{-- BACK --}}
        <a href="{{ route('admin.students.index') }}"
            class="text-blue-600 hover:text-blue-800 text-sm mb-4 inline-flex items-center gap-1">
            ← Back to Students
        </a>

        {{-- BASIC INFO --}}
        <div class="bg-white shadow-sm rounded-lg p-6 flex flex-col md:flex-row gap-6 border border-gray-200">

            <div class="shrink-0">
                @php
                    use Illuminate\Support\Facades\Storage;
                @endphp

                <img   src="{{ asset($student->hu_profile_photo_path) }}" 
    alt="{{ $student->hu_name }}"                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm" />
            </div>

            <div class="flex-1">

                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold">{{ $student->hu_name }}</h1>
                        <p class="text-gray-500">{{ $student->hu_email }}</p>
                    </div>

                    {{-- VERIFICATION --}}
                    @if ($student->hu_verification_status === 'approved')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                            Verified
                        </span>
                    @else
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                            Pending
                        </span>
                    @endif
                </div>

                {{-- DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 text-sm">

                    <div class="space-y-2">
                        <p><strong>Student ID:</strong> {{ $student->hu_student_id ?? '-' }}</p>
                        <p><strong>Phone:</strong> {{ $student->hu_phone ?? '-' }}</p>
                        <p>
                            <strong>Role:</strong>
                            @if ($student->hu_role === 'helper')
                                <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-bold">
                                    Seller
                                </span>
                            @else
                                <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-bold">
                                    Student
                                </span>
                            @endif
                        </p>
                    </div>

                    <div class="space-y-2">
                        <p><strong>Faculty:</strong> {{ $student->hu_faculty ?? '-' }}</p>
                        <p><strong>Course:</strong> {{ $student->hu_course ?? '-' }}</p>
                        <p>
                            <strong>Graduation:</strong>
                            @if ($student->studentStatus && $student->studentStatus->graduation_date)
                                {{ \Carbon\Carbon::parse($student->studentStatus->graduation_date)->format('d M Y') }}
                            @else
                                <span class="text-gray-400 italic">Not set</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- BANNED --}}
                @if ($student->hu_is_suspended)
                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded">
                        <strong class="text-red-700">Student Banned</strong>
                        <p class="text-sm text-red-600 mt-1">
                            Reason: {{ $student->hu_blacklist_reason }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- ACTIONS --}}
            <div class="flex flex-col gap-3">
                <a href="{{ route('admin.students.edit', $student->hu_id) }}"
                    class="px-4 py-2 border rounded text-sm text-center">
                    Edit Profile
                </a>

                @if ($student->hu_is_suspended)
                    <form action="{{ route('admin.students.unban', $student->hu_id) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded text-sm w-full">
                            Unban
                        </button>
                    </form>
                @else
                    <button onclick="openBanModal({{ $student->hu_id }})"
                        class="px-4 py-2 bg-red-600 text-white rounded text-sm">
                        Ban
                    </button>
                @endif
            </div>
        </div>

        {{-- HELPER VERIFICATION INFO --}}
       @if ($student->hu_role === 'helper')
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-8">
        <div class="bg-emerald-600 px-6 py-3 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-white text-lg"></i>
            <h2 class="text-white font-semibold tracking-wide">STUDENT SELLER VERIFICATION</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="space-y-6">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Verified Since</label>
                        @if ($student->helper_verified_at)
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-emerald-50 rounded-lg">
                                    <i class="fa-regular fa-calendar-check text-emerald-600"></i>
                                </div>
                                <div>
                                    <p class="text-gray-900 font-bold">{{ $student->helper_verified_at->format('d M Y') }}</p>
                                    <p class="text-sm text-gray-500">{{ $student->helper_verified_at->format('h:i A') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-400 italic text-sm">No verification date recorded</p>
                        @endif
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">📍 Last Known Location</label>
                        @if ($student->latitude && $student->longitude)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <p class="text-xs font-mono text-gray-600 leading-relaxed">
                                    {{ $student->address ?? 'Coordinates: ' . number_format($student->latitude, 5) . ', ' . number_format($student->longitude, 5) }}
                                </p>
                                <a href="https://www.google.com/maps?q={{ $student->latitude }},{{ $student->longitude }}" 
                                   target="_blank" 
                                   class="text-xs text-emerald-600 font-bold hover:underline mt-2 inline-block">
                                   View on Google Maps →
                                </a>
                            </div>
                        @else
                            <p class="text-gray-400 italic text-sm">No GPS data available</p>
                        @endif
                    </div>
                </div>

                <div class="lg:border-x lg:px-8 border-gray-100">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-3">Live Selfie Identity</label>
                        @if ($student->hu_selfie_media_path)
                        <div class="relative group w-48 mx-auto lg:mx-0">
                            <img src="{{ route('admin.verifications.selfie', $student->hu_id) }}"
                                 class="w-48 h-60 rounded-xl object-cover border-4 border-white shadow-md transition-transform group-hover:scale-[1.02]"
                                 alt="Live Selfie">
                            
                            <button onclick="openSelfieModal('{{ route('admin.verifications.selfie', $student->hu_id) }}')"
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-xl flex items-center justify-center">
                                <span class="bg-white px-3 py-1 rounded-full text-xs font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                    Click to Enlarge
                                </span>
                            </button>
                        </div>
                        
                        @if ($student->hu_verification_note)
                            <div class="mt-3 p-2 bg-amber-50 border border-amber-100 rounded text-xs text-amber-800">
                                <span class="font-bold">Challenge Note:</span> {{ $student->hu_verification_note }}
                            </div>
                        @endif
                    @else
                        <div class="h-48 flex flex-col items-center justify-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <i class="fa-solid fa-camera text-gray-300 text-3xl mb-2"></i>
                            <p class="text-gray-400 text-xs">No Selfie Uploaded</p>
                        </div>
                    @endif
                </div>

                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-4">Management</label>
                    
                    <div class="space-y-4">
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Revoking status will immediately disable all service listings and hide the seller profile from the S2U marketplace.
                        </p>
                        
                        <form action="{{ route('admin.students.revoke_helper', $student->hu_id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Revoke Seller Status? This user will become a normal student again.')"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white font-bold text-xs rounded-lg transition-all shadow-sm">
                                <i class="fa-solid fa-user-slash"></i>
                                REVOKE SELLER STATUS
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endif
<div id="selfieModal" 
     class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm p-4 transition-all duration-300">
    
    <div class="absolute inset-0 cursor-pointer" onclick="closeSelfieModal()"></div>

    <div class="relative max-w-4xl w-full flex flex-col items-center">
        <button onclick="closeSelfieModal()" 
                class="absolute -top-12 right-0 text-white hover:text-gray-300 flex items-center gap-2 font-bold tracking-widest text-xs">
            <i class="fa-solid fa-xmark text-xl"></i> CLOSE
        </button>

        <img id="modalSelfieImage" src="" 
             class="w-full h-auto max-h-[85vh] rounded-lg shadow-2xl object-contain border-4 border-white bg-white">
            
        <p class="text-white mt-4 text-xs font-medium tracking-widest uppercase opacity-70">Identity Verification Image</p>
    </div>
</div>
<script>
    function openSelfieModal(imageUrl) {
        const modal = document.getElementById('selfieModal');
        const modalImg = document.getElementById('modalSelfieImage');

        if (modal && modalImg) {
            // 1. Set the source of the modal image to the URL provided
            modalImg.src = imageUrl; 
            
            // 2. Remove the 'hidden' class to show it
            modal.classList.remove('hidden');
            
            // 3. Prevent the background page from scrolling
            document.body.style.overflow = 'hidden'; 
        }
    }

    function closeSelfieModal() {
        const modal = document.getElementById('selfieModal');
        if (modal) {
            // 1. Add the 'hidden' class back
            modal.classList.add('hidden');
            
            // 2. Re-enable background scrolling
            document.body.style.overflow = 'auto';
            
            // 3. Clear the image src to save memory
            document.getElementById('modalSelfieImage').src = '';
        }
    }

    // Close the modal if the admin presses the "Escape" key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeSelfieModal();
        }
    });
</script>

        {{-- HELPER PROFILE --}}
        @if ($student->hu_role === 'helper')
            <div class="bg-white shadow-sm rounded-lg p-6 mt-6 border border-gray-200">
                <div class="flex items-center gap-2 mb-4">
                    <h2 class="text-xl font-semibold">Helper Profile</h2>
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded">
                        Student Seller
                    </span>
                </div>

                @if ($student->work_experience_message)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                            Experience / Description
                        </h3>

                        <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                            {{ $student->work_experience_message }}
                        </div>
                    </div>
                @endif

                @if ($student->skills)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                            Skills
                        </h3>

                        <ul class="list-disc list-inside space-y-1 text-gray-700 text-sm">
                            @foreach (explode(',', $student->skills) as $skill)
                                <li>{{ trim($skill) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- RESUME / CV --}}
                @if ($student->work_experience_file)
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">
                            Resume / CV
                        </h3>

                        <a href="{{ asset('storage/' . $student->work_experience_file) }}" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50
                  border border-blue-200 text-blue-700 rounded-lg
                  hover:bg-blue-100 transition text-sm font-medium">

                            View Resume (PDF)
                        </a>
                    </div>
                @endif

            </div>
        @endif

        {{-- ABOUT --}}
        <div class="bg-white shadow-sm rounded-lg p-6 mt-6 border border-gray-200">
            <h2 class="text-xl font-semibold mb-3">About</h2>
            @if ($student->hu_bio)
                <p class="text-gray-700 whitespace-pre-line">{{ $student->hu_bio }}</p>
            @else
                <p class="text-gray-400 italic">No bio provided.</p>
            @endif
        </div>

        {{-- SYSTEM INFO --}}
        <div class="bg-white shadow-sm rounded-lg p-6 mt-6 border border-gray-200">
            <h2 class="text-sm font-bold text-gray-500 uppercase mb-4">Student Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div><strong>ID:</strong> #{{ $student->hu_id }}</div>
                <div><strong>Registered:</strong> {{ $student->created_at->format('d M Y') }}</div>
                <div><strong>Updated:</strong> {{ $student->updated_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    {{-- BAN MODAL --}}
    <div id="banModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h2 class="text-lg font-bold mb-3">Ban Student</h2>

            <textarea id="banReason" rows="3" class="w-full border rounded p-2 mb-4" placeholder="Reason for ban..."></textarea>

            <form id="banForm" method="POST">@csrf</form>

            <div class="flex justify-end gap-2">
                <button onclick="closeBanModal()" class="px-4 py-2 border rounded">Cancel</button>
                <button onclick="submitBan()" class="px-4 py-2 bg-red-600 text-white rounded">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
        let selectedStudentId = null;

        function openBanModal(id) {
            selectedStudentId = id;
            document.getElementById('banModal').classList.remove('hidden');
        }

        function closeBanModal() {
            document.getElementById('banModal').classList.add('hidden');
            document.getElementById('banReason').value = '';
        }

        function submitBan() {
            const reason = document.getElementById('banReason').value.trim();
            if (!reason) return alert('Please enter a reason.');

            const form = document.getElementById('banForm');
            // Use route() to handle base paths correctly
            form.action = "{{ route('admin.students.ban', 'ID_PLACEHOLDER') }}"
                          .replace('ID_PLACEHOLDER', selectedStudentId);


            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'blacklist_reason';
            input.value = reason;
            form.appendChild(input);

            form.submit();
        }

        // Helper Selfie Modal
        function openHelperSelfieModal(studentId) {
            const modal = document.createElement('div');
            modal.id = 'helperSelfieModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
            modal.onclick = () => modal.remove();

            modal.innerHTML = `
        <div class="relative max-w-4xl max-h-full" onclick="event.stopPropagation()">
            <button onclick="this.closest('#helperSelfieModal').remove()" 
                    class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">
                ×
            </button>
            <img src="/admin/students/${studentId}/selfie" 
                 class="max-w-full max-h-[90vh] rounded-lg shadow-2xl" 
                 alt="Helper Verification Selfie">
        </div>
    `;

            document.body.appendChild(modal);
        }
    </script>

@endsection
