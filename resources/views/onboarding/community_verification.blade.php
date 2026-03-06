<x-guest-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-5xl rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-8 md:p-10">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Community Verification</h1>
                <p class="mt-2 text-slate-500">Complete these steps to verify your identity and ensure community safety.</p>
            </div>

            @if(session('info'))
                <div class="max-w-3xl mx-auto mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-lg">
                    <p class="text-sm font-medium text-amber-800">{{ session('info') }}</p>
                </div>
            @endif

            @if($communityVerificationUi['is_pending_review'])
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8 text-center">
                    <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-yellow-800 mb-2">Verification Under Review</h2>
                    <p class="text-yellow-700 max-w-lg mx-auto">We have received your details. Our admin team is reviewing your profile photo, selfie, and documents. You will be notified via email once approved.</p>
                    <div class="mt-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-indigo-600 hover:text-indigo-800 font-medium text-sm underline">Log Out</button>
                        </form>
                    </div>
                </div>
            @elseif($communityVerificationUi['is_rejected'])
                <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8 text-center">
                    <h3 class="font-bold text-red-800 text-lg">Verification Rejected</h3>
                    <p class="text-red-600 mt-1">Please re-submit valid documents matching your profile.</p>
                </div>
            @endif

            @if($communityVerificationUi['show_steps'])
            <div class="space-y-8 max-w-3xl mx-auto">

                <div id="step1" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden {{ $communityVerificationUi['step1_card_class'] }}">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-full {{ $communityVerificationUi['step1_badge_class'] }} flex items-center justify-center font-bold">
                                {{ $communityVerificationUi['step1_badge_text'] }}
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">Verify Location</h2>
                                <p class="text-sm text-slate-500">You must be in the Muallim District area.</p>
                            </div>
                        </div>

                        @if($communityVerificationUi['location_verified'])
                            <div class="bg-green-50 border border-green-100 text-green-700 p-4 rounded-xl flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-bold">Location Verified</span>
                            </div>
                        @else
                            <div class="space-y-4 max-w-lg mx-auto">
                                <button id="detect_location_btn" class="w-full max-w-xl mx-auto flex items-center justify-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-6 rounded-xl font-bold shadow-md transition-all">
                                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>Detect My Location</span>
                                </button>
                                <div id="location_status_msg" class="text-center text-sm font-medium"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <div id="step2" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden {{ $communityVerificationUi['step2_card_class'] }}">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">2</div>
                            <h2 class="text-xl font-bold text-slate-900">Upload Profile Photo</h2>
                        </div>

                        <form id="profile_form" action="{{ route('onboarding.community.upload_photo') }}" method="POST" enctype="multipart/form-data" class="max-w-md mx-auto w-full space-y-6">
                            @csrf
                            <div class="flex justify-center">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-slate-100 shadow-lg bg-slate-50">
                                    <img id="profile-preview" src="{{ $communityVerificationUi['profile_preview_url'] }}" class="w-full h-full object-cover">
                                </div>
                            </div>

                            <div>
                                <input type="file" name="profile_photo" id="profile_photo_input" accept="image/*" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all"/>
                                <p class="mt-2 text-xs text-slate-400 text-center">Clear face photo. JPG/PNG, Max 4MB.</p>
                            </div>

                            <button type="submit" class="w-full bg-slate-900 text-white py-3 rounded-xl font-medium hover:bg-slate-800 transition-all">Save Photo</button>
                        </form>
                    </div>
                </div>

                <div id="step3" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden {{ $communityVerificationUi['step3_card_class'] }}">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">3</div>
                            <h2 class="text-xl font-bold text-slate-900">Live Selfie Check</h2>
                        </div>
                        
                        <p class="text-slate-600 mb-4 text-sm">To prove you are a real person, please follow the specific gesture instruction below.</p>

                        <div class="bg-slate-900 rounded-2xl p-4 relative overflow-hidden">
                            <div id="challenge_banner" class="hidden absolute top-4 left-0 w-full z-10 text-center px-4">
                                <div class="bg-yellow-400 text-slate-900 font-bold py-2 px-4 rounded-full inline-block shadow-lg border-2 border-yellow-200 animate-pulse">
                                    Target Gesture: <span id="challenge_text" class="uppercase">Retrieving...</span>
                                </div>
                            </div>

                            <div class="aspect-video bg-black rounded-xl overflow-hidden relative flex items-center justify-center">
                                <video id="camera_preview" autoplay playsinline class="w-full h-full object-cover transform -scale-x-100 hidden"></video>
                                <canvas id="snapshot_canvas" class="w-full h-full object-cover hidden"></canvas>
                                
                                <div id="camera_placeholder" class="text-slate-500 flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-sm">Camera inactive</span>
                                </div>
                                
                                <div id="face_guide" class="absolute inset-0 border-4 border-white/30 rounded-[50%] w-48 h-64 m-auto hidden pointer-events-none"></div>
                            </div>

                            <div class="mt-4 flex justify-center gap-3">
                                <button id="start_camera" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-full font-medium text-sm transition-all shadow-lg">Start Camera</button>
                                <button id="take_snapshot" class="hidden bg-white text-slate-900 px-6 py-2 rounded-full font-bold text-sm hover:bg-slate-100 transition-all">Capture Photo</button>
                                <button id="retake_snapshot" class="hidden bg-slate-700 text-white px-6 py-2 rounded-full font-medium text-sm hover:bg-slate-600 transition-all">Retake</button>
                                <button id="confirm_snapshot" class="hidden bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-full font-bold text-sm transition-all shadow-lg">Confirm & Upload</button>
                            </div>
                        </div>
                        <p id="selfie_status" class="text-center text-sm font-medium text-green-600 mt-2 h-5">{{ $communityVerificationUi['selfie_status_text'] }}</p>
                    </div>
                </div>

                <div id="step4" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden {{ $communityVerificationUi['step4_card_class'] }}">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">4</div>
                            <h2 class="text-xl font-bold text-slate-900">Upload Proof Document</h2>
                        </div>

                       <form id="verificationForm" method="POST" action="{{ route('onboarding.community.submit_doc') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <h3 class="font-bold text-blue-900 text-xs uppercase tracking-wider mb-2">Accepted Documents (Private & Secure)</h3>
                                <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                                    <li>Recent Utility Bill (Water/Electric)</li>
                                    <li>Work Staff ID (if working in Muallim District)</li>
                                    <li>Business Registration (SSM)</li>
                                </ul>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Select Document (Image or PDF)</label>
                                <input type="file" name="verification_document" accept=".jpg,.jpeg,.png,.pdf" required
                                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all border border-slate-200 rounded-lg"/>
                            </div>

                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl font-medium transition-all shadow-lg">
                                Submit Final Verification
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            @endif
            
            <div class="text-center mt-8 pb-8">
                 <a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-slate-600">Back to Dashboard</a>
            </div>

        </div>
    </div>

    @push('scripts')
    <div id="communityVerificationConfig"
        data-save-location-url="{{ route('verification.save_location') }}"
        data-upload-selfie-url="{{ route('onboarding.community.upload_selfie') }}"
        data-csrf-token="{{ csrf_token() }}"
        data-upsi-lat="3.7832"
        data-upsi-lng="101.5927"
        data-radius-km="25"></div>
    <script src="{{ asset('js/community-verification.js') }}"></script>
    @endpush
</x-guest-layout>
