@extends('admin.layout')

@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-white">Pending Community Verifications</h1>

        <div class="bg-slate-800 shadow-xl rounded-lg p-4 sm:p-6 border border-slate-700">
            <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[780px]">
                <thead>
                    <tr class="bg-slate-900 border-b border-slate-700">
                        <th class="py-3 px-4 text-slate-200 font-semibold">User</th>
                        <th class="py-3 px-4 text-slate-200 font-semibold">Profile Photo</th>
                        <th class="py-3 px-4 text-slate-200 font-semibold">Live Selfie</th>
                        <th class="py-3 px-4 text-slate-200 font-semibold">Document</th>
                        <th class="py-3 px-4 text-slate-200 font-semibold">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-700">
                    @forelse ($pending as $user)
                        <tr class="border-b border-slate-700 hover:bg-slate-700 transition">
                            <!-- USER INFO -->
                            <td class="py-3 px-4">
                                <div>
                                    <p class="font-semibold text-white text-sm">{{ $user->hu_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $user->hu_email }}</p>
                                    <p class="text-xs text-slate-400">{{ $user->hu_phone ?? '-' }}</p>
                                </div>
                            </td>

                            <!-- PROFILE PHOTO -->
                            <td class="py-3 px-4">
                                @if($user->hu_profile_photo_path)
                                    <img src="{{ asset('storage/' . $user->hu_profile_photo_path) }}" 
                                         class="w-16 h-16 rounded-full object-cover border shadow-sm" 
                                         alt="Profile">
                                @else
                                    <span class="text-xs text-slate-300">No photo</span>
                                @endif
                            </td>

                            <!-- LIVE SELFIE -->
                            <td class="py-3 px-4">
                                @if($user->hu_selfie_media_path)
                                    <div class="flex flex-col items-start gap-1">
                                        <button onclick="openSelfieModal({{ $user->hu_id }})" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            View Selfie
                                        </button>
                                        @if($user->hu_verification_note)
                                            <span class="text-[10px] font-bold text-slate-300 bg-slate-700 px-2 py-0.5 rounded border border-slate-600">
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
                                    <button onclick="openDocumentModal({{ $user->hu_id }})" 
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

            <div class="mt-4">
                {{ $pending->links() }}
            </div>
        </div>
    </div>

<script>
function openSelfieModal(userId) {
    const modal = document.createElement('div');
    modal.id = 'selfieModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
    modal.onclick = () => modal.remove();
    modal.innerHTML = `
        <div class="relative max-w-4xl max-h-full" onclick="event.stopPropagation()">
            <button onclick="this.closest('#selfieModal').remove()" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">&times;</button>
            <img src="/admin/verifications/${userId}/selfie" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl" alt="Selfie">
        </div>
    `;
    document.body.appendChild(modal);
}

function openDocumentModal(userId) {
    const modal = document.createElement('div');
    modal.id = 'documentModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
    modal.onclick = () => modal.remove();
    modal.innerHTML = `
        <div class="relative max-w-6xl max-h-full w-full" onclick="event.stopPropagation()">
            <button onclick="this.closest('#documentModal').remove()" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">&times;</button>
            <iframe src="/admin/verifications/${userId}/document" class="w-full h-[90vh] bg-white rounded-lg shadow-2xl"></iframe>
        </div>
    `;
    document.body.appendChild(modal);
}
</script>
@endsection
