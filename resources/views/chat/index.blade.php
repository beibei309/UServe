<x-app-layout>
    @section('content')
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            
            <div class="flex items-center justify-between mb-8 mt-20">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Messages</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage your chats and incoming requests.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                
                @if((isset($pendingRequests) && $pendingRequests->count() > 0) || (isset($sentRequests) && $sentRequests->count() > 0))
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex space-x-1 bg-gray-200/60 p-1 rounded-xl">
                            @if(isset($pendingRequests) && $pendingRequests->count() > 0)
                                <button onclick="showTab('pending')" id="pending-tab" 
                                    class="tab-button flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all shadow-sm bg-white text-gray-900">
                                    Pending
                                    <span class="ml-2 bg-indigo-100 text-indigo-700 py-0.5 px-2 rounded-full text-xs font-bold">
                                        {{ $pendingRequests->count() }}
                                    </span>
                                </button>
                            @endif
                            @if(isset($sentRequests) && $sentRequests->count() > 0)
                                <button onclick="showTab('sent')" id="sent-tab" 
                                    class="tab-button flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-500 hover:text-gray-700 transition-all">
                                    Sent
                                    <span class="ml-2 bg-gray-200 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                                        {{ $sentRequests->count() }}
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if(isset($pendingRequests) && $pendingRequests->count() > 0)
                        <div id="pending-content" class="tab-content bg-indigo-50/30">
                            <div class="p-6 space-y-4">
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Incoming Requests</h3>
                                @foreach($pendingRequests as $request)
                                    <div class="bg-white border border-indigo-100 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                            <div class="flex items-center gap-4">
                                                <div class="relative">
                                                    <img src="{{ $request->requester->profile_picture ? asset('storage/' . $request->requester->profile_picture) : asset('images/default-avatar.png') }}" 
                                                        alt="{{ $request->requester->name }}" 
                                                        class="w-14 h-14 rounded-full object-cover ring-4 ring-indigo-50">
                                                    <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full"></div>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-gray-900">{{ $request->requester->name }}</h4>
                                                    <p class="text-xs text-indigo-600 font-medium mb-1">Community Member</p>
                                                    <p class="text-xs text-gray-400">Received {{ $request->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>

                                            @if($request->message)
                                                <div class="flex-1 bg-gray-50 p-3 rounded-lg text-sm text-gray-600 border border-gray-100 italic mx-0 sm:mx-4 w-full sm:w-auto">
                                                    "{{ Str::limit($request->message, 80) }}"
                                                </div>
                                            @endif

                                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                                <button onclick="acceptChatRequest({{ $request->id }})" 
                                                    class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm hover:shadow">
                                                    Accept
                                                </button>
                                                <button onclick="declineChatRequest({{ $request->id }})" 
                                                    class="flex-1 sm:flex-none px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors text-sm font-medium">
                                                    Decline
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(isset($sentRequests) && $sentRequests->count() > 0)
                        <div id="sent-content" class="tab-content" style="display: none;">
                            <div class="p-6 space-y-4">
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Outgoing Requests</h3>
                                @foreach($sentRequests as $request)
                                    <div class="bg-white border border-gray-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div class="flex items-center gap-4 w-full">

                                           <img src="{{ $request->recipient->profile_picture ? asset('storage/' . $request->recipient->profile_picture) : asset('images/default-avatar.png') }}" 
                             alt="{{ $request->recipient->name }}" 
                             class="w-12 h-12 rounded-full object-cover grayscale opacity-80">
                                            <div>
                                                <h4 class="font-semibold text-gray-800">{{ $request->recipient->name }}</h4>
                                                <p class="text-sm text-gray-500">Service Provider</p>
                                                <p class="text-xs text-gray-400 mt-0.5">Sent {{ $request->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="w-full sm:w-auto flex justify-end">
                                            <span class="inline-flex items-center px-3 py-1 bg-yellow-50 text-yellow-700 rounded-full text-xs font-medium border border-yellow-100">
                                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></span>
                                                Pending Response
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <div class="px-6 py-4 border-b border-gray-100 bg-white">
                    <h2 class="text-lg font-bold text-gray-800">Your Conversations</h2>
                </div>

                <div class="divide-y divide-gray-100">
                    @if(isset($conversations) && $conversations->count() > 0)
                        @foreach($conversations as $conversation)
                            <div class="group p-4 sm:p-6 hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <img src="{{ $conversation->otherUser->profile_picture ? asset('storage/' . $conversation->otherUser->profile_picture) : asset('images/default-avatar.png') }}" 
                                                alt="{{ $conversation->otherUser->name }}" 
                                                class="w-14 h-14 rounded-full object-cover shadow-sm group-hover:scale-105 transition-transform duration-200">
                                        </div>
                                        
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-bold text-gray-900 text-base">{{ $conversation->otherUser->name }}</h3>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide
                                                    {{ $conversation->otherUser->role === 'student' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                    {{ $conversation->otherUser->role === 'student' ? 'Service Provider' : 'Community' }}
                                                </span>
                                            </div>
                                            
                                            @if($conversation->lastMessage)
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-1">
                                                    <span class="text-gray-400 mr-1">{{ $conversation->lastMessage->sender_id === auth()->id() ? 'You:' : '' }}</span>
                                                    {{ Str::limit($conversation->lastMessage->content, 60) }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1 group-hover:text-indigo-500 transition-colors">
                                                    {{ $conversation->lastMessage->created_at->diffForHumans() }}
                                                </p>
                                            @else
                                                <p class="text-sm text-gray-400 italic mt-1">Start a conversation...</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="pl-4">
                                        <a href="{{ route('chat.show', $conversation) }}" 
                                           class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-16 px-6">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">No conversations yet</h3>
                            <p class="text-gray-500 max-w-sm mx-auto mb-8">Connect with service providers or community members to start chatting.</p>
                            <a href="{{ route('services.index') }}" 
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Browse Services
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.style.display = 'none');

            const tabButtons = document.querySelectorAll('.tab-button');
            // Reset to inactive state (gray text, transparent bg)
            tabButtons.forEach(button => {
                button.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                button.classList.add('text-gray-500');
            });

            // Activate content
            const selectedContent = document.getElementById(tabName + '-content');
            if(selectedContent) selectedContent.style.display = 'block';

            // Set active state (white bg, dark text, shadow)
            const activeTab = document.getElementById(tabName + '-tab');
            if(activeTab) {
                activeTab.classList.remove('text-gray-500');
                activeTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Your existing Logic for AJAX (Accept/Decline) remains unchanged
            window.acceptChatRequest = function(requestId) {
                Swal.fire({
                    title: 'Accept Request?',
                    text: "Start a conversation with this user.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Accept',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#4f46e5', // Indigo-600
                    cancelButtonColor: '#9ca3af',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/chat-requests/${requestId}/accept`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.conversation) {
                                Swal.fire({
                                    title: 'Connected!', 
                                    text: 'Chat request accepted.', 
                                    icon: 'success',
                                    confirmButtonColor: '#4f46e5'
                                }).then(() => {
                                    window.location.reload(); 
                                });
                            } else {
                                Swal.fire('Error', 'Could not accept request.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'An error occurred.', 'error');
                        });
                    }
                });
            }

            window.declineChatRequest = function(requestId) {
                Swal.fire({
                    title: 'Decline Request?',
                    text: "This cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Decline',
                    cancelButtonText: 'Keep',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#9ca3af',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/chat-requests/${requestId}/decline`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.chat_request) {
                                Swal.fire({
                                    title: 'Declined', 
                                    text: 'Request removed.', 
                                    icon: 'success',
                                    confirmButtonColor: '#4f46e5'
                                }).then(() => {
                                    window.location.reload(); 
                                });
                            } else {
                                Swal.fire('Error', 'Could not decline request.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'An error occurred.', 'error');
                        });
                    }
                });
            }
        });
    </script>
</x-app-layout>