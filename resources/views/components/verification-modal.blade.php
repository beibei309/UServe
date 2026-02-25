@auth
    @php
        $user = auth()->user();
        $isRestricted = $user->is_blocked || $user->is_suspended || $user->is_blacklisted;
        $isCommunityUnverified =
            $user->role === 'community' &&
            $user->verification_status !== 'approved' &&
            !$isRestricted &&
            $user->hasVerifiedEmail();

        $isOnCommunityOnboarding = request()->routeIs('onboarding.community.*');
        $isPending = $user->verification_status === 'pending';
        $hasFiles = !empty($user->verification_document_path) && !empty($user->selfie_media_path);
        $reviewInProgress = $isPending && $hasFiles;

        $title = $reviewInProgress ? 'Verification in Progress' : 'Verification Required';
        $message = $reviewInProgress
            ? 'Your submitted document and selfie are currently being reviewed by the admin team.'
            : 'To keep the platform safe, please complete your community verification before continuing.';
        $reason = $user->verification_note ?: ($reviewInProgress
            ? 'Status: Pending admin review.'
            : 'Proof of residency and selfie are required.');
    @endphp

    @if($isCommunityUnverified && !$isOnCommunityOnboarding)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 animate-in fade-in duration-300">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-md w-full p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-blue-600"></div>

                <div class="mx-auto w-20 h-20 bg-indigo-50 border-8 border-indigo-200 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mb-3">{{ $title }}</h2>
                <p class="text-slate-600 mb-6 leading-relaxed">{{ $message }}</p>

                <div class="bg-indigo-50 p-4 rounded-xl text-left border border-indigo-100 mb-6">
                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-wide">Reason</p>
                    <p class="text-sm text-slate-800 italic mt-1">"{{ $reason }}"</p>
                </div>

                <div class="space-y-3">
                    @if(!$reviewInProgress)
                        <a href="{{ route('onboarding.community.verify') }}"
                            class="block w-full bg-indigo-600 text-white font-bold py-3.5 px-6 rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-[0.99]">
                            Complete Verification Now
                        </a>
                    @else
                        <button disabled class="w-full bg-slate-100 text-slate-400 font-bold py-3.5 px-6 rounded-xl cursor-not-allowed">
                            Review in Progress...
                        </button>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-slate-400 hover:text-red-500 font-medium transition-colors pt-2">
                            Log Out
                        </button>
                    </form>
                </div>

                <p class="text-xs text-slate-500 mt-6">
                    If you believe this is a mistake, please contact support at
                    <a href="mailto:support@U-Serve.upsi.edu.my" class="text-indigo-600 hover:underline font-semibold">support@U-Serve.upsi.edu.my</a>.
                </p>
            </div>
        </div>
    @endif
@endauth
