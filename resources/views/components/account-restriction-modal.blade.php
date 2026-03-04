@auth
    @php
        $user = auth()->user();
        $isRestricted = $user->isHardLocked();
        $reason = $user->hu_blacklist_reason ?: 'No specific reason provided.';

        $title = 'Account Restricted';
        $message = 'Your account cannot access the platform at this time.';
        $statusLabel = 'Restricted';
        $scope = 'Site access: Disabled';

        if ($user->hu_is_blacklisted) {
            $title = 'Account Blacklisted';
            $message = 'Your account has been permanently blacklisted. Please contact support for further assistance.';
            $statusLabel = 'Blacklisted';
            $scope = 'Site access: Disabled permanently';
        } elseif ($user->hu_is_suspended) {
            $title = 'Account Suspended';
            $message = 'Your account has been suspended temporarily. Please contact support for details.';
            $statusLabel = 'Suspended';
            $scope = 'Site access: Disabled temporarily';
        }
    @endphp

    @if($isRestricted)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-md w-full p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 to-rose-600"></div>

                <div class="mx-auto w-20 h-20 bg-red-50 border-8 border-red-200 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"></path>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mb-3">{{ $title }}</h2>
                <p class="text-slate-600 mb-6 leading-relaxed">{{ $message }}</p>

                <div class="bg-slate-50 p-4 rounded-xl text-left border border-slate-200 mb-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Restriction Type</p>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ $statusLabel }}</p>
                    <p class="text-xs text-slate-600 mt-1">{{ $scope }}</p>
                </div>

                <div class="bg-red-50 p-4 rounded-xl text-left border border-red-100 mb-6">
                    <p class="text-xs font-bold text-red-500 uppercase tracking-wide">Reason</p>
                    <p class="text-sm text-slate-800 italic mt-1">"{{ $reason }}"</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full bg-red-600 text-white font-bold py-3.5 px-6 rounded-xl hover:bg-red-700 transition-all shadow-lg shadow-red-200 active:scale-[0.99]">
                        Log Out
                    </button>
                </form>
                <div class="text-xs text-slate-500 mt-4">
                    If you believe this is a mistake, please contact our support team at 
                    <a href="mailto:support@uservemalaysia.com" class="text-indigo-600 hover:underline">support@uservemalaysia.com</a>
                </div>
            </div>
        </div>
    @endif
@endauth
