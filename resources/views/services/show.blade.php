<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header + Tabs -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $service->hss_title }}</h1>
                        <p class="text-gray-600 mt-2">Service details by {{ $provider->hu_name }}</p>
                    </div>
                    <a href="{{ route('students.profile', $provider->hu_id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">View Provider Profile</a>
                </div>
            </div>

            <!-- Details Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="space-y-2">
                        @if($service->category)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">{{ $service->category->hc_name }}</span>
                        @endif
                        <div class="text-sm text-gray-500">Status: {{ $service->hss_status }}</div>
                    </div>
                    @if(!is_null($service->hss_suggested_price))
                        <div class="text-right">
                            <span class="whitespace-nowrap text-xl font-semibold text-indigo-600">RM {{ number_format($service->hss_suggested_price, 2) }}</span>
                        </div>
                    @endif
                </div>

                @if($service->hss_description)
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $service->hss_description }}</p>
                    </div>
                @else
                    <p class="text-gray-500">No description provided for this service.</p>
                @endif

                <!-- Provider + Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($provider->hu_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $provider->hu_name }}</div>
                                <div class="text-sm text-gray-500">{{ $provider->isStudent() ? 'Student Service Provider' : 'UPSI User' }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @auth
                                @if($canContactProvider)
                                    <a href="{{ route('chat.request', ['user' => $provider->hu_id]) }}"
                                       class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white {{ $provider->hu_is_available ? 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' : 'bg-gray-400 cursor-not-allowed' }} focus:outline-none focus:ring-2 transition-colors"
                                       {{ !$provider->hu_is_available ? 'aria-disabled=true' : '' }}>
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.436L3 21l2.436-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                                        </svg>
                                        {{ $provider->hu_is_available ? 'Send Message' : 'Currently Unavailable' }}
                                    </a>
                                    <x-favorite-button :user-id="$provider->hu_id" :is-favorited="$isProviderFavorited ?? false" />
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">Login to Contact</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back link -->
            <div class="mt-6">
                <a href="{{ route('students.profile', $provider->hu_id) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">← Back to {{ $provider->hu_name }}'s profile</a>
            </div>
        </div>
    </div>
</x-app-layout>
