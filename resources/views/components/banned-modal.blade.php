@auth
    @if($bannedModalData['isBanned'])
        <style>
            .swal-high-zindex {
                z-index: 10000 !important;
            }
        </style>
        <div id="bannedModalConfig"
            data-title="@json($bannedModalData['title'])"
            data-message="@json($bannedModalData['message'])"
            data-reason="@json($bannedModalData['reason'])"
            data-logout-url="{{ route('logout') }}"
            data-csrf-token="{{ csrf_token() }}"></div>
        @once
            @push('scripts')
                <script src="{{ asset('js/banned-modal.js') }}"></script>
            @endpush
        @endonce
    @endif
@endauth
