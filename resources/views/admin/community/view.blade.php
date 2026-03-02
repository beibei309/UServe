@extends('admin.layout')

@section('content')

    <div class="max-w-4xl mx-auto">

        <!-- Back Button -->
        <a href="{{ route('admin.community.index') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
            ← Back to Community List
        </a>

        <!-- Profile Header -->
        <div class="bg-white shadow rounded-lg p-6 flex gap-6 items-center">

            <!-- Profile Photo -->
            <div class="h-32 w-32 rounded-full overflow-hidden border">
                @php
                    $path = $user->hu_profile_photo_path;
                    // 1. Check if external URL
                    if (Str::startsWith($path, ['http://', 'https://'])) {
                        $imageUrl = $path;
                    } 
                    // 2. Check if file exists in 'storage' (public/storage/...)
                    elseif ($path && file_exists(public_path('storage/' . $path))) {
                        $imageUrl = asset('storage/' . $path);
                    } 
                    // 3. Fallback: Assume it's in public root (public/...)
                    elseif ($path) {
                        $imageUrl = asset($path);
                    } else {
                        $imageUrl = asset('uploads/profile/default.png');
                    }
                @endphp
                <img src="{{ $imageUrl }}" class="w-full h-full object-cover" alt="{{ $user->hu_name }}" />
            </div>
            <div class="flex-1">

                <!-- Name -->
                <h1 class="text-3xl font-bold">{{ $user->hu_name }}</h1>

                <!-- Email + Phone -->
                <p class="text-gray-600">{{ $user->hu_email }}</p>
                <p class="text-gray-600">{{ $user->hu_phone ?? 'No phone provided' }}</p>

                <!-- Verification -->
                <div class="mt-2">
                    @if ($user->hu_verification_status == 'approved')
                        <span class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-full">
                            Approved
                        </span>
                    @elseif($user->hu_verification_status == 'pending')
                        <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 rounded-full">
                            Pending
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-full">
                            Rejected
                        </span>
                    @endif
                </div>

                <!-- Blacklist -->
                @if ($user->hu_is_blacklisted)
                    <div class="mt-2">
                        <span class="px-3 py-1 text-sm bg-red-200 text-red-800 rounded-full">
                           Suspended
                        </span>

                        <p class="text-sm text-red-700 mt-1">
                            Reason: {{ $user->hu_blacklist_reason }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-2">

                <!-- Edit -->
                <a href="{{ route('admin.community.edit', $user->hu_id) }}"
                    class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                    Edit User
                </a>

                <!-- Blacklist / Unblacklist -->
                @if ($user->hu_is_blacklisted)
                    <form action="{{ route('admin.community.unblacklist', $user->hu_id) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm w-full">
                            Remove Blacklist
                        </button>
                    </form>
                @else
                    <button onclick="openBlacklistModal({{ $user->hu_id }})" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm w-full">
                        Suspend User
                    </button>
                @endif

                <!-- Delete -->
                <form action="{{ route('admin.community.delete', $user->hu_id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-black text-sm w-full">
                        Delete User
                    </button>
                </form>

            </div>

        </div>

        <!-- VERIFICATION DOCUMENTS (New Section) -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 mt-6">
            <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-4">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fas fa-shield-check text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-800">Verification Assets</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="flex flex-col h-full">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-camera"></i> Live Selfie Check
                    </h3>

                    @if ($user->hu_selfie_media_path)
                        <div
                            class="relative group border border-slate-200 rounded-xl overflow-hidden bg-slate-50 flex-grow">
                            <img src="{{ route('admin.verifications.selfie', $user->hu_id) }}"
                                class="w-full h-72 object-cover transition duration-300 group-hover:scale-105"
                                alt="Live Selfie">

                            <div
                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button onclick="openSelfieModal('{{ route('admin.verifications.selfie', $user->hu_id) }}')"
                                    class="bg-white text-slate-900 px-4 py-2 rounded-full font-semibold text-sm shadow-xl">
                                    <i class="fas fa-expand-arrows-alt mr-1"></i> View Full Size
                                </button>
                            </div>

                            @if ($user->hu_verification_note)
                                <div
                                    class="absolute bottom-3 left-3 right-3 bg-amber-50/90 backdrop-blur border border-amber-200 p-2 rounded-lg">
                                    <p class="text-[10px] uppercase font-bold text-amber-700 leading-tight">Verification
                                        Challenge</p>
                                    <p class="text-sm text-amber-900 font-medium">{{ $user->hu_verification_note }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div
                            class="flex flex-col items-center justify-center h-72 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50 text-slate-400">
                            <i class="fas fa-user-slash text-4xl mb-2"></i>
                            <p class="text-sm">No selfie uploaded</p>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col h-full">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-file-invoice"></i> Official Proof Document
                    </h3>

                    @if ($user->hu_verification_document_path)
                        <div
                            class="border border-slate-200 rounded-xl p-6 bg-white flex flex-col items-center justify-center h-72">
                            <div
                                class="w-20 h-20 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-file-alt text-3xl"></i>
                            </div>
                            <h4 class="text-slate-900 font-bold">Verification Document</h4>
                            <p class="text-slate-500 text-xs mb-6">Stored securely in protected local storage</p>

                            <button onclick="document.getElementById('modalDocumentFrame').src='{{ route('admin.verifications.document', $user->hu_id) }}'; document.getElementById('documentModal').classList.remove('hidden')"
        class="flex items-center gap-2 px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl w-full justify-center">
    <i class="fas fa-eye"></i> Preview Document
</button>
                        </div>
                    @else
                        <div
                            class="flex flex-col items-center justify-center h-72 border-2 border-dashed border-red-100 rounded-xl bg-red-50 text-red-400">
                            <i class="fas fa-file-excel text-4xl mb-2"></i>
                            <p class="text-sm font-medium">No document uploaded</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 border-t border-slate-100 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Geographic Verification</h3>
                        <p class="text-xs text-slate-500">Last known location based on IP or GPS coordinates</p>
                    </div>
                    @if ($user->hu_latitude && $user->hu_longitude)
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active Tracking
                        </span>
                    @endif
                </div>

                @if ($user->hu_latitude && $user->hu_longitude)
                    <div class="relative">
                        <div id="map" class="w-full h-64 rounded-xl border border-slate-200 shadow-sm z-0"></div>
                        <div
                            class="mt-3 flex flex-wrap gap-4 text-xs font-mono text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <p><span class="text-slate-400">LAT:</span> {{ $user->hu_latitude }}</p>
                            <p><span class="text-slate-400">LNG:</span> {{ $user->hu_longitude }}</p>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $user->hu_latitude }},{{ $user->hu_longitude }}"
                                target="_blank"
                                class="text-indigo-600 font-bold hover:text-indigo-800 ml-auto flex items-center gap-1">
                                VIEW ON GOOGLE MAPS <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <div
                        class="bg-slate-50 border border-slate-200 border-dashed rounded-xl p-12 text-center text-slate-400">
                        <i class="fas fa-map-marked-alt text-4xl mb-3 opacity-20"></i>
                        <p class="text-sm italic">Location coordinates not available for this user.</p>
                    </div>
                @endif
            </div>
        </div>
        <div id="documentModal"
            class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/90 backdrop-blur-md p-4">
            <div class="absolute inset-0" onclick="closeDocumentModal()"></div>

            <div class="relative max-w-5xl w-full h-[90vh] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col">
                <div class="p-4 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-file-contract text-indigo-600"></i> Proof of Identity Document
                    </h3>
                    <button onclick="closeDocumentModal()" class="text-slate-400 hover:text-slate-600 transition p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="flex-grow bg-slate-200 relative">
                    <div id="docLoading" class="absolute inset-0 flex items-center justify-center bg-white z-10">
                        <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-indigo-600">
                        </div>
                    </div>
                    <iframe id="modalDocumentFrame" src="" class="w-full h-full border-none"
                        onload="document.getElementById('docLoading').classList.add('hidden')"></iframe>
                </div>
            </div>
        </div>

        <div id="selfieModal"
            class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-sm transition-all duration-300">

            <div class="absolute inset-0 cursor-zoom-out" onclick="closeSelfieModal()"></div>

            <div class="relative max-w-4xl w-full flex flex-col items-center">
                <button onclick="closeSelfieModal()"
                    class="absolute -top-14 right-0 md:-right-10 text-white/70 hover:text-white transition-colors flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-widest">Close</span>
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>

                <div class="bg-white p-2 rounded-2xl shadow-2xl">
                    <img id="modalSelfieImage" src=""
                        class="w-full h-auto max-h-[80vh] rounded-xl object-contain shadow-inner" alt="Enlarged Selfie">
                </div>

                <div class="mt-4 text-center">
                    <p class="text-white font-medium">Live Selfie Verification</p>
                    @php
                        $createdAt = $user->hu_created_at ?? $user->created_at;
                    @endphp
                    <p class="text-slate-400 text-xs">Captured on: {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d M Y, H:i A') : '-' }}</p>
                </div>
            </div>
        </div>


        <!-- ACCOUNT INFO -->
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h2 class="text-xl font-semibold mb-3">Account Information</h2>

            @php
                $registeredAt = $user->hu_created_at ?? $user->created_at;
                $updatedAt = $user->hu_updated_at ?? $user->updated_at;
            @endphp
            <p class="text-gray-700"><strong>User ID:</strong> {{ $user->hu_id }}</p>
            <p class="text-gray-700"><strong>Registered On:</strong> {{ $registeredAt ? \Carbon\Carbon::parse($registeredAt)->format('d M Y, h:i A') : '-' }}</p>
            <p class="text-gray-700"><strong>Last Updated:</strong> {{ $updatedAt ? \Carbon\Carbon::parse($updatedAt)->format('d M Y, h:i A') : '-' }}</p>

        </div>

    </div>


    <!-- BLACKLIST MODAL -->
    <div id="blacklistModal" class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-xl">

            <h2 class="text-xl font-bold mb-4">Suspend User</h2>
            <p class="text-gray-600 mb-3">Please provide a reason:</p>

            <textarea id="blacklistReason" rows="3" class="w-full border rounded p-2 focus:ring focus:ring-red-300"
                placeholder="Write reason..."></textarea>

            <div class="mt-5 flex justify-end gap-3">
                <button onclick="closeBlacklistModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                    Cancel
                </button>

                <button onclick="submitBlacklist()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                    Confirm
                </button>

            </div>
        </div>
    </div>


    <script>
        // --- BLACKLIST MODAL LOGIC ---
        let selectedUserId = null;

        function openBlacklistModal(id) {
            selectedUserId = id;
            document.getElementById("blacklistModal").classList.remove("hidden");
        }

        function closeBlacklistModal() {
            document.getElementById("blacklistModal").classList.add("hidden");
            document.getElementById("blacklistReason").value = "";
        }

        function submitBlacklist() {
            const reason = document.getElementById("blacklistReason").value.trim();

            if (!reason) {
                alert("Please enter account suspended reason.");
                return;
            }

            let form = document.createElement("form");
            form.method = "POST";
            // Replace PLACEHOLDER with actual ID
            // NOTE: We used 'selectedUserId' but in View page we might just need {{ $user->hu_id }} 
            // BUT to keep it consistent with the JS function signature, we'll use the variable.
            form.action = "{{ route('admin.community.blacklist', 'ID_PLACEHOLDER') }}"
                .replace('ID_PLACEHOLDER', selectedUserId);

            let token = document.createElement("input");
            token.type = "hidden";
            token.name = "_token";
            token.value = "{{ csrf_token() }}";
            form.appendChild(token);

            let reasonInput = document.createElement("input");
            reasonInput.type = "hidden";
            reasonInput.name = "blacklist_reason";
            reasonInput.value = reason;
            form.appendChild(reasonInput);

            document.body.appendChild(form);
            form.submit();
        }

        function openDocumentModal(userId) {
            const modal = document.getElementById('documentModal');
            const frame = document.getElementById('modalDocumentFrame');
            const loader = document.getElementById('docLoading');

            loader.classList.remove('hidden');
            // Point this to secure document route
            frame.src = `/admin/verifications/${userId}/document`;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDocumentModal() {
            const modal = document.getElementById('documentModal');
            const frame = document.getElementById('modalDocumentFrame');
            modal.classList.add('hidden');
            frame.src = ''; // Clear src to stop loading/video
            document.body.style.overflow = 'auto';
        }

        function openSelfieModal(imageUrl) {
            const modal = document.getElementById('selfieModal');
            const modalImg = document.getElementById('modalSelfieImage');

            // 1. Set the source
            modalImg.src = imageUrl;

            // 2. Show the modal with a fade-in effect (Tailwind utility)
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // 3. Lock body scroll
            document.body.style.overflow = 'hidden';
        }

        function closeSelfieModal() {
            const modal = document.getElementById('selfieModal');

            // 1. Hide modal
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // 2. Unlock body scroll
            document.body.style.overflow = 'auto';
        }

        // Close on "Escape" key press
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeSelfieModal();
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan data lat/long wujud sebelum run script
            const lat = {{ $user->hu_latitude ?? 'null' }};
            const lng = {{ $user->hu_longitude ?? 'null' }};

            if (lat && lng) {
                // Initialize map
                const map = L.map('map').setView([lat, lng], 15);

                // Add OpenStreetMap layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Add Marker
                const userIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', // Icon marker
                    iconSize: [38, 38],
                    iconAnchor: [19, 38],
                    popupAnchor: [0, -38]
                });

                L.marker([lat, lng], {
                        icon: userIcon
                    })
                    .addTo(map)
                    .bindPopup("<b>{{ $user->hu_name }}</b><br>Location Registered.")
                    .openPopup();
            }
        });
    </script>

@endsection
