@auth
    <style>
        .swal-high-zindex {
            z-index: 10000 !important;
        }
    </style>
    
    @if(auth()->user()->verification_status !== 'approved')
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 animate-in fade-in duration-300">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center relative overflow-hidden transform transition-all scale-100">
                
                <!-- Decorative Background Blob -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-indigo-600"></div>

                <!-- Icon -->
                <div class="mx-auto w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-6 ring-8 ring-amber-50/50">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                @php
                    $isCommunity = auth()->user()->role === 'community';
                    $isPending = auth()->user()->verification_status === 'pending';
                    $hasFiles = auth()->user()->verification_document_path && auth()->user()->selfie_media_path;
                    $reviewInProgress = $isCommunity && $isPending && $hasFiles;
                @endphp

                <h2 class="text-2xl font-bold text-slate-900 mb-3 tracking-tight">
                    @if($reviewInProgress)
                        Verification in Progress
                    @else
                        Verification Required
                    @endif
                </h2>

                <p class="text-slate-600 mb-8 leading-relaxed">
                    @if($isCommunity)
                        @if($reviewInProgress)
                            Thank you for submitting your details! Our team is currently reviewing your documents. You will be notified once your account is approved.
                        @else
                            To ensure the safety of our community, please complete the one-time verification process by uploading your proof of residency.
                        @endif
                    @else
                        Your account is not fully active. Please check your inbox at <span class="font-semibold text-slate-800">{{ auth()->user()->email }}</span> and click the verification link.
                    @endif
                </p>

                <div class="space-y-4">
                    @if($isCommunity)
                        @if(!$reviewInProgress)
                            <a href="{{ route('onboarding.community.verify') }}" 
                               class="block w-full bg-indigo-600 text-white font-bold py-3.5 px-6 rounded-xl hover:bg-indigo-700 transition-all transform hover:scale-[1.02] shadow-lg shadow-indigo-200">
                                Verify Account Now
                            </a>
                        @else
                            <button disabled class="block w-full bg-slate-100 text-slate-400 font-bold py-3.5 px-6 rounded-xl cursor-not-allowed">
                                Review in Progress...
                            </button>
                        @endif
                    @else
                        <button onclick="window.location.reload()" 
                                class="block w-full bg-white border-2 border-slate-200 text-slate-700 font-bold py-3.5 px-6 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all mb-3">
                            I've Verified, Refresh Page
                        </button>

                        <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium underline transition-colors">
                                Resend Verification Email
                            </button>
                        </form>

                        <script>
                            document.getElementById('resendForm').addEventListener('submit', function(e) {
                                e.preventDefault();
                                
                                fetch('{{ route('verification.send') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Email Sent!',
                                        text: 'A new verification link has been sent to your inbox. Please check your email.',
                                        confirmButtonColor: '#4f46e5',
                                        timer: 4000,
                                        timerProgressBar: true,
                                        customClass: {
                                            container: 'swal-high-zindex'
                                        }
                                    });
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops!',
                                        text: 'Something went wrong. Please try again.',
                                        confirmButtonColor: '#4f46e5',
                                        customClass: {
                                            container: 'swal-high-zindex'
                                        }
                                    });
                                });
                            });
                        </script>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-slate-400 hover:text-red-500 font-medium transition-colors pt-2">
                            Log Out
                        </button>
                    </form>
                </div>

            </div>
        </div>
    @endif

    @if (session('status') == 'verification-link-sent')
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Email Sent!',
                text: 'A new verification link has been sent to your inbox. Please check your email.',
                confirmButtonColor: '#4f46e5',
                timer: 4000,
                timerProgressBar: true,
            });
        </script>
    @endif
@endauth
