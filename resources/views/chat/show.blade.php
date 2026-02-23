<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <br>
        <div class="mb-8 text-left mt-10">

            </div>
            
        <div class="bg-white rounded-lg shadow-md">
            <!-- Chat Header -->
            <div class="bg-blue-600 text-white p-4 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('chat.index') }}" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        @php
                            $otherUser = $conversation->student_id === auth()->id() ? $conversation->customer : $conversation->student;
                        @endphp
                        <img src="{{ $otherUser->profile_picture ? asset('storage/' . $otherUser->profile_picture) : asset('images/default-avatar.png') }}" 
                             alt="{{ $otherUser->name }}" 
                             class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <h2 class="font-semibold">{{ $otherUser->name }}</h2>
                            <p class="text-sm opacity-90">{{ $otherUser->role === 'student' ? 'Service Provider' : 'Community Member' }}</p>
                        </div>
                    </div>
                    
                    <!-- Service Application Button (for community users chatting with students) -->
                    @if(auth()->user()->role === 'community' && $otherUser->role === 'student')
                        <button onclick="openServiceApplicationModal()" 
                                class="px-4 py-2 bg-green-500 bg-opacity-20 rounded-lg hover:bg-opacity-30 transition-colors">
                            Apply for Service
                        </button>
                    @endif
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="chatMessages" class="h-96 overflow-y-auto p-6 space-y-4 bg-gray-50">
                @forelse($conversation->messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} mb-4">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="flex items-end space-x-2 {{ $message->sender_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                <img src="{{ $message->sender->profile_photo_path ? asset('storage/' . $message->sender->profile_photo_path) : asset('images/default-avatar.png') }}" 
                                     alt="{{ $message->sender->name }}" 
                                     class="w-8 h-8 rounded-full object-cover">
                                <div class="px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200' }}">
                                    <p class="text-sm">{{ $message->body }}</p>
                                    <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }}">
                                        {{ $message->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">No messages yet. Start the conversation!</p>
                    </div>
                @endforelse
            </div>

            <!-- Service Applications Status -->
            @if($serviceApplications->count() > 0)
                <div class="border-t border-gray-200 p-4 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Applications</h3>
                    <div class="space-y-3">
                        @foreach($serviceApplications as $application)
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $application->service->title ?? $application->title }}</h4>
                                        <p class="text-sm text-gray-600">Applied by {{ $application->user->name }}</p>
                                        <div class="flex items-center space-x-2 mt-2">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($application->status === 'accepted') bg-green-100 text-green-800
                                                @elseif($application->status === 'declined') bg-red-100 text-red-800
                                                @elseif($application->status === 'completed') bg-blue-100 text-blue-800
                                                @endif">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                            
                                            @if($application->status === 'accepted')
                                                @if($application->customer_completed && $application->provider_completed)
                                                    <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                                        Fully Completed
                                                    </span>
                                                @elseif($application->customer_completed)
                                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                        Customer Completed
                                                    </span>
                                                @elseif($application->provider_completed)
                                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                        Provider Completed
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        @if($application->status === 'pending' && auth()->user()->id === $application->service->user_id)
                                            <!-- Provider can accept/decline -->
                                            <button onclick="handleApplication({{ $application->id }}, 'accept')" 
                                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                                Accept
                                            </button>
                                            <button onclick="handleApplication({{ $application->id }}, 'decline')" 
                                                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                                Decline
                                            </button>
                                        @elseif($application->status === 'accepted' && $application->canBeMarkedCompletedBy(auth()->user()))
                                            <!-- Both parties can mark as completed -->
                                            <button onclick="markCompleted({{ $application->id }})" 
                                                    class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                                                Mark Completed
                                            </button>
                                        @elseif($application->status === 'completed' && $application->isFullyCompleted())
                                            <!-- Show review button -->
                                            <button onclick="openReviewModal({{ $application->id }})" 
                                                    class="px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                                                Leave Review
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Typing Indicator -->
            <div id="typingIndicator" class="px-4 py-2 text-sm text-gray-500 italic hidden">
                <span id="typingText"></span>
            </div>

            <!-- Message Input -->
            <div class="border-t border-gray-200 p-4">
                <form id="messageForm" class="flex space-x-3">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    <input type="text" 
                           name="message" 
                           id="messageInput"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Type your message..." 
                           required>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Leave a Review</h3>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="reviewForm" class="space-y-4">
                @csrf
                <input type="hidden" id="reviewServiceApplicationId" name="service_application_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rating
                    </label>
                    <div class="flex space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating({{ $i }})" 
                                    class="star-button text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none">
                                ★
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" id="rating" name="rating" required>
                </div>
                
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                        Comment (Optional)
                    </label>
                    <textarea id="comment" name="comment" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Share your experience..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeReviewModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Service Application Modal -->
