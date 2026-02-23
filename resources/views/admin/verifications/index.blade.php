@extends('admin.layout')

@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Pending Community Verifications</h1>

        <div class="bg-white shadow rounded-lg p-6">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Profile Photo</th>
                        <th class="py-3 px-4">Live Selfie</th>
                        <th class="py-3 px-4">Document</th>
                        <th class="py-3 px-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pending as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <!-- USER INFO -->
                            <td class="py-3 px-4">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->phone ?? '-' }}</p>
                                </div>
                            </td>

                            <!-- PROFILE PHOTO -->
                            <td class="py-3 px-4">
                                @if($user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                                         class="w-16 h-16 rounded-full object-cover border shadow-sm" 
                                         alt="Profile">
                                @else
                                    <span class="text-xs text-gray-400">No photo</span>
                                @endif
                            </td>

                            <!-- LIVE SELFIE -->
                            <td class="py-3 px-4">
                                @if($user->selfie_media_path)
                                    <div class="flex flex-col items-start gap-1">
                                        <button onclick="openSelfieModal({{ $user->id }})" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            View Selfie
                                        </button>
                                        @if($user->verification_note)
                                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                {{ $user->verification_note }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-red-600 font-medium">Missing</span>
                                @endif
                            </td>

                            <!-- DOCUMENT -->
                            <td class="py-3 px-4">
                                @if($user->verification_document_path)
                                    <button onclick="openDocumentModal({{ $user->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full text-blue-700 bg-blue-100 hover:bg-blue-200">
                                        View Document
                                    </button>
                                @else
                                    <span class="text-xs text-red-600 font-medium">Missing</span>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.verifications.approve', $user->id) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.verifications.reject', $user->id) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                            Reject
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