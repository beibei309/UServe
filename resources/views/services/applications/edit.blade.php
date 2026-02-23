<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Application</h1>
                        <p class="text-gray-600 mt-2">Update the details of your service request</p>
                    </div>
                    <a href="{{ route('services.applications.show', $application) }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Back to Details</span>
                    </a>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-red-700">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Edit Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <form method="POST" action="{{ route('services.applications.update', $application) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

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
                            @php
                                $types = [
                                    'tutoring' => 'Academic Tutoring',
                                    'design' => 'Graphic Design',
                                    'web_development' => 'Web Development',
                                    'photography' => 'Photography',
                                    'writing' => 'Content Writing',
                                    'translation' => 'Translation Services',
                                    'music' => 'Music Lessons',
                                    'fitness' => 'Fitness Training',
                                    'event_planning' => 'Event Planning',
                                    'other' => 'Other'
                                ];
                            @endphp
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ old('service_type', $application->service_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Service Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $application->title) }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="e.g., Need help with Calculus, Logo design for my business">
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
                                  placeholder="Describe your requirements in detail">{{ old('description', $application->description) }}</textarea>
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
                            @php
                                $budgets = [
                                    'under_50' => 'Under RM 50',
                                    '50_100' => 'RM 50 - RM 100',
                                    '100_200' => 'RM 100 - RM 200',
                                    '200_500' => 'RM 200 - RM 500',
                                    '500_1000' => 'RM 500 - RM 1,000',
                                    'over_1000' => 'Over RM 1,000',
                                    'negotiable' => 'Negotiable'
                                ];
                            @endphp
                            @foreach($budgets as $key => $label)
                                <option value="{{ $key }}" {{ old('budget_range', $application->budget_range) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
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
                            @php
                                $timelines = [
                                    'asap' => 'As soon as possible',
                                    'within_week' => 'Within a week',
                                    'within_month' => 'Within a month',
                                    'flexible' => 'Flexible timeline',
                                    'ongoing' => 'Ongoing project'
                                ];
                            @endphp
                            <option value="">When do you need this completed?</option>
                            @foreach($timelines as $key => $label)
                                <option value="{{ $key }}" {{ old('timeline', $application->timeline) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Contact Preferences -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Preferred Contact Method
                        </label>
                        @php
                            $methods = old('contact_methods', (array)($application->contact_methods ?? []));
                        @endphp
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="platform_chat" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ in_array('platform_chat', $methods) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Platform messaging system</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="email" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ in_array('email', $methods) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Email communication</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="phone" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ in_array('phone', $methods) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Phone/WhatsApp</span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('services.applications.show', $application) }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m-7-7h14"></path>
                            </svg>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>