@if(auth()->user()->role === 'community')
<div id="serviceApplicationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Apply for Service</h3>
                <button onclick="closeServiceApplicationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="serviceApplicationForm" class="space-y-4">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                    <select name="service_id" id="service_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Choose a service...</option>
                        @foreach($providerServices as $service)
                            <option value="{{ $service->id }}">{{ $service->title }} - {{ $service->price_range ?? 'Price negotiable' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="application_message" class="block text-sm font-medium text-gray-700 mb-2">Additional Message (Optional)</label>
                    <textarea name="message" id="application_message" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Any specific requirements or questions..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeServiceModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Apply for Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// Real-time message listening with Echo
const conversationId = {{ $conversation->id }};
const currentUserId = {{ auth()->id() }};

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Variables for typing functionality
    let typingTimer;
    let isTyping = false;

    // Check if Echo is available and establish connection
    if (typeof window.Echo === 'undefined') {
        console.error('❌ Echo is not loaded! WebSocket connection will not work.');
        console.log('Available window properties:', Object.keys(window).filter(key => key.includes('Echo') || key.includes('Pusher')));
    } else {
        console.log('✅ Echo is loaded, attempting to connect to conversation channel...');
        console.log('Echo configuration:', {
            broadcaster: window.Echo.options.broadcaster,
            key: window.Echo.options.key,
            wsHost: window.Echo.options.wsHost,
            wsPort: window.Echo.options.wsPort
        });
        
        // Test Echo connection with more detailed logging
        window.Echo.connector.pusher.connection.bind('connected', function() {
            console.log('✅ WebSocket connected successfully!');
            console.log('Connection state:', window.Echo.connector.pusher.connection.state);
            console.log('Socket ID:', window.Echo.connector.pusher.connection.socket_id);
        });
        
        window.Echo.connector.pusher.connection.bind('disconnected', function() {
            console.log('❌ WebSocket disconnected');
            console.log('Connection state:', window.Echo.connector.pusher.connection.state);
        });
        
        window.Echo.connector.pusher.connection.bind('error', function(error) {
            console.error('❌ WebSocket connection error:', error);
        });
        
        // Listen for new messages on the conversation channel with detailed logging
        const channel = window.Echo.private(`conversation.${conversationId}`);
        console.log('📡 Subscribing to channel:', `conversation.${conversationId}`);
        
        channel.listen('.message.sent', (e) => {
            console.log('📨 New message received via Echo:', e);
            console.log('Current user ID:', currentUserId);
            console.log('Message sender ID:', e.sender_id);
            console.log('Should add message:', e.sender_id !== currentUserId);
        
            if (e.sender_id !== currentUserId) {
                console.log('✅ Adding message to chat');
                addMessageToChat(e);
                scrollToBottom();
            } else {
                console.log('❌ Not adding message - sent by current user');
            }
        })
        .listen('MessageSent', (e) => {
            console.log('📨 New message received via MessageSent event:', e);
            console.log('Current user ID:', currentUserId);
            console.log('Message data:', e.message || e);
        
            const messageData = e.message || e;
            console.log('Message sender ID:', messageData.sender_id);
            console.log('Should add message:', messageData.sender_id !== currentUserId);
        
            if (messageData.sender_id !== currentUserId) {
                console.log('✅ Adding message to chat from MessageSent');
                addMessageToChat(messageData);
                scrollToBottom();
            } else {
                console.log('❌ Not adding message - sent by current user');
            }
        })
        .error((error) => {
            console.error('❌ Echo channel error:', error);
        });
        
        // Test if channel is subscribed
        setTimeout(() => {
            console.log('Channel subscription status:', channel);
            console.log('All active channels:', Object.keys(window.Echo.connector.channels));
        }, 2000);
        
        // Add a test button to manually trigger Echo connection test
        const testButton = document.createElement('button');
        testButton.textContent = 'Test WebSocket Connection';
        testButton.className = 'px-4 py-2 bg-red-500 text-white rounded mb-4';
        testButton.onclick = function() {
            console.log('🔍 Testing WebSocket connection...');
            
            // Check if Echo is available
            if (!window.Echo) {
                console.error('❌ Echo is not available on window object');
                updateStatus('❌ Echo not loaded', 'bg-red-100 text-red-800');
                return;
            }
            
            console.log('✅ Echo object found:', window.Echo);
            
            // Check connector
            if (!window.Echo.connector) {
                console.error('❌ Echo connector not available');
                updateStatus('❌ Echo connector missing', 'bg-red-100 text-red-800');
                return;
            }
            
            console.log('✅ Echo connector found:', window.Echo.connector);
            
            // Debug: Log the full connector structure
            console.log('🔍 Connector keys:', Object.keys(window.Echo.connector));
            console.log('🔍 Connector pusher:', window.Echo.connector.pusher);
            
            // Check socket through Pusher connection
            const pusher = window.Echo.connector.pusher;
            if (!pusher) {
                console.error('❌ Pusher not available on connector');
                updateStatus('❌ Pusher missing', 'bg-red-100 text-red-800');
                return;
            }
            
            console.log('✅ Pusher found:', pusher);
            console.log('🔍 Pusher keys:', Object.keys(pusher));
            console.log('🔍 Pusher connection:', pusher.connection);
            
            if (!pusher.connection) {
                console.error('❌ Pusher connection not available');
                updateStatus('❌ Pusher connection missing', 'bg-red-100 text-red-800');
                return;
            }
            
            console.log('✅ Pusher connection found:', pusher.connection);
            console.log('🔍 Connection keys:', Object.keys(pusher.connection));
            console.log('🔍 Connection.connection:', pusher.connection.connection);
            
            // Access the actual WebSocket through the ConnectionManager
            const connectionManager = pusher.connection;
            const actualConnection = connectionManager.connection;
            
            if (!actualConnection) {
                console.error('❌ Actual connection not available');
                updateStatus('❌ Connection not established', 'bg-red-100 text-red-800');
                return;
            }
            
            console.log('✅ Actual connection found:', actualConnection);
            console.log('🔍 Actual connection keys:', Object.keys(actualConnection));
            console.log('🔍 Actual connection transport:', actualConnection.transport);
            
            // Access the WebSocket through the transport layer
            const transport = actualConnection.transport;
            if (!transport) {
                console.error('❌ Transport not available');
                updateStatus('❌ Transport missing', 'bg-red-100 text-red-800');
                return;
            }
            
            console.log('✅ Transport found:', transport);
            console.log('🔍 Transport keys:', Object.keys(transport));
            console.log('🔍 Transport socket:', transport.socket);
            console.log('🔍 Transport ws:', transport.ws);
            console.log('🔍 Transport websocket:', transport.websocket);
            
            // Try multiple possible socket property names
            let socket = transport.socket || transport.ws || transport.websocket;
            
            // If still not found, check if it's nested deeper
            if (!socket && transport.connection) {
                console.log('🔍 Checking transport.connection:', transport.connection);
                socket = transport.connection.socket || transport.connection.ws;
            }
            if (!socket) {
                console.error('❌ WebSocket not available');
                updateStatus('❌ WebSocket not created', 'bg-red-100 text-red-800');
                return;
            }
            
            console.log('✅ WebSocket found:', socket);
            console.log('WebSocket URL:', socket.url);
            console.log('WebSocket readyState:', socket.readyState);
            console.log('WebSocket protocol:', socket.protocol);
            
            const connectionState = socket.readyState;
            const stateNames = {
                0: 'CONNECTING',
                1: 'OPEN', 
                2: 'CLOSING',
                3: 'CLOSED'
            };
            
            console.log(`WebSocket State: ${connectionState} (${stateNames[connectionState]})`);
            
            if (connectionState === 1) { // WebSocket.OPEN
                updateStatus('✅ Connected Successfully', 'bg-green-100 text-green-800');
                
                // Test channel subscription
                 try {
                     const channel = window.Echo.private(`conversation.{{ $conversation->id }}`);
                     console.log('✅ Channel subscription test:', channel);
                     updateStatus('✅ Connected & Channel Ready', 'bg-green-100 text-green-800');
                 } catch (error) {
                     console.error('❌ Channel subscription failed:', error);
                     updateStatus('⚠️ Connected but Channel Failed', 'bg-yellow-100 text-yellow-800');
                 }
            } else {
                updateStatus(`❌ Not Connected (${stateNames[connectionState]})`, 'bg-red-100 text-red-800');
                
                // Add error event listener for debugging
                socket.addEventListener('error', function(error) {
                    console.error('WebSocket Error:', error);
                });
                
                socket.addEventListener('close', function(event) {
                    console.error('WebSocket Closed:', event.code, event.reason);
                });
            }
        };
        
        function updateStatus(message, className) {
            const statusDiv = document.getElementById('connection-status') || document.createElement('div');
            statusDiv.id = 'connection-status';
            statusDiv.className = 'mt-2 p-2 rounded text-sm font-medium ' + className;
            statusDiv.textContent = message;
            
            if (!document.getElementById('connection-status')) {
                testButton.parentNode.insertBefore(statusDiv, testButton.nextSibling);
            }
        }
        
        // Add test button to the page
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages && chatMessages.parentNode) {
            chatMessages.parentNode.insertBefore(testButton, chatMessages);
        }
        
        // Auto-test connection after 3 seconds
        setTimeout(() => {
            // Auto-test WebSocket connection
            console.log('🧪 Auto-testing WebSocket connection...');
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher && 
                window.Echo.connector.pusher.connection && window.Echo.connector.pusher.connection.connection &&
                window.Echo.connector.pusher.connection.connection.transport && 
                window.Echo.connector.pusher.connection.connection.transport.socket) {
                
                const socket = window.Echo.connector.pusher.connection.connection.transport.socket;
                console.log('✅ WebSocket found:', socket);
                console.log('WebSocket URL:', socket.url);
                console.log('WebSocket readyState:', socket.readyState);
                
                if (socket.readyState === 1) {
                    console.log('✅ Connected Successfully - WebSocket is OPEN');
                    // Update UI to show connection status
                    const statusElement = document.querySelector('.connection-status');
                    if (statusElement) {
                        statusElement.textContent = 'Connected';
                        statusElement.className = 'connection-status text-green-600';
                    }
                } else {
                    console.error('❌ WebSocket not in OPEN state. ReadyState:', socket.readyState);
                }
            } else {
                console.error('❌ WebSocket not available');
            }
            testButton.click();
        }, 3000);
    }

    // Function to send typing status
    function sendTypingStatus(typing) {
        console.log('📤 Sending typing status:', typing);
        isTyping = typing;
        
        fetch('{{ route("messages.typing") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: {{ $conversation->id }},
                is_typing: typing
            })
        })
        .then(response => {
            console.log('✅ Typing status sent successfully:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Typing response:', data);
        })
        .catch(error => {
            console.error('❌ Error sending typing status:', error);
        });
    }
    
    // Message input and typing functionality
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            if (!isTyping) {
                sendTypingStatus(true);
            }
            
            // Clear existing timer
            clearTimeout(typingTimer);
            
            // Set timer to stop typing after 2 seconds of inactivity
            typingTimer = setTimeout(() => {
                sendTypingStatus(false);
            }, 2000);
        });
    }
    
    // Message sending functionality
    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const message = messageInput.value.trim();
            
            if (!message) return;
            
            // Stop typing indicator when sending message
            clearTimeout(typingTimer);
            sendTypingStatus(false);
            
            // Disable send button temporarily
            const sendButton = this.querySelector('button[type="submit"]');
            sendButton.disabled = true;
            sendButton.textContent = 'Sending...';
            
            try {
                const response = await fetch('{{ route("messages.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    messageInput.value = '';
                    // Add message immediately for current user
                    addMessageToChat(data.message);
                    scrollToBottom();
                } else {
                    throw new Error(data.message || 'Failed to send message');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            } finally {
                // Re-enable send button
                sendButton.disabled = false;
                sendButton.textContent = 'Send';
            }
        });
    }
});

