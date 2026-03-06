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

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Dynamic Header Background --}}
                <div class="relative px-8 py-10 text-white" style="{{ $headerVisual['style'] }}">
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <span class="inline-block px-3 py-1 mb-3 text-xs font-bold tracking-wider uppercase bg-white/20 rounded-full">
                                Request #{{ $serviceRequest->hsr_id }}
                            </span>
                            <h1 class="text-3xl font-extrabold tracking-tight text-white">
                                {{ optional($serviceRequest->studentService)->hss_title ?? 'Custom Request' }}
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
                                <span class="w-3 h-3 rounded-full {{ $headerVisual['dot'] }}"></span>
                                
                                @if($isRestricted)
                                    Account Restricted
                                @else
                                    {{ str_replace('_', ' ', $serviceRequest->hsr_status) }}
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
                                            {{ ucfirst($selectedPackageLabel) }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">From</label>
                                        <p class="text-lg font-bold text-green-600 mt-1">
                                            @if ($serviceRequest->hsr_offered_price)
                                                RM {{ number_format($serviceRequest->hsr_offered_price, 2) }}
                                            @else
                                                <span class="text-gray-400 italic">Not specified</span>
                                            @endif
                                        </p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Requested Dates</label>
                                        <div class="flex flex-col gap-1 mt-1 text-gray-800 font-medium">
                                            @forelse ($requestedDateDisplays as $dateDisplay)
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $dateDisplay }}
                                                </div>
                                            @empty
                                                <span class="text-gray-400 italic">No dates selected</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    @if ($serviceRequest->hsr_status === 'rejected' && $serviceRequest->hsr_rejection_reason)
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
                                                        <p class="text-sm mt-1">"{{ $serviceRequest->hsr_rejection_reason }}"</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($serviceRequest->hsr_message)
                                        <div class="sm:col-span-2">
                                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Message from Buyer</label>
                                            <div class="mt-2 p-4 bg-white rounded-lg border border-gray-200 text-gray-600">
                                                {{ $serviceRequest->hsr_message }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if ($serviceRequest->hsr_status == 'completed')
                                        <div class="sm:col-span-2 mt-4">
                                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Job Timeline</label>
                                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                                                    <span class="block text-xs text-blue-400 uppercase font-bold">Accepted</span>
                                                    <span class="font-medium text-gray-700 text-sm">
                                                        {{ $serviceRequest->hsr_accepted_at ? $serviceRequest->hsr_accepted_at->format('d M, h:i A') : '-' }}
                                                    </span>
                                                </div>
                                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                                                    <span class="block text-xs text-yellow-500 uppercase font-bold">Started Work</span>
                                                    <span class="font-medium text-gray-700 text-sm">
                                                        {{ $serviceRequest->hsr_started_at ? $serviceRequest->hsr_started_at->format('d M, h:i A') : '-' }}
                                                    </span>
                                                </div>
                                                <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                                                    <span class="block text-xs text-green-500 uppercase font-bold">Finished</span>
                                                    <span class="font-medium text-gray-700 text-sm">
                                                        {{ $serviceRequest->hsr_finished_at ? $serviceRequest->hsr_finished_at->format('d M, h:i A') : '-' }}
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

                                    @if($providerRestricted)
                                        {{-- BANNED: Non-clickable Div --}}
                                        <div class="block bg-gray-50 rounded-xl border border-gray-200 p-5 flex gap-4 items-start opacity-75 cursor-not-allowed relative">
                                            {{-- "Unavailable" Badge --}}
                                            <div class="absolute top-3 right-3 bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                                                Unavailable
                                            </div>

                                            <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 grayscale">
                                                @if ($serviceImageUrl)
                                                    <img src="{{ $serviceImageUrl }}" data-fallback-src="{{ $serviceImageFallback }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" /></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-500">{{ $service->hss_title }}</h4>
                                                <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600">
                                                    {{ optional($service->category)->hc_name }}
                                                </span>
                                                <p class="text-sm text-gray-400 mt-2 line-clamp-2">{{ strip_tags($service->hss_description) }}</p>
                                            </div>
                                        </div>
                                    @else
                                        {{-- ACTIVE: Clickable Link --}}
                                        <a href="{{ route('services.details', $service->hss_id) }}" class="block bg-white rounded-xl border border-gray-200 p-5 flex gap-4 items-start hover:shadow-md transition-all hover:border-indigo-300 group">
                                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                @if ($serviceImageUrl)
                                                    <img src="{{ $serviceImageUrl }}" data-fallback-src="{{ $serviceImageFallback }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" /></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $service->hss_title }}</h4>
                                                <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700">
                                                    {{ optional($service->category)->hc_name }}
                                                </span>
                                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ strip_tags($service->hss_description) }}</p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            @if ($serviceRequest->hsr_payment_proof)
                                <div class="mt-8">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Payment Information
                                    </h3>
                                    
                                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Payment Proof</span>
                                            <a href="{{ $paymentProofUrl }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center gap-1 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                Open Original
                                            </a>
                                        </div>
                                        
                                        <div class="rounded-lg overflow-hidden border border-gray-100 bg-gray-50 flex justify-center p-4">
                                            @if ($paymentProofIsPdf)
                                                <div class="flex flex-col items-center justify-center py-6">
                                                    <svg class="w-16 h-16 text-red-500 opacity-80 mb-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M11.362 2c4.156 0 2.638 6 2.638 6s6-1.65 6 2.457v11.543h-16v-20h7.362zm.827 7.5c-.328-.242-.361-.252-.408-.252-.161 0-.319.497-.319.497l-.234.698s.709-.54 1.144-.725c.168-.071.145-.097-.183-.218zm-2.062 1.488c-.979.622-1.895 1.581-2.072 1.932-.15.297-.478.752-.519 1-.038.225.267.382.435.334.301-.087 1.258-.598 2.156-3.266zm1.196 6.512c.706 0 1.272-.647 1.272-1.446 0-.66-.465-1.195-1.042-1.195-.576 0-1.041.535-1.041 1.195 0 .799.565 1.446 1.271 1.446h-1.46c-1.334-.067-2.618-.46-3.805-1.056l3.364-1.229c.142-.045.242-.1.319-.166.39-.334.618-1.571.618-2.678 0-1.554.025-2.738.04-3.551.011-.643.08-1.282.203-1.91h-5.462v18h14v-11.543c0-3.136-2.522-3.136-2.522-3.136s-.795 3.197-2.428 5.765zm2.844-3c-.928-1.149-1.341-1.693-1.802-1.693-1.173 0-1.246 1.638-1.096 2.413.085.441.258 1.139 1.096 1.139 1.19 0 1.838-.942 1.802-1.859zm-10.222 13h10v-2h-10v2zm0-4h14v-2h-14v2z"/></svg>
                                                    <p class="text-sm font-semibold text-gray-700">PDF Document Uploaded</p>
                                                    <a href="{{ $paymentProofUrl }}" target="_blank" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">View PDF</a>
                                                </div>
                                            @else
                                                <a href="{{ $paymentProofUrl }}" target="_blank" class="block">
                                                    <img src="{{ $paymentProofUrl }}" alt="Payment Proof" class="max-w-full h-auto max-h-96 object-contain rounded-md shadow-sm hover:opacity-95 transition-opacity">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-8">
                            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-6 border-b border-gray-100 pb-2">
                                    People Involved
                                </h3>

                                {{-- 1. Service Seller --}}
                                <div class="mb-6">
                                    <span class="text-xs font-semibold text-indigo-500 mb-2 block">Service Seller</span>
                                    @if($providerRestricted)
                                        <div class="flex items-center gap-3 p-2 -ml-2 rounded-lg bg-gray-50 border border-gray-100 opacity-75 cursor-not-allowed">
                                            <div class="relative">
                                                <img src="{{ $serviceRequest->provider->hu_profile_photo_path ? asset($serviceRequest->provider->hu_profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceRequest->provider->hu_name) }}" class="w-10 h-10 rounded-full border border-gray-200 grayscale">
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-500 line-through">{{ $serviceRequest->provider->hu_name }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 6.524a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>
                                                    Seller Suspended
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ route('students.profile', $serviceRequest->provider->hu_id) }}" class="flex items-center gap-3 p-2 -ml-2 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <img src="{{ $serviceRequest->provider->hu_profile_photo_path ? asset($serviceRequest->provider->hu_profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceRequest->provider->hu_name) }}" class="w-10 h-10 rounded-full border border-gray-200 group-hover:border-indigo-200">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $serviceRequest->provider->hu_name }} <span class="text-gray-300 text-[10px] ml-1">↗</span></p>
                                                <div class="flex items-center text-xs text-yellow-500">
                                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    <span class="ml-1 text-gray-600">{{ number_format($providerAverageRating, 1) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>

                                {{-- 2. Requester (Buyer) --}}
                                <div>
                                    <span class="text-xs font-semibold text-blue-500 mb-2 block">Buyer</span>
                                    @if($buyerRestricted)
                                        <div class="flex items-center gap-3 p-2 -ml-2 rounded-lg bg-gray-50 border border-gray-100 opacity-75 cursor-not-allowed">
                                            <div class="relative">
                                                <img src="{{ $serviceRequest->requester->hu_profile_photo_path ? asset($serviceRequest->requester->hu_profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceRequest->requester->hu_name) }}" class="w-10 h-10 rounded-full border border-gray-200 grayscale">
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-500 line-through">{{ $serviceRequest->requester->hu_name }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 6.524a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>
                                                    User Suspended
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ route('profile.public', $serviceRequest->requester->hu_id) }}" class="flex items-center gap-3 p-2 -ml-2 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <img src="{{ $serviceRequest->requester->hu_profile_photo_path ? asset($serviceRequest->requester->hu_profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($serviceRequest->requester->hu_name) }}" class="w-10 h-10 rounded-full border border-gray-200 group-hover:border-blue-200">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $serviceRequest->requester->hu_name }} <span class="text-gray-300 text-[10px] ml-1">↗</span></p>
                                                <div class="flex items-center text-xs text-yellow-500">
                                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    <span class="ml-1 text-gray-600">{{ number_format($buyerAverageRating, 1) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-4">Available Actions</h3>
                                <div class="space-y-3">
                                    @if($isRestricted)
                                        <div class="p-3 bg-red-50 text-red-700 text-sm font-medium rounded-lg border border-red-100 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                            Actions unavailable because one account is restricted.
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
                                            <button type="button" data-request-action="accept" data-request-id="{{ $serviceRequest->hsr_id }}" class="w-full py-2.5 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 shadow-sm transition">Accept Request</button>
                                            <button type="button" data-request-action="reject" data-request-id="{{ $serviceRequest->hsr_id }}" class="w-full py-2.5 bg-white border border-red-200 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition">Reject Request</button>
                                        @endif

                                        @if ($isProvider && $serviceRequest->isAccepted())
                                            <button type="button" data-request-action="in-progress" data-request-id="{{ $serviceRequest->hsr_id }}" class="w-full py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 shadow-sm transition">Start Work</button>
                                        @endif

                                        @if ($isProvider && $serviceRequest->isInProgress())
                                            <button type="button" data-request-action="complete" data-request-id="{{ $serviceRequest->hsr_id }}" class="w-full py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 shadow-sm transition">Mark as Completed</button>
                                        @endif

                                        @if ($isRequester && $serviceRequest->hsr_status === 'pending')
                                            <button type="button" data-request-action="cancel" data-request-id="{{ $serviceRequest->hsr_id }}" class="w-full py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">Cancel Request</button>
                                        @elseif ($isRequester && ($serviceRequest->hsr_status === 'in_progress' || $serviceRequest->hsr_status === 'accepted'))
                                            <button disabled class="w-full py-2.5 bg-gray-100 border border-gray-200 text-gray-400 rounded-lg font-semibold cursor-not-allowed flex items-center justify-center gap-2"><i class="fa-solid fa-play"></i> Work Started</button>
                                        @endif

                                        @if ($isRequester && $serviceRequest->isCompleted() && !$hasCurrentUserReviewed)
                                            <button type="button" data-open-review="{{ $serviceRequest->hsr_id }}" class="w-full py-2.5 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 shadow-sm transition">Leave a Review</button>
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
                                                    {{ substr($review->reviewer->hu_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-sm text-gray-900">{{ $review->reviewer->hu_name }}</p>
                                                    <div class="flex text-yellow-400 text-xs">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <span>{{ $i <= $review->hr_rating ? '★' : '☆' }}</span>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ optional($review->hr_created_at)->diffForHumans() ?? 'Recently' }}</span>
                                        </div>
                                        @if ($review->hr_comment)
                                            <p class="mt-3 text-gray-600 text-sm leading-relaxed">{{ $review->hr_comment }}</p>
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
                <button type="button" data-close-review class="text-white/80 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6">
                <form id="reviewForm" class="space-y-6">
                    @csrf
                    <input type="hidden" id="reviewServiceRequestId" name="service_request_id">
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-2">How would you rate this service?</label>
                        <div class="flex justify-center gap-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" data-set-rating="{{ $i }}" class="star-button text-4xl text-gray-300 hover:text-yellow-400 transition-colors focus:outline-none">★</button>
                            @endfor
                        </div>
                        <input type="hidden" id="rating" name="rating" required>
                    </div>
                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Your Review (Optional)</label>
                        <textarea id="comment" name="comment" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none" placeholder="Tell us what you liked..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" data-close-review class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="serviceRequestsShowConfig"
        data-review-store-url="{{ route('reviews.store') }}"
        data-request-action-url-template="{{ url('/service-requests/__ID__/__ACTION__') }}"></div>
    @push('scripts')
        <script src="{{ asset('js/nonadmin-service-requests-show.js') }}"></script>
    @endpush
</x-app-layout>
