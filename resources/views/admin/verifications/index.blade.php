@extends('admin.layout')

@section('content')
    <div class="admin-list-page">
        <div class="admin-list-header">
            <div>
                <h1 class="admin-list-title">Pending Community Verifications</h1>
                <p class="admin-list-subtitle">Review profile photos, selfies, and uploaded identity documents.</p>
            </div>
            <div class="admin-list-actions">
                <a href="{{ route('admin.verifications.export') }}" class="admin-export-action">
                    <i class="fa-solid fa-download text-xs"></i> Export CSV
                </a>
            </div>
        </div>

        <div class="admin-table-card">
            <div class="admin-table-scroll">
            <table class="admin-table" style="min-width: 780px;">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Profile Photo</th>
                        <th>Live Selfie</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pending as $user)
                        <tr>
                            <!-- USER INFO -->
                            <td class="py-3 px-4">
                                <div>
                                    <p class="font-semibold text-sm" style="color: var(--text-primary);">{{ $user->hu_name }}</p>
                                    <p class="text-xs" style="color: var(--text-secondary);">{{ $user->hu_email }}</p>
                                    <p class="text-xs" style="color: var(--text-secondary);">{{ $user->hu_phone ?? '-' }}</p>
                                </div>
                            </td>

                            <!-- PROFILE PHOTO -->
                            <td class="py-3 px-4">
                                @if($user->hu_profile_photo_path)
                                    <img src="{{ asset('storage/' . $user->hu_profile_photo_path) }}" 
                                         class="w-16 h-16 rounded-full object-cover border shadow-sm" 
                                         alt="Profile">
                                @else
                                    <span class="text-xs" style="color: var(--text-muted);">No photo</span>
                                @endif
                            </td>

                            <!-- LIVE SELFIE -->
                            <td class="py-3 px-4">
                                @if($user->hu_selfie_media_path)
                                    <div class="flex flex-col items-start gap-1">
                                        <button type="button" data-verification-selfie data-user-id="{{ $user->hu_id }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            View Selfie
                                        </button>
                                        @if($user->hu_verification_note)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border"
                                                style="background-color: var(--bg-tertiary); color: var(--text-secondary); border-color: var(--border-color);">
                                                {{ $user->hu_verification_note }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-red-600 font-medium">Missing</span>
                                @endif
                            </td>

                            <!-- DOCUMENT -->
                            <td class="py-3 px-4">
                                @if($user->hu_verification_document_path)
                                    <button type="button" data-verification-document data-user-id="{{ $user->hu_id }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full text-blue-700 bg-blue-100 hover:bg-blue-200">
                                        View Document
                                    </button>
                                @else
                                    <span class="text-xs text-red-600 font-medium">Missing</span>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="py-3 px-4">
                                <div class="flex justify-end gap-3">
                                    <form action="{{ route('admin.verifications.approve', $user->hu_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-400 hover:text-green-300 transition" title="Approve">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.verifications.reject', $user->hu_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Reject">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                No pending verifications at this time.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            <div class="admin-pagination">
                {{ $pending->links() }}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <div id="adminModuleVerificationsIndexConfig"
        data-selfie-url-template="{{ route('admin.verifications.selfie', 'USER_ID_PLACEHOLDER') }}"
        data-document-url-template="{{ route('admin.verifications.document', 'USER_ID_PLACEHOLDER') }}"></div>
    <script src="{{ asset('js/admin-verifications-index.js') }}"></script>
@endsection