// Function to add new message to chat
function addMessageToChat(message) {
    const chatMessages = document.getElementById('chatMessages');
    const isCurrentUser = message.sender_id === currentUserId;
    
    const messageElement = document.createElement('div');
    messageElement.className = `flex ${isCurrentUser ? 'justify-end' : 'justify-start'} mb-4`;
    
    const currentTime = new Date().toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: false 
    });
    
    messageElement.innerHTML = `
        <div class="max-w-xs lg:max-w-md">
            <div class="flex items-end space-x-2 ${isCurrentUser ? 'flex-row-reverse space-x-reverse' : ''}">
                <img src="${message.sender.avatar ? '/storage/' + message.sender.avatar : '/images/default-avatar.png'}" 
                     alt="${message.sender.name}" 
                     class="w-8 h-8 rounded-full object-cover">
                <div class="px-4 py-2 rounded-lg ${isCurrentUser ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200'}">
                    <p class="text-sm">${message.body}</p>
                    <p class="text-xs mt-1 ${isCurrentUser ? 'text-blue-100' : 'text-gray-500'}">
                        ${currentTime}
                    </p>
                </div>
            </div>
        </div>
    `;
    
    chatMessages.appendChild(messageElement);
}

// Function to scroll to bottom of chat
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// Service application handling
async function handleApplication(applicationId, action) {
    try {
        const response = await fetch(`/service-applications/${applicationId}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (response.ok && data.success) {
            // Refresh the page to show updated status
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to process application');
        }
    } catch (error) {
        console.error('Error processing application:', error);
        alert('Failed to process application. Please try again.');
    }
}

// Mark service as completed
async function markCompleted(applicationId) {
    if (!confirm('Are you sure you want to mark this service as completed?')) {
        return;
    }
    
    try {
        const response = await fetch(`/service-applications/${applicationId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            alert(data.message);
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to mark as completed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// Review modal function (placeholder)
function openReviewModal(applicationId) {
        // Set the service application ID in a hidden form field
        document.getElementById('reviewServiceApplicationId').value = applicationId;
        document.getElementById('reviewModal').classList.remove('hidden');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.getElementById('reviewForm').reset();
        resetStars();
    }

    function setRating(rating) {
        document.getElementById('rating').value = rating;
        
        // Update star display
        document.querySelectorAll('.star-button').forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    function resetStars() {
        document.querySelectorAll('.star-button').forEach(star => {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        });
        document.getElementById('rating').value = '';
    }

    // Handle review form submission
    document.getElementById('reviewForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const rating = document.getElementById('rating').value;
        if (!rating) {
            alert('Please select a rating');
            return;
        }
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('{{ route("reviews.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(data.message);
                closeReviewModal();
                location.reload(); // Refresh to update the UI
            } else {
                alert(data.error || 'An error occurred while submitting the review');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while submitting the review');
        }
    });

// Service application modal functions
function openServiceApplicationModal() {
    // Load available services for the provider
    loadProviderServices();
    document.getElementById('serviceApplicationModal').classList.remove('hidden');
}

function closeServiceApplicationModal() {
    document.getElementById('serviceApplicationModal').classList.add('hidden');
}

async function loadProviderServices() {
    const providerId = document.querySelector('input[name="provider_id"]').value;
    const serviceSelect = document.getElementById('service_id');
    
    try {
        const response = await fetch(`/api/users/${providerId}/services`);
        const services = await response.json();
        
        serviceSelect.innerHTML = '<option value="">Choose a service...</option>';
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = `${service.title} - $${service.price}`;
            serviceSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Failed to load services:', error);
    }
}

// Service application form submission
document.getElementById('serviceApplicationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    submitButton.disabled = true;
    submitButton.textContent = 'Submitting...';
    
    try {
        const response = await fetch('{{ route("service-applications.apply") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            closeServiceModal();
            // Show success message
            alert('Service application submitted successfully!');
            // Optionally redirect to service requests page
            window.location.href = '{{ route("service-requests.index") }}';
        } else {
            throw new Error(data.error || 'Failed to submit application');
        }
    } catch (error) {
        alert('Failed to submit application: ' + error.message);
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
});

// Close modal when clicking outside
document.getElementById('serviceApplicationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeServiceApplicationModal();
    }
});



// Function to initialize typing indicators
function initializeTypingIndicators() {
    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
        console.log('🔄 Initializing typing indicators...');
        try {
            const channel = window.Echo.private('conversation.{{ $conversation->id }}');
            
            channel.listen('.user.typing', (e) => {
                console.log('⌨️ Typing event received:', e);
                console.log('Event user ID:', e.user_id, 'Current user ID:', {{ auth()->id() }});
                
                const typingIndicator = document.getElementById('typingIndicator');
                const typingText = document.getElementById('typingText');
                
                console.log('Typing indicator element:', typingIndicator);
                console.log('Typing text element:', typingText);
                
                // Don't show typing indicator for current user
                if (e.user_id === {{ auth()->id() }}) {
                    console.log('❌ Ignoring typing event from current user');
                    return;
                }
                
                if (e.is_typing) {
                    console.log('✅ Showing typing indicator for:', e.user_name);
                    if (typingText) typingText.textContent = `${e.user_name} is typing...`;
                    if (typingIndicator) typingIndicator.classList.remove('hidden');
                    scrollToBottom();
                } else {
                    console.log('✅ Hiding typing indicator for:', e.user_name);
                    if (typingIndicator) typingIndicator.classList.add('hidden');
                }
            });
            
            // Also listen for the event without the dot prefix (in case of different event naming)
            channel.listen('user.typing', (e) => {
                console.log('⌨️ Typing event received (without dot):', e);
                console.log('Event user ID:', e.user_id, 'Current user ID:', {{ auth()->id() }});
                
                const typingIndicator = document.getElementById('typingIndicator');
                const typingText = document.getElementById('typingText');
                
                // Don't show typing indicator for current user
                if (e.user_id === {{ auth()->id() }}) {
                    console.log('❌ Ignoring typing event from current user');
                    return;
                }
                
                if (e.is_typing) {
                    console.log('✅ Showing typing indicator for:', e.user_name);
                    if (typingText) typingText.textContent = `${e.user_name} is typing...`;
                    if (typingIndicator) typingIndicator.classList.remove('hidden');
                    scrollToBottom();
                } else {
                    console.log('✅ Hiding typing indicator for:', e.user_name);
                    if (typingIndicator) typingIndicator.classList.add('hidden');
                }
            });
            
            console.log('✅ Typing indicators initialized successfully');
        } catch (error) {
            console.error('❌ Error initializing typing indicators:', error);
        }
    } else {
        console.error('❌ Echo is not available for typing indicators');
        console.log('Echo state:', {
            Echo: !!window.Echo,
            connector: !!(window.Echo && window.Echo.connector),
            pusher: !!(window.Echo && window.Echo.connector && window.Echo.connector.pusher)
        });
    }
}

// Note: initializeTypingIndicators() will be called after Echo loads via CDN
</script>

<!-- Load Echo via CDN if not available -->
<script>
// Check if Echo is loaded, if not, load it via CDN
if (typeof window.Echo === 'undefined') {
    console.log('Echo not found, loading via CDN...');
    
    // Load Pusher first
    const pusherScript = document.createElement('script');
    pusherScript.src = 'https://js.pusher.com/8.2.0/pusher.min.js';
    pusherScript.onload = function() {
        console.log('Pusher loaded');
        
        // Load Laravel Echo
        const echoScript = document.createElement('script');
        echoScript.src = 'https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js';
        echoScript.onload = function() {
            console.log('Echo loaded, initializing...');
            
            // Initialize Echo
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ env('VITE_REVERB_APP_KEY') }}',
                wsHost: '127.0.0.1',
                wsPort: 6001,
                wssPort: 6001,
                forceTLS: false,
                enabledTransports: ['ws'],
                disableStats: true,
            });
            
            console.log('Echo initialized via CDN:', window.Echo);
            
            // Wait for Echo to fully connect before setting up typing indicators
            setTimeout(() => {
                initializeTypingIndicators();
            }, 500);
            
            // Trigger the test button after Echo is loaded
            setTimeout(() => {
                const testButton = document.querySelector('button[onclick*="Testing WebSocket"]');
                if (testButton) {
                    console.log('Auto-triggering test after Echo load...');
                    testButton.click();
                }
            }, 1000);
        };
        document.head.appendChild(echoScript);
    };
    document.head.appendChild(pusherScript);
}
// Find the message form by ID (assuming your form has an ID 'messageForm')
const messageForm = document.getElementById('messageForm');
const messageInput = document.getElementById('messageInput');

