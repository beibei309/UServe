@extends('layouts.helper')

@section('content')

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Service Requests (Helper View)') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div id="received-content" class="sr-tab-content">
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-b from-white to-slate-50/50 shadow-sm">
                    <div class="p-6 text-gray-800">
                         <h3 class="font-medium mb-4 700" style="font-size: 25px;">My Services Orders ({{ $receivedRequests->count() }} total)</h3>

                        <form method="GET" action="{{ url()->current() }}" class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                                {{-- 1. Search Bar (Takes up 4 columns) --}}
                                <div class="md:col-span-4">
                                    <label for="search" class="sr-only">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                                        </div>
                                        <input type="text" name="search" id="request-search"
                                            value="{{ request('search') }}" placeholder="Search requests..."
                                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-custom-teal focus:border-custom-teal text-sm">
                                    </div>
                                </div>

                                {{-- 2. Filter by Category (Takes up 3 columns) --}}
                                <div class="md:col-span-3">
                                    <select name="category" data-auto-submit-filter
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-custom-teal focus:border-custom-teal text-sm text-gray-700 bg-white">
                                        <option value="">-- All Categories --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->hc_id }}"
                                                {{ request('category') == $category->hc_id ? 'selected' : '' }}>
                                                {{ $category->hc_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- 3. Filter by Service Type (Takes up 3 columns) --}}
                                <div class="md:col-span-3">
                                    <select name="service_type" data-auto-submit-filter
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-custom-teal focus:border-custom-teal text-sm text-gray-700 bg-white">
                                        <option value="">-- All My Services --</option>
                                        {{-- Assuming you pass a variable $serviceTypes from controller --}}
                                        @foreach ($serviceTypes as $type)
                                            <option value="{{ $type->hss_id }}"
                                                {{ request('service_type') == $type->hss_id ? 'selected' : '' }}>
                                                {{ $type->hss_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- 4. Filter by Status (Replaces Sort) (Takes up 2 columns) --}}
                                <div class="md:col-span-2">
                                    <select name="status" data-auto-submit-filter
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-custom-teal focus:border-custom-teal text-sm text-gray-700 bg-white">
                                        <option value="">-- Status --</option>
                                        <option value="waiting_payment"
                                            {{ request('status') == 'waiting_payment' ? 'selected' : '' }}>Waiting Payment
                                        </option>
                                        <option value="disputed" {{ request('status') == 'disputed' ? 'selected' : '' }}>
                                            Disputed</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                    </select>
                                </div>

                                {{-- Optional: Reset Button (Only shows if filters are active) --}}
                                @if (request()->hasAny(['search', 'category', 'service_type', 'sort']))
                                    <div class="md:col-span-12 flex justify-end">
                                        <a href="{{ url()->current() }}"
                                            class="text-xs text-red-500 hover:text-red-700 font-medium underline">
                                            Clear Filters
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </form>

                        <div class="mb-6">
                            <div class="flex space-x-4 border-b border-gray-200">
                                <button type="button" data-status-tab="pending" id="pending-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'pending' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    Pending
                                </button>
                                <button type="button" data-status-tab="in-progress" id="in-progress-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'in-progress' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    In Progress
                                </button>
                                <button type="button" data-status-tab="completed" id="completed-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'completed' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    Completed
                                </button>
                            </div>
                        </div>

                       <div id="pending-content" class="sr-status-tab-content {{ $defaultStatusTab === 'pending' ? '' : 'hidden' }}">
    @if ($receivedRequests->where('hsr_status', 'pending')->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center bg-white rounded-xl border border-dashed border-gray-300">
            <div class="rounded-full bg-indigo-50 p-4 mb-4">
                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No Pending Requests</h3>
            <p class="mt-2 text-sm text-gray-500">Good job! You've processed all your incoming requests.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($receivedRequests->where('hsr_status', 'pending') as $request)
                <div class="sr-request-item group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-md hover:border-indigo-300"
                    data-category="{{ optional(optional($request->studentService)->category)->hc_name ?? 'Other' }}">
                    
                    <div class="absolute top-0 left-0 right-0 h-1 bg-indigo-500"></div>

                    <div class="p-5 sm:p-6">
                        
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-bold text-indigo-700">
                                        NEW REQUEST
                                    </span>
                                    <span class="text-xs text-gray-400">#{{ $request->hsr_id }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ optional($request->studentService)->hss_title ?? 'Custom Request' }}
                                </h4>
                                @if(optional(optional($request->studentService)->category))
                      <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1" style="color:{{ $request->studentService->category->hc_color }}; background-color: {{ $request->studentService->category->hc_color }}10; border: 1px solid {{ $request->studentService->category->hc_color }};">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span class="text-xs font-medium">
                                        {{ $request->studentService->category->hc_name }}
                                    </span>
                                </div>
                        @endif
                                <p class="text-xs text-gray-400 mt-1">
                                    Received {{ $request->ui_created_human }}
                                </p>
                            </div>
                            
                            <div class="text-left sm:text-right mt-2 sm:mt-0">
                                @if ($request->hsr_offered_price)
                                    <div class="text-2xl font-bold text-gray-900">
                                        RM {{ number_format($request->hsr_offered_price, 2) }}
                                    </div>
                                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                        {{ $request->ui_package_label }} Package
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="h-px w-full bg-gray-100 my-4"></div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                            
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Requester</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $request->requester->hu_name }}</p>
                                    <a href="https://wa.me/6{{ $request->requester->hu_phone }}" target="_blank" class="text-xs text-green-600 hover:text-green-700 font-medium inline-flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        Chat now
                                    </a>
                                </div>
                            </div>

                            @if ($request->ui_first_date_display)
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Requested Date</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $request->ui_first_date_display }}
                                        @if ($request->ui_date_count > 1)
                                            <span class="text-xs font-normal text-gray-500 ml-1">(+{{ $request->ui_date_count - 1 }} days)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif

                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Requirements</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $request->ui_pkg_duration ? $request->ui_pkg_duration . ' Hrs' : 'N/A' }} 
                                        <span class="text-gray-300 mx-1">|</span> 
                                        {{ $request->ui_pkg_frequency ? ucfirst($request->ui_pkg_frequency) : 'One-time' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if ($request->hsr_message)
                            <div class="rounded-lg bg-gray-50 p-4 border border-gray-100 mb-6">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-1">Requester's Note</p>
                                <p class="text-sm text-gray-600 italic">"{{ $request->hsr_message }}"</p>
                            </div>
                        @endif

                        <div class="flex flex-col-reverse md:flex-row items-center justify-between gap-4 pt-2">
                            
                            <a href="{{ route('service-requests.show', $request) }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors w-full md:w-auto text-center">
                                View Full Details
                            </a>

                            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                                
                                <button type="button" data-open-reject="{{ $request->hsr_id }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject
                                </button>
                                <form id="reject-form-{{ $request->hsr_id }}" action="{{ route('service-requests.reject', $request->hsr_id) }}" method="POST" class="hidden">@csrf</form>

                                <button type="button" data-accept-request="{{ $request->hsr_id }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 hover:shadow-md transition-all focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Accept Request
                                </button>
                                <form id="accept-form-{{ $request->hsr_id }}" action="{{ route('service-requests.accept', $request->hsr_id) }}" method="POST" class="hidden">@csrf</form>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

                        <div id="in-progress-content" class="sr-status-tab-content {{ $defaultStatusTab === 'in-progress' ? '' : 'hidden' }}">
    @if ($receivedRequests->whereIn('hsr_status', ['accepted', 'in_progress', 'waiting_payment', 'disputed'])->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center bg-white rounded-xl border border-dashed border-gray-300">
            <div class="rounded-full bg-blue-50 p-4 mb-4">
                <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No Ongoing Requests</h3>
            <p class="mt-2 text-sm text-gray-500">You don't have any active jobs right now.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($receivedRequests->whereIn('hsr_status', ['accepted', 'in_progress', 'waiting_payment', 'disputed']) as $request)
                <div class="sr-request-item group relative overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-300 hover:shadow-md {{ $request->ui_helper_inprogress_theme['border'] }}"
                    data-category="{{ optional(optional($request->studentService)->category)->hc_name ?? 'Other' }}">
                    
                    <div class="absolute top-0 left-0 right-0 h-1 {{ $request->ui_helper_inprogress_theme['bg'] }}"></div>

                    <div class="p-5 sm:p-6">
                        
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide bg-{{ $request->ui_helper_inprogress_theme['color'] }}-50 text-{{ $request->ui_helper_inprogress_theme['color'] }}-700 border border-{{ $request->ui_helper_inprogress_theme['color'] }}-100">
                                        {{ $request->formatted_status }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-mono">#{{ $request->ui_display_id }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 leading-tight group-hover:text-indigo-600 transition-colors">
                                    {{ optional($request->studentService)->hss_title ?? 'Custom Request' }}
                                </h4>
                                @if(optional(optional($request->studentService)->category))
                      <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1" style="color:{{ $request->studentService->category->hc_color }}; background-color: {{ $request->studentService->category->hc_color }}10; border: 1px solid {{ $request->studentService->category->hc_color }};">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span class="text-xs font-medium">
                                        {{ $request->studentService->category->hc_name }}
                                    </span>
                                </div>
                        @endif
                            </div>
                            
                            @if ($request->hsr_offered_price)
                                <div class="text-left sm:text-right">
                                     <span class="text-xs text-gray-500 uppercase tracking-wide">Estimated</span>
                                    <span class="block text-2xl font-bold text-gray-900">
                                        RM {{ number_format($request->hsr_offered_price, 2) }}
                                    </span>
                                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                          {{ $request->ui_package_label }} Package
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-6 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                                <span class="font-medium">{{ $request->requester->hu_name }}</span>
                            </div>
                            <span class="text-gray-300">|</span>
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span>{{ $request->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        {{-- ================================================= --}}
                        {{--  ACTION ZONES                                     --}}
                        {{-- ================================================= --}}

                        <div class="space-y-4">
                            
                            {{-- 1. WAITING FOR PAYMENT --}}
                            @if ($request->hsr_status === 'waiting_payment')
                                
                                {{-- Case A: Verification Needed --}}
                                @if ($request->hsr_payment_status === 'verification_status')
                                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-center gap-2 text-blue-800">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                <span class="font-bold text-sm">Payment Proof Uploaded</span>
                                            </div>

                                            {{-- Button: Normal Width --}}
<<<<<<< HEAD
<<<<<<< HEAD
                                            <button type="button" data-open-proof="{{ $request->hsr_id }}" data-proof-url="{{ asset('storage/' . $request->hsr_payment_proof) }}"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition-all">
                                                Check Proof
                                            </button>
=======
=======
>>>>>>> develop
                                            @if ($request->ui_has_payment_proof)
                                                <button type="button" data-open-proof="{{ $request->hsr_id }}" data-proof-url="{{ $request->ui_payment_proof_url }}"
                                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition-all">
                                                    Check Proof
                                                </button>
                                            @else
                                                <span class="text-xs font-semibold text-red-600">Proof file is missing</span>
                                            @endif
<<<<<<< HEAD
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
>>>>>>> develop
                                        </div>
                                    </div>

                                {{-- Case B: Waiting for buyer --}}
                                @else
                                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 flex flex-col md:flex-row items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <p class="text-sm font-bold text-yellow-800">Waiting for Payment</p>
                                        </div>
                                        
                                        {{-- Button: Normal Width --}}
                                        <button type="button" data-finalize-order="{{ $request->hsr_id }}" data-outcome="paid"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-yellow-300 bg-white text-xs font-bold text-yellow-700 hover:bg-yellow-50 transition-all">
                                            Mark Paid Manually
                                        </button>
                                        <form id="finalize-form-{{ $request->hsr_id }}" action="{{ route('service-requests.finalize', $request->hsr_id) }}" method="POST" class="hidden">@csrf<input type="hidden" name="outcome" id="finalize-outcome-{{ $request->hsr_id }}"></form>
                                    </div>
                                @endif

                            {{-- 2. DISPUTE ACTIVE --}}
                            @elseif ($request->hsr_status === 'disputed')
                                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                                    <div class="flex items-start gap-3 mb-3">
                                        <svg class="h-5 w-5 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <div>
                                            <h5 class="text-sm font-bold text-red-800">Order Disputed</h5>
                                            <p class="text-xs text-red-600 mt-1">Reason: "{{ $request->hsr_dispute_reason ?? 'Admin review required' }}"</p>
                                        </div>
                                    </div>
                                    <form id="cancel-dispute-form-{{ $request->hsr_id }}" action="{{ route('service-requests.cancel-dispute', $request->hsr_id) }}" method="POST">
                                        @csrf
                                        {{-- Button: Normal Width --}}
                                        <button type="button" data-cancel-dispute="{{ $request->hsr_id }}"
                                            class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-white border border-red-200 text-xs font-bold text-red-600 hover:bg-red-50 transition-all shadow-sm">
                                            Resolve & Complete
                                        </button>
                                    </form>
                                </div>

                            {{-- 3. ACCEPTED (Start Work) --}}
                            @elseif ($request->hsr_status === 'accepted')
                                {{-- Button: Normal Width --}}
                                <button type="button" data-mark-progress="{{ $request->hsr_id }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 transition-all">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Start Work
                                </button>
                                <form id="progress-form-{{ $request->hsr_id }}" action="{{ route('service-requests.mark-in-progress', $request->hsr_id) }}" method="POST" class="hidden">@csrf</form>

                            {{-- 4. IN PROGRESS (Finish Work) --}}
                            @elseif ($request->hsr_status === 'in_progress')
                                {{-- Button: Normal Width --}}
                                <button type="button" data-mark-finished="{{ $request->hsr_id }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-700 transition-all">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Finish Work
                                </button>
                                <form id="finish-work-form-{{ $request->hsr_id }}" action="{{ route('service-requests.mark-work-finished', $request->hsr_id) }}" method="POST" class="hidden">@csrf</form>
                            @endif

                            {{-- Secondary Actions Row (Short Buttons) --}}
                            <div class="flex items-center gap-2 pt-2">
                                {{-- Short Button: WhatsApp --}}
                                <a href="https://wa.me/6{{ $request->requester->hu_phone }}" target="_blank" 
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs font-bold text-green-700 hover:bg-green-100 transition-colors">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" /></svg>
                                    WhatsApp
                                </a>
                                
                                {{-- Short Button: Details --}}
                                <a href="{{ route('service-requests.show', $request) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                                    Details
                                </a>

                                {{-- Short Square Button: Report (Only if needed) --}}
                                @if ($request->isWorkFinished() && $request->hsr_status !== 'disputed')
                                    <button type="button" data-open-dispute="{{ $request->hsr_id }}" class="h-[34px] w-[34px] flex items-center justify-center rounded-lg border border-red-200 bg-white text-red-500 hover:bg-red-50 transition-colors" title="Report Issue">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    </button>
                                    <form id="dispute-form-{{ $request->hsr_id }}" action="{{ route('service-requests.report', $request->hsr_id) }}" method="POST" class="hidden">@csrf<input type="hidden" name="reason" id="dispute-reason-{{ $request->hsr_id }}"></form>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

                       <div id="completed-content" class="sr-status-tab-content {{ $defaultStatusTab === 'completed' ? '' : 'hidden' }}">
    @if ($receivedRequests->whereIn('hsr_status', ['completed', 'cancelled', 'rejected'])->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center bg-white rounded-xl border border-dashed border-gray-300">
            <div class="rounded-full bg-gray-50 p-4 mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No Completed Requests Yet</h3>
            <p class="mt-2 text-sm text-gray-500">Completed and cancelled jobs will appear here.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($receivedRequests->whereIn('hsr_status', ['completed', 'cancelled', 'rejected']) as $request)
                <div class="sr-request-item group relative overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-300 hover:shadow-md {{ $request->ui_helper_completed_theme['border'] }}"
                    data-category="{{ optional(optional($request->studentService)->category)->hc_name ?? 'Other' }}">
                    
                    <div class="absolute top-0 left-0 right-0 h-1 {{ $request->ui_helper_completed_theme['strip'] }}"></div>

                    <div class="p-5 sm:p-6">
                        
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $request->ui_helper_completed_theme['badge'] }}">
                                        {{ strtoupper($request->hsr_status) }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-mono">#{{ $request->ui_display_id }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ optional($request->studentService)->hss_title ?? 'Custom Request' }}
                                </h4>
                                @if(optional(optional($request->studentService)->category))
                      <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1" style="color:{{ $request->studentService->category->hc_color }}; background-color: {{ $request->studentService->category->hc_color }}10; border: 1px solid {{ $request->studentService->category->hc_color }};">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span class="text-xs font-medium">
                                        {{ $request->studentService->category->hc_name }}
                                    </span>
                                </div>
                        @endif
                            </div>
                            
                            @if ($request->hsr_offered_price)
                                <div class="text-left sm:text-right">
                                                                         <span class="text-xs text-gray-500 uppercase tracking-wide">Estimated</span>

                                    <span class="block text-2xl font-bold text-gray-900">
                                        RM {{ number_format($request->hsr_offered_price, 2) }}
                                    </span>
                                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                          {{ $request->ui_package_label }} Package
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="border-gray-100 my-4">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Buyer</p>
                                    <p class="font-semibold text-gray-900">{{ $request->requester->hu_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            @if ($request->hsr_status === 'completed' && $request->hsr_started_at && $request->hsr_completed_at)
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Work Log</p>
                                        <div class="flex gap-4 text-sm mt-0.5">
                                            <div>
                                                <span class="text-xs text-gray-500 block">Start</span>
                                                <span class="font-mono font-semibold">{{ $request->hsr_started_at->format('H:i') }}</span>
                                            </div>
                                            <div class="border-l border-gray-200"></div>
                                            <div>
                                                <span class="text-xs text-gray-500 block">End</span>
                                                <span class="font-mono font-semibold">{{ $request->hsr_completed_at->format('H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2 border-t border-gray-50 mt-4">
                            
                            <a href="{{ route('service-requests.show', $request) }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors flex items-center gap-1">
                                View Full Details
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>

                            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                
                                {{-- 1. Buyer's Review (Incoming) --}}
<<<<<<< HEAD
<<<<<<< HEAD
                                @if ($request->reviewForHelper)
                                    <button type="button" data-open-buyer-review='@json($request->reviewForHelper)' data-reviewer-name="{{ $request->requester->hu_name }}"
=======
                                @if ($request->ui_review_for_helper)
                                    <button type="button" data-open-buyer-review='@json($request->ui_review_for_helper)' data-reviewer-name="{{ $request->requester->hu_name }}"
>>>>>>> develop
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 transition-all">
                                        <div class="flex gap-0.5 text-yellow-500">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $request->ui_review_for_helper->hr_rating ? 'fas' : 'far' }} fa-star text-xs"></i>
                                            @endfor
                                        </div>
<<<<<<< HEAD
                                        <span>{{ $request->reviewForHelper->hr_reply ? 'See Reply' : 'Reply' }}</span>
=======
                                @if ($request->ui_review_for_helper)
                                    <button type="button" data-open-buyer-review='@json($request->ui_review_for_helper)' data-reviewer-name="{{ $request->requester->hu_name }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 transition-all">
                                        <div class="flex gap-0.5 text-yellow-500">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $request->ui_review_for_helper->hr_rating ? 'fas' : 'far' }} fa-star text-xs"></i>
                                            @endfor
                                        </div>
                                        <span>{{ $request->ui_review_for_helper->hr_reply ? 'See Reply' : 'Reply' }}</span>
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
                                        <span>{{ $request->ui_review_for_helper->hr_reply ? 'See Reply' : 'Reply' }}</span>
>>>>>>> develop
                                    </button>
                                @elseif($request->hsr_status === 'completed')
                                    <span class="text-xs text-gray-400 italic py-2">Waiting for buyer review...</span>
                                @endif

                                {{-- 2. Seller's Review (Outgoing) --}}
                                @if ($request->ui_reviewed_by_auth)
                                    <div class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium rounded-lg bg-green-50 text-green-700 border border-green-200">
                                        <i class="fas fa-check"></i> You rated buyer
                                    </div>
                                @elseif($request->hsr_status === 'completed')
                                    <button type="button" data-open-seller-review="{{ $request->hsr_id }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all hover:shadow-md">
                                        <i class="fas fa-star"></i> Rate Buyer
                                    </button>
                                @endif
                            </div>

                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

                        {{-- MODAL --}}

                        {{-- Payment proof modal --}}
                        <div id="proofModal"
                            class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm flex items-center justify-center">
                            <div
                                class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full m-4 flex flex-col max-h-[90vh]">

                                {{-- Header --}}
                                <div class="flex justify-between items-center p-4 border-b">
                                    <h3 class="text-lg font-bold text-gray-900">Verify Payment Proof</h3>
                                    <button type="button" data-close-proof
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Body: The Viewer --}}
                                <div class="flex-1 p-4 bg-gray-100 flex justify-center items-center overflow-auto">

                                    {{-- 1. Image Viewer --}}
                                    <img id="proofImage" src="" alt="Payment Proof"
                                        class="max-h-[60vh] w-auto rounded shadow-sm border border-gray-200 hidden object-contain">

                                    {{-- 2. PDF Viewer (Iframe) --}}
                                    <iframe id="proofPdf" src=""
                                        class="w-full h-[60vh] rounded shadow-sm border border-gray-200 hidden">
                                    </iframe>

                                    {{-- 3. Fallback / Error --}}
                                    <div id="proofFallback" class="hidden text-center p-6">
                                        <div class="mb-3 text-red-500">
                                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-900 font-medium">Unable to preview file.</p>
                                        <a id="proofLink" href="#" target="_blank"
                                            class="mt-3 inline-block text-blue-600 underline text-sm hover:text-blue-800">
                                            Download File to View
                                        </a>
                                    </div>
                                </div>

                                {{-- Footer: Decisions --}}
                                <div class="p-4 border-t bg-gray-50 rounded-b-xl flex gap-3 shrink-0">
                                    <button type="button" data-submit-decision="unpaid_problem"
                                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Reject / Report
                                    </button>

                                    <button type="button" data-submit-decision="paid"
                                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 shadow-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Confirm Payment
                                    </button>
                                </div>

                                {{-- Hidden Form --}}
                                <form id="finalizeOrderForm" method="POST" class="hidden">
                                    @csrf
                                    <input type="hidden" name="outcome" id="finalizeOutcome">
                                </form>
                            </div>
                        </div>
                        {{-- Review modal --}}
                        <div id="reviewModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog"
                            aria-modal="true">
                            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

                            <div class="fixed inset-0 z-10 overflow-y-auto">
                                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                    <div
                                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                                        {{-- Modal Header --}}
                                        <div class="bg-indigo-600 px-4 py-4 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-base font-semibold leading-6 text-white" id="modal-title">
                                                    Buyer Review</h3>
                                                <button type="button" data-close-review
                                                    class="text-indigo-200 hover:text-white">
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            {{-- Buyer Review Box --}}
                                            <div class="rounded-xl bg-yellow-50 p-4 border border-yellow-100 mb-6">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 text-sm"
                                                            id="modalRequesterName">Buyer Name</h4>
                                                        <p class="text-xs text-gray-500" id="modalDate">Date</p>
                                                    </div>
                                                    <div class="text-yellow-400 text-sm flex gap-1" id="modalStars"></div>
                                                </div>
                                                <div class="relative mt-2">
                                                    <span
                                                        class="absolute top-0 left-0 text-yellow-200 text-4xl -translate-y-2 -translate-x-2">"</span>
                                                    <p class="relative text-sm text-gray-700 italic px-2 z-10"
                                                        id="modalComment"></p>
                                                </div>
                                            </div>

                                            {{-- Reply Section --}}
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-indigo-500" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                    Your Response
                                                </h4>

                                                {{-- State A: Already Replied --}}
                                                <div id="viewReplyContainer" class="hidden">
                                                    <div
                                                        class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm text-gray-700 relative">
                                                        <p id="modalReplyText"></p>
                                                        <div class="mt-2 text-right">
                                                            <span class="text-xs text-gray-400">Replied on <span
                                                                    id="modalRepliedAt"></span></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- State B: Form --}}
                                                <form id="replyForm" method="POST" action="" class="hidden">
                                                    @csrf
                                                    <div class="relative">
                                                        <textarea name="reply" rows="4"
                                                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3"
                                                            placeholder="Thank the Buyer for their feedback..."></textarea>
                                                    </div>
                                                    <div class="mt-4 flex justify-end">
                                                        <button type="submit"
                                                            class="inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                            Post Reply
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="sellerReviewModal" class="relative z-50 hidden" aria-labelledby="modal-title"
                            role="dialog" aria-modal="true">
                            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

                            <div class="fixed inset-0 z-10 overflow-y-auto">
                                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                    <div
                                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                                        {{-- Modal Header --}}
                                        <div class="bg-indigo-600 px-4 py-4 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-base font-semibold leading-6 text-white">
                                                    Rate This Buyer
                                                </h3>
                                                <button type="button" data-close-seller-review
                                                    class="text-indigo-200 hover:text-white focus:outline-none">
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Modal Body --}}
                                        <div class="px-4 pt-5 pb-4 sm:p-6">
                                            <form id="sellerReviewForm">
                                                <input type="hidden" name="service_request_id"
                                                    id="sellerReviewRequestId">
                                                <input type="hidden" name="rating" id="sellerReviewRating">

                                                {{-- Star Rating Input --}}
                                                <div class="mb-6 text-center">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">How was
                                                        your experience?</label>
                                                    <div class="flex justify-center gap-2 text-2xl cursor-pointer">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="far fa-star text-gray-300 hover:text-yellow-400 transition-colors seller-star-input"
                                                                data-value="{{ $i }}" data-set-seller-rating="{{ $i }}"></i>
                                                        @endfor
                                                    </div>
                                                    <p class="text-xs text-red-500 mt-1 hidden" id="ratingError">Please
                                                        select a rating.</p>
                                                </div>

                                                {{-- Comment Input --}}
                                                <div class="mb-4">
                                                    <label for="sellerComment"
                                                        class="block text-sm font-medium text-gray-700 mb-1">Comment
                                                        (Optional)</label>
                                                    <textarea id="sellerComment" name="comment" rows="4"
                                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm placeholder-gray-400"
                                                        placeholder="Describe your experience working with this Buyer..."></textarea>
                                                </div>

                                                {{-- Actions --}}
                                                <div
                                                    class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                                    <button type="submit"
                                                        class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2">
                                                        Submit Review
                                                    </button>
                                                    <button type="button" data-close-seller-review
                                                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="serviceRequestsHelperConfig"
        data-default-status-tab="{{ $defaultStatusTab }}"
        data-finalize-url-template="{{ url('/service-requests/__ID__/finalize') }}"
        data-reviews-store-url="{{ route('reviews.store') }}"
        data-reviews-reply-url-template="{{ url('/reviews/__ID__/reply') }}"></div>
    @push('scripts')
        <script src="{{ asset('js/nonadmin-service-requests-helper.js') }}"></script>
    @endpush
@endsection
