<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8 mt-20">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Notifications</h1>
                    <p class="mt-1 text-sm text-gray-500">Stay updated with your latest activities.</p>
                </div>

                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="group flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-indigo-700 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-indigo-500 group-hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>

            <!-- Notifications Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                @if($notifications->count() > 0)
                    <ul role="list" class="divide-y divide-gray-100">
                        @foreach($notifications as $notification)
                            @php
                                $isUnread = !$notification->read_at;
                                $iconClass = 'bg-gray-100 text-gray-500';
                                $icon = '<i class="fa-solid fa-bell"></i>';
                                
                                // Determine Icon based on Notification Type
                                switch ($notification->type) {
                                    case 'App\Notifications\NewServiceRequest':
                                        $iconClass = 'bg-blue-100 text-blue-600';
                                        $icon = '<i class="fa-solid fa-calendar-plus"></i>';
                                        break;
                                    case 'App\Notifications\ServiceRequestStatusUpdated':
                                        if (str_contains(strtolower($notification->data['message'] ?? ''), 'accepted')) {
                                            $iconClass = 'bg-green-100 text-green-600';
                                            $icon = '<i class="fa-solid fa-check"></i>';
                                        } elseif (str_contains(strtolower($notification->data['message'] ?? ''), 'rejected')) {
                                            $iconClass = 'bg-red-100 text-red-600';
                                            $icon = '<i class="fa-solid fa-xmark"></i>';
                                        } else {
                                            $iconClass = 'bg-indigo-100 text-indigo-600';
                                            $icon = '<i class="fa-solid fa-info"></i>';
                                        }
                                        break;
                                    case 'App\Notifications\AdminWarningNotification':
                                        $iconClass = 'bg-red-50 text-red-600 ring-4 ring-red-50';
                                        $icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
                                        break;
                                }
                            @endphp

                            <li class="group {{ $isUnread ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-gray-50 transition duration-200 ease-in-out">
                                <div class="px-6 py-5">
                                    <div class="flex items-start gap-4">
                                        
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full {{ $iconClass }} shadow-sm">
                                            {!! $icon !!}
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-bold text-gray-900 truncate">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </p>
                                                <div class="flex items-center">
                                                    @if($isUnread)
                                                        <span class="inline-block w-2.5 h-2.5 bg-indigo-600 rounded-full mr-3 animate-pulse"></span>
                                                    @endif
                                                    <p class="text-xs text-gray-400">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <p class="text-sm text-gray-600 leading-relaxed mb-3">
                                                {{ $notification->data['message'] ?? '' }}
                                            </p>

                                            <!-- Actions -->
                                            <div class="flex items-center gap-3">
                                                @if(isset($notification->data['action_url']) && $notification->data['action_url'] !== '#')
                                                    <a href="{{ route('notifications.read', $notification->id) }}" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-indigo-200 text-xs font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                                        View Details
                                                        <svg class="ml-1.5 w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </a>
                                                @else
                                                     @if($isUnread)
                                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="text-xs font-medium text-gray-500 hover:text-indigo-600 transition-colors flex items-center gap-1">
                                                                <i class="fa-regular fa-circle-check"></i> Mark as read
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            Showing {{ $notifications->count() }} recent notifications
                        </div>
                        {{ $notifications->links() }}
                    </div>

                @else
                    <!-- Empty State -->
                    <div class="text-center py-20 px-6">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">All caught up!</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-8">You have no new notifications at the moment.</p>
                        <a href="{{ route('dashboard') }}" 
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-house mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
