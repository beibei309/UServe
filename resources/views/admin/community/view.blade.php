@extends('admin.layout')

@section('content')
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                    Community Member Profile
                </h1>
                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">
                    View and manage student community member details
                </p>
            </div>
            <a href="{{ route('admin.community.index') }}"
               class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-400 hover:to-gray-500 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-arrow-left"></i>
                Back to Community
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

        {{-- PROFILE HEADER CARD --}}
        <div class="rounded-xl shadow-xl border transition-all duration-300 mb-8" 
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    
                    {{-- Profile Photo --}}
                    <div class="flex-shrink-0 text-center lg:text-left">
                        <div class="w-32 h-32 lg:w-40 lg:h-40 rounded-full overflow-hidden border-4 shadow-lg mx-auto lg:mx-0 transition-transform hover:scale-105" 
                             style="border-color: var(--border-color);">
                            <img src="{{ $user->profile_image_url }}" class="w-full h-full object-cover" alt="{{ $user->hu_name }}" />
                        </div>
                    </div>

                    {{-- Profile Details --}}
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
                            <div>
                                <h2 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                                    {{ $user->hu_name }}
                                </h2>
                                <p class="text-lg mt-1 transition-colors duration-300" style="color: var(--text-muted);">
                                    {{ $user->hu_email }}
                                </p>
                                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">
                                    {{ $user->hu_phone ?? 'No phone provided' }}
                                </p>
                            </div>

                            {{-- Status Badges --}}
                            <div class="flex flex-col sm:flex-row gap-2">
                                @if ($user->hu_verification_status == 'approved')
                                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Verified
                                    </span>
                                @elseif($user->hu_verification_status == 'pending')
                                    <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-medium flex items-center gap-2">
                                        <i class="fas fa-clock"></i>
                                        Pending
                                    </span>
                                @else
                                    <span class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium flex items-center gap-2">
                                        <i class="fas fa-times-circle"></i>
                                        Rejected
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Suspension Status --}}
                        @if ($user->hu_is_blacklisted)
                            <div class="mb-6 p-4 rounded-lg border border-red-200" style="background-color: #fee2e2;">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-ban text-red-600 mt-1"></i>
                                    <div>
                                        <h3 class="font-bold text-red-700">Account Suspended</h3>
                                        <p class="text-sm text-red-600 mt-1">
                                            <strong>Reason:</strong> {{ $user->hu_blacklist_reason }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('admin.community.edit', $user->hu_id) }}"
                               class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
                                <i class="fas fa-edit"></i>
                                Edit User
                            </a>

                            @if ($user->hu_is_blacklisted)
                                <form action="{{ route('admin.community.unblacklist', $user->hu_id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
                                        <i class="fas fa-unlock"></i>
                                        Remove Suspension
                                    </button>
                                </form>
                            @else
                                <button type="button" data-blacklist-open data-user-id="{{ $user->hu_id }}"
                                        class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 font-medium">
                                    <i class="fas fa-ban"></i>
                                    Suspend User
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- VERIFICATION DOCUMENTS SECTION --}}
        <div class="rounded-xl shadow-xl border transition-all duration-300 mb-8" 
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4 rounded-t-xl">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-xl">Verification Assets</h2>
                        <p class="text-indigo-100 text-sm">Identity verification documents and photos</p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    {{-- Live Selfie Section --}}
                    <div class="flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-camera text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">Live Selfie Check</h3>
                        </div>

                        @if ($user->hu_selfie_media_path)
                            <div class="relative group rounded-xl overflow-hidden border shadow-lg transition-all duration-300 hover:shadow-xl" 
                                 style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                <img src="{{ route('admin.verifications.selfie', $user->hu_id) }}"
                                     class="w-full h-72 object-cover transition-transform duration-300 group-hover:scale-105"
                                     alt="Live Selfie">

                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                    <button type="button" data-selfie-open data-selfie-url="{{ route('admin.verifications.selfie', $user->hu_id) }}"
                                            class="bg-white text-slate-900 px-6 py-3 rounded-lg font-semibold text-sm shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <i class="fas fa-expand-arrows-alt mr-2"></i> View Full Size
                                    </button>
                                </div>

                                @if ($user->hu_verification_note)
                                    <div class="absolute bottom-3 left-3 right-3 bg-amber-50 bg-opacity-95 backdrop-blur border border-amber-200 p-3 rounded-lg">
                                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Verification Challenge</p>
                                        <p class="text-sm text-amber-900 font-medium">{{ $user->hu_verification_note }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-72 border-2 border-dashed rounded-xl transition-all duration-300"
                                 style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                <i class="fas fa-user-slash text-4xl mb-3 transition-colors duration-300" style="color: var(--text-muted);"></i>
                                <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-muted);">No selfie uploaded</p>
                            </div>
                        @endif
                    </div>

                    {{-- Official Document Section --}}
                    <div class="flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-invoice text-green-600"></i>
                            </div>
                            <h3 class="font-semibold transition-colors duration-300" style="color: var(--text-primary);">Official Proof Document</h3>
                        </div>

                        @if ($user->hu_verification_document_path)
                            <div class="border rounded-xl p-8 flex flex-col items-center justify-center h-72 transition-all duration-300 hover:shadow-lg" 
                                 style="background-color: var(--bg-primary); border-color: var(--border-color);">
                                <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                                    <i class="fas fa-file-alt text-blue-600 text-3xl"></i>
                                </div>
                                <h4 class="font-bold mb-1 transition-colors duration-300" style="color: var(--text-primary);">Verification Document</h4>
                                <p class="text-xs mb-6 text-center transition-colors duration-300" style="color: var(--text-muted);">
                                    Stored securely in protected local storage
                                </p>
                                <button type="button" data-document-open data-document-url="{{ route('admin.verifications.document', $user->hu_id) }}"
                                        class="bg-gradient-to-r from-slate-700 to-slate-900 hover:from-slate-600 hover:to-slate-800 text-white px-6 py-3 rounded-lg transition-all duration-300 flex items-center gap-2 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-eye"></i> Preview Document
                                </button>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-72 border-2 border-dashed rounded-xl transition-all duration-300"
                                 style="background-color: #fef2f2; border-color: #fca5a5;">
                                <i class="fas fa-file-excel text-red-400 text-4xl mb-3"></i>
                                <p class="text-sm font-medium text-red-500">No document uploaded</p>
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
            class="modal-overlay hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/90 backdrop-blur-md p-4">
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
            class="modal-overlay hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-sm transition-all duration-300">

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


        {{-- ACCOUNT INFORMATION SECTION --}}
        <div class="rounded-xl shadow-xl border transition-all duration-300" 
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4 rounded-t-xl">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-xl">Account Information</h2>
                        <p class="text-emerald-100 text-sm">Registration and activity details</p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="p-6 rounded-xl border transition-all duration-300 hover:shadow-lg" 
                         style="background-color: var(--bg-primary); border-color: var(--border-color);">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-hashtag text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">User ID</p>
                                <p class="text-lg font-bold font-mono transition-colors duration-300" style="color: var(--text-primary);">{{ $user->hu_id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 rounded-xl border transition-all duration-300 hover:shadow-lg" 
                         style="background-color: var(--bg-primary); border-color: var(--border-color);">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-plus text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Registered On</p>
                                <p class="text-lg font-bold transition-colors duration-300" style="color: var(--text-primary);">{{ $user->registered_at_display }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 rounded-xl border transition-all duration-300 hover:shadow-lg" 
                         style="background-color: var(--bg-primary); border-color: var(--border-color);">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-orange-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Last Updated</p>
                                <p class="text-lg font-bold transition-colors duration-300" style="color: var(--text-primary);">{{ $user->updated_at_display }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- BLACKLIST MODAL -->
    <div id="blacklistModal" class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

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
