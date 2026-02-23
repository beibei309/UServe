<x-app-layout>
    <style>
        .rich-text ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin-bottom: 1rem;
        }

        .rich-text ol {
            list-style-type: decimal;
            padding-left: 1.25rem;
            margin-bottom: 1rem;
        }

        .rich-text li {
            margin-bottom: 0.25rem;
        }

        .rich-text p {
            margin-bottom: 0.75rem;
        }

        .rich-text strong {
            font-weight: 600;
            color: #1e293b;
        }
    </style>

    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">

            <div class="mb-8">
                <a href="{{ route('service-requests.index') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Requests
                </a>
            </div>

            @php
                $isRequester = auth()->id() === $serviceRequest->requester_id;
                $isProvider = auth()->id() === $serviceRequest->provider_id;
                $service = $serviceRequest->studentService;

                // --- 1. Check Suspension Status ---
                $providerBanned = $serviceRequest->provider->is_suspended == 1 || $serviceRequest->provider->is_blacklisted == 1;
                $buyerBanned = $serviceRequest->requester->is_suspended == 1 || $serviceRequest->requester->is_blacklisted == 1;
                $isSuspended = $providerBanned || $buyerBanned;

                // --- 2. Determine Colors (Using Inline Styles to guarantee visibility) ---
                
                if ($isSuspended) {
                    // Force RED if suspended
                    $headerStyle = 'background: linear-gradient(to right, #ef4444, #b91c1c);'; // Red-500 to Red-700
                    $statusDot = 'bg-red-400';
                } else {
                    switch($serviceRequest->status) {
                        case 'in_progress':
                        case 'accepted':
                            // Blue
                            $headerStyle = 'background: linear-gradient(to right, #3b82f6, #4f46e5);'; 
                            $statusDot = 'bg-blue-300';
                            break;
                        case 'completed':
                            // Green
                            $headerStyle = 'background: linear-gradient(to right, #10b981, #059669);'; 
                            $statusDot = 'bg-green-300';
                            break;
                        case 'cancelled':
                        case 'rejected':
                        case 'disputed':
                            // Gray/Redish
                            $headerStyle = 'background: linear-gradient(to right, #6b7280, #374151);'; 
                            $statusDot = 'bg-red-300';
                            break;
                        case 'waiting_payment':
                            // Yellow
                            $headerStyle = 'background: linear-gradient(to right, #facc15, #ca8a04);'; 
                            $statusDot = 'bg-yellow-300';
                            break;
                        case 'pending':
                        default:
                            // Orange/Yellow
                            $headerStyle = 'background: linear-gradient(to right, #facc15, #fb923c);'; 
                            $statusDot = 'bg-white'; 
                            break;
                    }
                }
            @endphp

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Dynamic Header Background --}}
                <div class="relative px-8 py-10 text-white" style="{{ $headerStyle }}">
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <span class="inline-block px-3 py-1 mb-3 text-xs font-bold tracking-wider uppercase bg-white/20 rounded-full">
                                Request #{{ $serviceRequest->id }}
                            </span>
                            <h1 class="text-3xl font-extrabold tracking-tight text-white">
                                {{ optional($service)->title ?? 'Custom Request' }}
                            </h1>
                            <p class="mt-2 text-indigo-100 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Requested on {{ $serviceRequest->created_at->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>

                        <div class="px-5 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 shadow-sm">
                            <span class="text-sm font-semibold uppercase tracking-wide">Status</span>
                            <div class="text-xl font-bold capitalize flex items-center gap-2 mt-1">
                                {{-- Dynamic Status Dot --}}
                                <span class="w-3 h-3 rounded-full {{ $statusDot }}"></span>
                                
                                @if($isSuspended)
                                    Suspended / Blocked
                                @else
                                    {{ str_replace('_', ' ', $serviceRequest->status) }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                        <div class="lg:col-span-2 space-y-8">

                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Request Details
                                </h3>
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-6">

                                    <div>
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Package Selected</label>
                                        <p class="text-lg font-medium text-gray-900 mt-1">
                                            {{ ucfirst(trim($serviceRequest->selected_package, '"') ?? 'Custom') }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">From</label>
                                        <p class="text-lg font-bold text-green-600 mt-1">
                                            @if ($serviceRequest->offered_price)
                                                RM {{ number_format($serviceRequest->offered_price, 2) }}
                                            @else
                                                <span class="text-gray-400 italic">Not specified</span>
                                            @endif
                                        </p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Requested Dates</label>
                                        <div class="flex flex-col gap-1 mt-1 text-gray-800 font-medium">
                                            @if (is_array($serviceRequest->selected_dates))
                                                @foreach ($serviceRequest->selected_dates as $date)
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                                                    </div>
                                                @endforeach
                                            @elseif($serviceRequest->selected_dates)
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($serviceRequest->selected_dates)->format('l, F j, Y') }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">No dates selected</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($serviceRequest->status === 'rejected' && $serviceRequest->rejection_reason)
                                        <div class="sm:col-span-2">
                                            <label class="text-xs font-semibold text-red-500 uppercase tracking-wide">
                                                Rejection Reason
                                            </label>
                                            <div class="mt-2 p-4 bg-red-50 rounded-lg border border-red-100 text-red-800">
                                                <div class="flex gap-3">
                                                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    <div>
                                                        <p class="font-bold text-sm">Request Rejected</p>
                                                        <p class="text-sm mt-1">"{{ $serviceRequest->rejection_reason }}"</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($serviceRequest->message)
                                        <div class="sm:col-span-2">
                                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Message from Buyer</label>
                                            <div class="mt-2 p-4 bg-white rounded-lg border border-gray-200 text-gray-600">
                                                {{ $serviceRequest->message }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if ($serviceRequest->status == 'completed')
                                        <div class="sm:col-span-2 mt-4">
                                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Job Timeline</label>
                                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                                                    <span class="block text-xs text-blue-400 uppercase font-bold">Accepted</span>
                                                    <span class="font-medium text-gray-700 text-sm">
                                                        {{ $serviceRequest->accepted_at ? $serviceRequest->accepted_at->format('d M, h:i A') : '-' }}
                                                    </span>
                                                </div>
                                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                                                    <span class="block text-xs text-yellow-500 uppercase font-bold">Started Work</span>
                                                    <span class="font-medium text-gray-700 text-sm">
                                                        {{ $serviceRequest->started_at ? $serviceRequest->started_at->format('d M, h:i A') : '-' }}
                                                    </span>
                                                </div>
                                                <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                                                    <span class="block text-xs text-green-500 uppercase font-bold">Finished</span>
                                                    <span class="font-medium text-gray-700 text-sm">
                                                        {{ $serviceRequest->finished_at ? $serviceRequest->finished_at->format('d M, h:i A') : '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($service)
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Service Reference
                                    </h3>

                                    @if($providerBanned)
                                        {{-- BANNED: Non-clickable Div --}}
                                        <div class="block bg-gray-50 rounded-xl border border-gray-200 p-5 flex gap-4 items-start opacity-75 cursor-not-allowed relative">
                                            {{-- "Unavailable" Badge --}}
                                            <div class="absolute top-3 right-3 bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                                                Unavailable
                                            </div>

                                            <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 grayscale">
                                                @if ($service->image_path)
                                                    <img src="{{ Str::startsWith($service->image_path, 'services/') ? asset('storage/' . $service->image_path) : asset($service->image_path) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" /></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-500">{{ $service->title }}</h4>
                                                <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600">
                                                    {{ optional($service->category)->name }}
                                                </span>
                                                <p class="text-sm text-gray-400 mt-2 line-clamp-2">{{ strip_tags($service->description) }}</p>
                                            </div>
                                        </div>
                                    @else
                                        {{-- ACTIVE: Clickable Link --}}
                                        <a href="{{ route('services.details', $service->id) }}" class="block bg-white rounded-xl border border-gray-200 p-5 flex gap-4 items-start hover:shadow-md transition-all hover:border-indigo-300 group">
                                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                @if ($service->image_path)
                                                    <img src="{{ Str::startsWith($service->image_path, 'services/') ? asset('storage/' . $service->image_path) : asset($service->image_path) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" /></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $service->title }}</h4>
                                                <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700">
                                                    {{ optional($service->category)->name }}
                                                </span>
                                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ strip_tags($service->description) }}</p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endif                        </div>

                        <div class="space-y-8">
                            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-6 border-b border-gray-100 pb-2">
                                    People Involved
                                </h3>

                                {{-- 1. Service Seller --}}
                                <div class="mb-6">
                                    <span class="text-xs font-semibold text-indigo-500 mb-2 block">Service Seller</span>
                                    @if($providerBanned)
                                        <div class="flex items-center gap-3 p-2 -ml-2 rounded-lg bg-gray-50 border border-gray-100 opacity-75 cursor-not-allowed">
                                            <div class="relative">
                                                <img src="{{ $serviceRequest->provider->profile_photo_path ? asset($serviceRequest->provider->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceRequest->provider->name) }}" class="w-10 h-10 rounded-full border border-gray-200 grayscale">
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-500 line-through">{{ $serviceRequest->provider->name }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 6.524a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>
                                                    Seller Suspended
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ route('students.profile', $serviceRequest->provider->id) }}" class="flex items-center gap-3 p-2 -ml-2 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <img src="{{ $serviceRequest->provider->profile_photo_path ? asset($serviceRequest->provider->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceRequest->provider->name) }}" class="w-10 h-10 rounded-full border border-gray-200 group-hover:border-indigo-200">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $serviceRequest->provider->name }} <span class="text-gray-300 text-[10px] ml-1">↗</span></p>
                                                <div class="flex items-center text-xs text-yellow-500">
                                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    <span class="ml-1 text-gray-600">{{ number_format(\App\Models\Review::where('reviewee_id', $serviceRequest->provider->id)->avg('rating') ?? 0, 1) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>

                                {{-- 2. Requester (Buyer) --}}
                                <div>
                                    <span class="text-xs font-semibold text-blue-500 mb-2 block">Buyer</span>
                                    @if($buyerBanned)
                                        <div class="flex items-center gap-3 p-2 -ml-2 rounded-lg bg-gray-50 border border-gray-100 opacity-75 cursor-not-allowed">
                                            <div class="relative">
                                                <img src="{{ $serviceRequest->requester->profile_photo_path ? asset($serviceRequest->requester->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceRequest->requester->name) }}" class="w-10 h-10 rounded-full border border-gray-200 grayscale">
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-500 line-through">{{ $serviceRequest->requester->name }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 6.524a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>
                                                    User Suspended
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ route('profile.public', $serviceRequest->requester->id) }}" class="flex items-center gap-3 p-2 -ml-2 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <img src="{{ $serviceRequest->requester->profile_photo_path ? asset($serviceRequest->requester->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceRequest->requester->name) }}" class="w-10 h-10 rounded-full border border-gray-200 group-hover:border-blue-200">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $serviceRequest->requester->name }} <span class="text-gray-300 text-[10px] ml-1">↗</span></p>
                                                <div class="flex items-center text-xs text-yellow-500">
                                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    <span class="ml-1 text-gray-600">{{ number_format(\App\Models\Review::where('reviewee_id', $serviceRequest->requester->id)->avg('rating') ?? 0, 1) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-4">Available Actions</h3>
                                <div class="space-y-3">
                                    @php
                                        $contactPhone = $isProvider ? $serviceRequest->requester->phone : $serviceRequest->provider->phone;
                                    @endphp
                                    
                                    {{-- If users are suspended, disable actions --}}
                                    @if($isSuspended)
                                        <div class="p-3 bg-red-50 text-red-700 text-sm font-medium rounded-lg border border-red-100 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                            Actions unavailable due to account suspension.
                                        </div>
                                    @else
                                        @if ($contactPhone)
                                            <a href="https://wa.me/6{{ $contactPhone }}" target="_blank"
                                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold shadow-sm transition-all hover:shadow-md">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" /></svg>
                                                Chat on WhatsApp
                                            </a>
                                        @endif

                                        @if ($isProvider && $serviceRequest->isPending())
                                            <button onclick="updateRequestStatus({{ $serviceRequest->id }}, 'accept')" class="w-full py-2.5 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 shadow-sm transition">Accept Request</button>
                                            <button onclick="updateRequestStatus({{ $serviceRequest->id }}, 'reject')" class="w-full py-2.5 bg-white border border-red-200 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition">Reject Request</button>
                                        @endif

                                        @if ($isProvider && $serviceRequest->isAccepted())
                                            <button onclick="updateRequestStatus({{ $serviceRequest->id }}, 'in-progress')" class="w-full py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 shadow-sm transition">Start Work</button>
                                        @endif

                                        @if ($isProvider && $serviceRequest->isInProgress())
                                            <button onclick="updateRequestStatus({{ $serviceRequest->id }}, 'complete')" class="w-full py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 shadow-sm transition">Mark as Completed</button>
                                        @endif

                                        @if ($isRequester && $serviceRequest->status === 'pending')
                                            <button onclick="updateRequestStatus({{ $serviceRequest->id }}, 'cancel')" class="w-full py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">Cancel Request</button>
                                        @elseif ($isRequester && ($serviceRequest->status === 'in_progress' || $serviceRequest->status === 'accepted'))
                                            <button disabled class="w-full py-2.5 bg-gray-100 border border-gray-200 text-gray-400 rounded-lg font-semibold cursor-not-allowed flex items-center justify-center gap-2"><i class="fa-solid fa-play"></i> Work Started</button>
                                        @endif

                                        @if ($isRequester && $serviceRequest->isCompleted() && !$serviceRequest->reviews()->where('reviewer_id', auth()->id())->exists())
                                            <button onclick="openReviewModal({{ $serviceRequest->id }}, '')" class="w-full py-2.5 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 shadow-sm transition">Leave a Review</button>
                                        @endif
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    @if ($serviceRequest->reviews->count() > 0)
                        <div class="mt-10 border-t border-gray-100 pt-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Reviews & Feedback</h2>
                            <div class="grid gap-4">
                                @foreach ($serviceRequest->reviews as $review)
                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                                    {{ substr($review->reviewer->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-sm text-gray-900">{{ $review->reviewer->name }}</p>
                                                    <div class="flex text-yellow-400 text-xs">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if ($review->comment)
                                            <p class="mt-3 text-gray-600 text-sm leading-relaxed">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div id="reviewModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Rate Your Experience</h3>
                <button onclick="closeReviewModal()" class="text-white/80 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6">
                <form id="reviewForm" class="space-y-6">
                    @csrf
                    <input type="hidden" id="reviewServiceRequestId" name="service_request_id">
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-2">How would you rate this service?</label>
                        <div class="flex justify-center gap-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating({{ $i }})" class="star-button text-4xl text-gray-300 hover:text-yellow-400 transition-colors focus:outline-none">★</button>
                            @endfor
                        </div>
                        <input type="hidden" id="rating" name="rating" required>
                    </div>
                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Your Review (Optional)</label>
                        <textarea id="comment" name="comment" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none" placeholder="Tell us what you liked..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeReviewModal()" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        async function updateRequestStatus(requestId, action) {
            let confirmText = "Are you sure?";
            let confirmBtnColor = "#3085d6";
            let bodyData = {}; 

            if (action === 'reject') {
                const { value: reason } = await Swal.fire({
                    title: 'Reject Request',
                    text: "Please provide a reason for the student:",
                    input: 'textarea',
                    inputPlaceholder: 'e.g. Fully booked on this date...',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Reject it',
                    inputValidator: (value) => { if (!value) return 'You need to write something!' }
                });
                if (!reason) return;
                confirmText = "This action cannot be undone.";
                confirmBtnColor = "#d33";
                bodyData = { rejection_reason: reason };
            }
            else if (action === 'cancel') {
                const result = await Swal.fire({
                    title: 'Cancel Request?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Cancel it'
                });
                if (!result.isConfirmed) return;
            }
            else {
                const result = await Swal.fire({
                    title: 'Confirm Action',
                    text: "Are you sure you want to proceed?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, proceed'
                });
                if (!result.isConfirmed) return;
            }

            const endpoints = {
                'accept': `/service-requests/${requestId}/accept`,
                'reject': `/service-requests/${requestId}/reject`,
                'in-progress': `/service-requests/${requestId}/mark-in-progress`,
                'complete': `/service-requests/${requestId}/mark-completed`,
                'cancel': `/service-requests/${requestId}/cancel`
            };

            try {
                const response = await fetch(endpoints[action], {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(bodyData)
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire('Success', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || data.message || 'Action failed', 'error');
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'Network error occurred', 'error');
            }
        }

        // Review Logic
        function openReviewModal(id, name) {
            document.getElementById('reviewServiceRequestId').value = id;
            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
            document.getElementById('reviewForm').reset();
            resetStars();
        }

        function setRating(rating) {
            document.getElementById('rating').value = rating;
            document.querySelectorAll('.star-button').forEach((star, index) => {
                star.classList.toggle('text-yellow-400', index < rating);
                star.classList.toggle('text-gray-300', index >= rating);
            });
        }

        function resetStars() { setRating(0); }

        document.getElementById('reviewForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const res = await fetch('/reviews', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    closeReviewModal();
                    Swal.fire("Thank You!", "Review submitted.", "success").then(() => location.reload());
                } else {
                    Swal.fire("Error", data.error, "error");
                }
            } catch (err) {
                Swal.fire("Error", "System error", "error");
            }
        });
    </script>
</x-app-layout>