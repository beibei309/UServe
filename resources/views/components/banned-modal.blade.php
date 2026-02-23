@auth
    @php
        $user = auth()->user();
        $isBanned = false;
        $title = 'Account Suspended';
        $message = 'Your account has been suspended due to policy violations.';
        $reason = $user->blacklist_reason ?? 'No specific reason provided.';

        // Check if user is suspended OR blacklisted, regardless of role
        if ($user->is_suspended) {
            $isBanned = true;
            $title = 'Account Suspended';
            $message = 'Your account has been suspended. You cannot access the platform.';
        } elseif ($user->is_blacklisted) {
            $isBanned = true;
            $title = 'Account Blacklisted';
            $message = 'Your account has been blacklisted. Access is restricted.';
        }
    @endphp

    @if($isBanned)
        <style>
            .swal-high-zindex {
                z-index: 10000 !important;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: '{{ $title }}',
                    html: `
                        <p class="text-slate-600 mb-4">{{ $message }}</p>
                        <div class="bg-red-50 p-3 rounded-lg text-left border border-red-100 mb-4">
                            <p class="text-xs font-bold text-red-500 uppercase">Reason:</p>
                            <p class="text-sm text-slate-800 italic">"{{ $reason }}"</p>
                        </div>
                        <p class="text-xs text-slate-500">
                            If you believe this is a mistake, please contact our support team at 
                            <a href="mailto:support@U-Serve.upsi.edu.my" class="text-indigo-600 hover:underline font-bold"><br>support@U-Serve.upsi.edu.my</a>.
                        </p>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: true,
                    confirmButtonText: 'Log Out',
                    confirmButtonColor: '#1e293b', // slate-900
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit logout form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("logout") }}';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        
                        form.appendChild(csrfToken);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        </script>
    @endif
@endauth