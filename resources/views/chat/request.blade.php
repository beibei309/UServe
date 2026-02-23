<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('search.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Services
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Contact Service Provider</h1>
            <p class="text-gray-600 mt-2">Send a message to discuss the service before making a formal request.</p>
        </div>

        <!-- Provider Info -->
        @if(isset($provider))
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center space-x-4">
                <img src="{{ $provider->profile_picture ? asset('storage/' . $provider->profile_picture) : asset('images/default-avatar.png') }}" 
                     alt="{{ $provider->name }}" 
                     class="w-16 h-16 rounded-full object-cover">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $provider->name }}</h2>
                    <p class="text-gray-600">{{ $provider->email }}</p>
                    @if(isset($serviceTitle))
                        <p class="text-sm text-blue-600 mt-1">Service: {{ $serviceTitle }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Chat Request Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Send Initial Message</h3>
            
            <form id="chatRequestForm">
                @csrf
                <input type="hidden" name="student_id" value="{{ $provider->id ?? request('user') }}">
                
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Message <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="message" rows="6" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Hi! I'm interested in your {{ request('service') ?? 'service' }}. Could we discuss the details?"></textarea>
                    <p class="text-sm text-gray-500 mt-1">
                        Introduce yourself and explain what you're looking for. This helps the provider understand your needs better.
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900">How it works:</h4>
                            <ul class="text-sm text-blue-800 mt-1 space-y-1">
                                <li>1. Send your initial message to the provider</li>
                                <li>2. Discuss details, requirements, and pricing via chat</li>
                                <li>3. Once you both agree, you can make a formal service request</li>
                                <li>4. Provider accepts the request and service becomes unavailable to others</li>
                                <li>5. Complete the service and leave reviews for each other</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('search.index') }}" 
                       class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('chatRequestForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    submitButton.disabled = true;
    submitButton.textContent = 'Sending...';
    
    try {
        const response = await fetch('{{ route("chat-requests.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Server returned an invalid response. Please check the console for details.');
        }
        
        const data = await response.json();
        
        if (response.ok) {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg z-50 shadow-lg';
                alertDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-medium">Message sent successfully!</p>
                            <p class="text-sm">The provider will be notified and can respond to your message.</p>
                        </div>
                    </div>
                `;
                document.body.appendChild(alertDiv);
                
                // Redirect to chat index after 2 seconds
                setTimeout(() => {
                    window.location.href = '{{ route("chat.index") }}';
                }, 2000);
            } else {
                throw new Error(data.error || 'Unknown error');
            }
            
        } else {
            throw new Error(data.error || 'Failed to send message');
        }
    } catch (error) {
        // Show error message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg z-50 shadow-lg';
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="font-medium">Failed to send message</p>
                    <p class="text-sm">${error.message}</p>
                </div>
            </div>
        `;
        document.body.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
});
</script>
</x-app-layout>