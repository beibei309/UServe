@extends('admin.layout')

@section('content')
<div class="px-4 sm:px-6">

    <div class="max-w-4xl mx-auto">

        <!-- Back Button -->
        <a href="{{ route('admin.community.index') }}" 
           class="hover:text-cyan-400 text-sm mb-4 inline-block transition-colors duration-300"
           style="color: var(--text-secondary);">
            ← Back to Community List
        </a>

        <!-- Profile Header -->
        <div class="shadow rounded-lg p-6 flex flex-col md:flex-row gap-6 items-start md:items-center border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">

            <!-- Profile Photo -->
            <div class="h-32 w-32 rounded-full overflow-hidden border transition-colors duration-300 shrink-0"
                 style="border-color: var(--border-color);">
                <img src="{{ $user->profile_image_url }}" class="w-full h-full object-cover" alt="{{ $user->hu_name }}" />
            </div>
            <div class="flex-1">

                <!-- Name -->
                <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">{{ $user->hu_name }}</h1>

                <!-- Email + Phone -->
                <p class="transition-colors duration-300" style="color: var(--text-secondary);">{{ $user->hu_email }}</p>
                <p class="transition-colors duration-300" style="color: var(--text-secondary);">{{ $user->hu_phone ?? 'No phone provided' }}</p>

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
            <div class="flex flex-col sm:flex-row gap-2 mt-6 sm:mt-0">

                <!-- Edit -->
                <a href="{{ route('admin.community.edit', $user->hu_id) }}"
                    class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded text-sm text-center transition-colors duration-300">
                    <i class="fa-solid fa-edit mr-1"></i> Edit User
                </a>

                <!-- Blacklist / Unblacklist -->
                @if ($user->hu_is_blacklisted)
                    <form action="{{ route('admin.community.unblacklist', $user->hu_id) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm w-full transition-colors duration-300">
                            <i class="fa-solid fa-unlock mr-1"></i> Remove Blacklist
                        </button>
                    </form>
                @else
                    <button type="button" data-blacklist-open data-user-id="{{ $user->hu_id }}"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm w-full transition-colors duration-300">
                        <i class="fa-solid fa-ban mr-1"></i> Suspend User
                    </button>
                @endif

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
                                <button type="button" data-selfie-open data-selfie-url="{{ route('admin.verifications.selfie', $user->hu_id) }}"
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

                            <button type="button" data-document-open data-document-url="{{ route('admin.verifications.document', $user->hu_id) }}"
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
            <div class="absolute inset-0" data-document-close></div>

            <div class="relative max-w-5xl w-full h-[90vh] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col">
                <div class="p-4 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-file-contract text-indigo-600"></i> Proof of Identity Document
                    </h3>
                    <button type="button" data-document-close class="text-slate-400 hover:text-slate-600 transition p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="flex-grow bg-slate-200 relative">
                    <div id="docLoading" class="absolute inset-0 flex items-center justify-center bg-white z-10">
                        <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-indigo-600">
                        </div>
                    </div>
                    <iframe id="modalDocumentFrame" src="" class="w-full h-full border-none"></iframe>
                </div>
            </div>
        </div>

        <div id="selfieModal"
            class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-sm transition-all duration-300">

            <div class="absolute inset-0 cursor-zoom-out" data-selfie-close></div>

            <div class="relative max-w-4xl w-full flex flex-col items-center">
                <button type="button" data-selfie-close
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
                    <p class="text-slate-400 text-xs">Captured on: {{ $user->captured_at_display }}</p>
                </div>
            </div>
        </div>


        <!-- ACCOUNT INFO -->
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h2 class="text-xl font-semibold mb-3">Account Information</h2>
            <p class="text-gray-700"><strong>User ID:</strong> {{ $user->hu_id }}</p>
            <p class="text-gray-700"><strong>Registered On:</strong> {{ $user->registered_at_display }}</p>
            <p class="text-gray-700"><strong>Last Updated:</strong> {{ $user->updated_at_display }}</p>

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
                <button type="button" data-blacklist-close class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                    Cancel
                </button>

                <button type="button" data-blacklist-submit class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                    Confirm
                </button>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <div id="adminModuleCommunityViewConfig"
        data-csrf-token="{{ csrf_token() }}"
        data-blacklist-route-template="{{ route('admin.community.blacklist', 'ID_PLACEHOLDER') }}"
        data-user-id="{{ $user->hu_id }}"
        data-lat="{{ $user->hu_latitude }}"
        data-lng="{{ $user->hu_longitude }}"
        data-user-name="{{ $user->hu_name }}"></div>
    <script src="{{ asset('js/admin-community-view.js') }}"></script>
@endsection