// Add an event listener to handle the form submission
messageForm.addEventListener('submit', async function(e) {
    e.preventDefault();  // Prevent default form submission (which reloads the page)

    const formData = new FormData(this);  // Capture the form data
    const sendButton = this.querySelector('button[type="submit"]');
    sendButton.disabled = true;  // Disable the send button to prevent multiple submissions
    sendButton.textContent = 'Sending...';  // Change button text

    try {
        // Make the fetch request to send the message to the backend
        const response = await fetch('{{ route("messages.store") }}', {
            method: 'POST',  // Use POST to send data
            body: formData,  // Send the form data as the body of the request
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',  // Expect JSON response
                'X-Requested-With': 'XMLHttpRequest'  // Let the backend know it's an AJAX request
            }
        });

        const data = await response.json();  // Parse the response as JSON

        if (response.ok) {
            messageInput.value = '';  // Clear the input field after sending the message
            addMessageToChat(data.message);  // Call your function to display the new message in the chat
            scrollToBottom();  // Ensure the chat scrolls to the latest message
        } else {
            throw new Error(data.message || 'Failed to send message');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    } finally {
        sendButton.disabled = false;  // Re-enable the send button
        sendButton.textContent = 'Send';  // Reset button text
    }
});

</script>
</x-app-layout>