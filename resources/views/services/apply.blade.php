<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header + Tabs -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Apply for Services</h1>
                        <p class="text-gray-600 mt-2">Request help from talented UPSI students</p>
                    </div>
                    <a href="{{ route('search.index') }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>Browse Services</span>
                    </a>
                </div>
                <div class="mt-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('services.apply') }}" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm font-medium border-indigo-600 text-indigo-600">Apply for Services</a>
                        @auth
                            @if(Auth::user()->isStudent())
                                <a href="{{ route('services.create') }}" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">Add New Service</a>
                            @endif
                        @endauth
                    </nav>
                </div>
            </div>

            <!-- Verification Notice -->
            @if(!Auth::user()->isVerifiedPublic())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Account Verification Required</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>To apply for services, you need to complete your account verification. This helps ensure the safety and trust of our community.</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('onboarding.community.verify') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    Complete Verification
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Service Application Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <form id="applyServiceForm" class="space-y-6">
                    @csrf
                    
                    <!-- Service Type -->
                    <div>
                        <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                            What type of service do you need? <span class="text-red-500">*</span>
                        </label>
                        <select id="service_type" 
                                name="service_type" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Select a service type</option>
                            <option value="tutoring">Academic Tutoring</option>
                            <option value="design">Graphic Design</option>
                            <option value="web_development">Web Development</option>
                            <option value="photography">Photography</option>
                            <option value="writing">Content Writing</option>
                            <option value="translation">Translation Services</option>
                            <option value="music">Music Lessons</option>
                            <option value="fitness">Fitness Training</option>
                            <option value="event_planning">Event Planning</option>
                            <option value="other">Other</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Choose the category that best matches your needs</p>
                    </div>

                    <!-- Service Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="e.g., Need help with Calculus, Logo design for my business">
                        <p class="text-sm text-gray-500 mt-1">Provide a clear, specific title for what you need</p>
                    </div>

                    <!-- Detailed Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Detailed Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="6"
                                  required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                  placeholder="Please describe your requirements in detail:
- What exactly do you need help with?
- What are your expectations?
- Any specific skills or qualifications required?
- Timeline and deadlines
- Any additional information that would help students understand your needs"></textarea>
                        <p class="text-sm text-gray-500 mt-1">The more details you provide, the better students can understand and respond to your needs</p>
                    </div>

                    <!-- Budget Range -->
                    <div>
                        <label for="budget_range" class="block text-sm font-medium text-gray-700 mb-2">
                            Budget Range (RM)
                        </label>
                        <select id="budget_range" 
                                name="budget_range" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Select your budget range (optional)</option>
                            <option value="under_50">Under RM 50</option>
                            <option value="50_100">RM 50 - RM 100</option>
                            <option value="100_200">RM 100 - RM 200</option>
                            <option value="200_500">RM 200 - RM 500</option>
                            <option value="500_1000">RM 500 - RM 1,000</option>
                            <option value="over_1000">Over RM 1,000</option>
                            <option value="negotiable">Negotiable</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Providing a budget range helps students understand your expectations</p>
                    </div>

                    <!-- Timeline -->
                    <div>
                        <label for="timeline" class="block text-sm font-medium text-gray-700 mb-2">
                            Timeline <span class="text-red-500">*</span>
                        </label>
                        <select id="timeline" 
                                name="timeline" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">When do you need this completed?</option>
                            <option value="asap">As soon as possible</option>
                            <option value="within_week">Within a week</option>
                            <option value="within_month">Within a month</option>
                            <option value="flexible">Flexible timeline</option>
                            <option value="ongoing">Ongoing project</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Let students know your timeline expectations</p>
                    </div>

                    <!-- Contact Preferences -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Preferred Contact Method
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="platform_chat" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Platform messaging system</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="email" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Email communication</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="phone" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Phone/WhatsApp</span>
                            </label>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Select how you'd like students to contact you</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center space-x-2"
                                {{ !Auth::user()->isVerifiedPublic() ? 'disabled' : '' }}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span>Submit Application</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- How It Works Section -->
            <div class="mt-12 bg-gray-50 rounded-xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">How It Works</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <span class="text-xl font-bold text-indigo-600">1</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Submit Your Request</h3>
                        <p class="text-sm text-gray-600">Fill out the form with your service requirements and preferences.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <span class="text-xl font-bold text-indigo-600">2</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Students Respond</h3>
                        <p class="text-sm text-gray-600">Qualified students will review and respond to your application.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <span class="text-xl font-bold text-indigo-600">3</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Connect & Collaborate</h3>
                        <p class="text-sm text-gray-600">Choose the best student and start working together on your project.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        document.getElementById('applyServiceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            @if(!Auth::user()->isVerifiedPublic())
                showMessage('Please complete your account verification first.', 'error');
                return;
            @endif
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
            `;
            
            try {
                const response = await fetch('{{ route("services.apply.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showMessage(data.message || 'Service application submitted successfully! Students will be notified and can contact you soon.', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("services.applications.index") }}';
                    }, 2000);
                } else {
                    showMessage(data.error || data.message || 'An error occurred. Please try again.', 'error');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
                
            } catch (error) {
                console.error('Error:', error);
                showMessage('An error occurred. Please try again.', 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
        
        function showMessage(message, type) {
            const messageContainer = document.getElementById('messageContainer');
            const messageDiv = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            
            messageDiv.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg mb-4 transform transition-all duration-300 translate-x-full max-w-md`;
            messageDiv.textContent = message;
            
            messageContainer.appendChild(messageDiv);
            
            // Animate in
            setTimeout(() => {
                messageDiv.classList.remove('translate-x-full');
            }, 100);
            
            // Remove after 5 seconds
            setTimeout(() => {
                messageDiv.classList.add('translate-x-full');
                setTimeout(() => {
                    if (messageContainer.contains(messageDiv)) {
                        messageContainer.removeChild(messageDiv);
                    }
                }, 300);
            }, 5000);
        }
    </script>
</x-app-layout>