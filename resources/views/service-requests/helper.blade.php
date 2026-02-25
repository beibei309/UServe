@extends('layouts.helper')

@section('content')

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Service Requests (Helper View)') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @php
                // Default status tab is 'pending'
                $defaultStatusTab = request('tab', 'in-progress');
            @endphp

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
                                    <select name="category" onchange="this.form.submit()"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-custom-teal focus:border-custom-teal text-sm text-gray-700 bg-white">
                                        <option value="">-- All Categories --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- 3. Filter by Service Type (Takes up 3 columns) --}}
                                <div class="md:col-span-3">
                                    <select name="service_type" onchange="this.form.submit()"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-custom-teal focus:border-custom-teal text-sm text-gray-700 bg-white">
                                        <option value="">-- All My Services --</option>
                                        {{-- Assuming you pass a variable $serviceTypes from controller --}}
                                        @foreach ($serviceTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ request('service_type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- 4. Filter by Status (Replaces Sort) (Takes up 2 columns) --}}
                                <div class="md:col-span-2">
                                    <select name="status" onchange="this.form.submit()"
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
                                <button onclick="showStatusTab('pending')" id="pending-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'pending' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    Pending
                                </button>
                                <button onclick="showStatusTab('in-progress')" id="in-progress-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'in-progress' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    In Progress
                                </button>
                                <button onclick="showStatusTab('completed')" id="completed-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'completed' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    Completed
                                </button>
                            </div>
                        </div>

                       <div id="pending-content" class="sr-status-tab-content {{ $defaultStatusTab === 'pending' ? '' : 'hidden' }}">
    @if ($receivedRequests->where('status', 'pending')->isEmpty())
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
            @foreach ($receivedRequests->where('status', 'pending') as $request)
                @php
                    // Data Setup
                    $service = $request->studentService;
                    $pkgType = strtolower($request->selected_package ?? 'basic');
                    $pkgDescription = $service->{$pkgType . '_description'} ?? null;
                    $pkgDuration = $service->{$pkgType . '_duration'} ?? null;
                    $pkgFrequency = $service->{$pkgType . '_frequency'} ?? null;
                    
                    $dates = $request->selected_dates;
                    $firstDate = is_array($dates) ? $dates[0] : $dates;
                    $dateCount = is_array($dates) ? count($dates) : 1;
                @endphp

                <div class="sr-request-item group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-md hover:border-indigo-300"
                    data-category="{{ optional($service->category)->name ?? 'Other' }}">
                    
                    <div class="absolute top-0 left-0 right-0 h-1 bg-indigo-500"></div>

                    <div class="p-5 sm:p-6">
                        
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-bold text-indigo-700">
                                        NEW REQUEST
                                    </span>
                                    <span class="text-xs text-gray-400">#{{ $request->id }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ optional($request->studentService)->title ?? 'Custom Request' }}
                                </h4>
                                @if(optional($service)->category)
                      <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1" style="color:{{ $service->category->color }}; background-color: {{ $service->category->color }}10; border: 1px solid {{ $service->category->color }};">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span class="text-xs font-medium">
                                        {{ $service->category->name }}
                                    </span>
                                </div>
                        @endif
                                <p class="text-xs text-gray-400 mt-1">
                                    Received {{ $request->created_at->diffForHumans() }}
                                </p>
                            </div>
                            
                            <div class="text-left sm:text-right mt-2 sm:mt-0">
                                @if ($request->offered_price)
                                    <div class="text-2xl font-bold text-gray-900">
                                        RM {{ number_format($request->offered_price, 2) }}
                                    </div>
                                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                        {{ str_replace('"', '', $request->selected_package) ?? 'Custom' }} Package
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
                                    <p class="text-sm font-bold text-gray-900">{{ $request->requester->name }}</p>
                                    <a href="https://wa.me/6{{ $request->requester->phone }}" target="_blank" class="text-xs text-green-600 hover:text-green-700 font-medium inline-flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        Chat now
                                    </a>
                                </div>
                            </div>

                            @if ($request->selected_dates)
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase">Requested Date</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($firstDate)->format('M j, Y') }}
                                        @if ($dateCount > 1)
                                            <span class="text-xs font-normal text-gray-500 ml-1">(+{{ $dateCount - 1 }} days)</span>
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
                                        {{ $pkgDuration ? $pkgDuration . ' Hrs' : 'N/A' }} 
                                        <span class="text-gray-300 mx-1">|</span> 
                                        {{ $pkgFrequency ? ucfirst($pkgFrequency) : 'One-time' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if ($request->message)
                            <div class="rounded-lg bg-gray-50 p-4 border border-gray-100 mb-6">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-1">Requester's Note</p>
                                <p class="text-sm text-gray-600 italic">"{{ $request->message }}"</p>
                            </div>
                        @endif

                        <div class="flex flex-col-reverse md:flex-row items-center justify-between gap-4 pt-2">
                            
                            <a href="{{ route('service-requests.show', $request) }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors w-full md:w-auto text-center">
                                View Full Details
                            </a>

                            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                                
                                <button onclick="openRejectModal({{ $request->id }})" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject
                                </button>
                                <form id="reject-form-{{ $request->id }}" action="{{ route('service-requests.reject', $request->id) }}" method="POST" class="hidden">@csrf</form>

                                <button onclick="acceptRequest({{ $request->id }})" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 hover:shadow-md transition-all focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Accept Request
                                </button>
                                <form id="accept-form-{{ $request->id }}" action="{{ route('service-requests.accept', $request->id) }}" method="POST" class="hidden">@csrf</form>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

                        <div id="in-progress-content" class="sr-status-tab-content {{ $defaultStatusTab === 'in-progress' ? '' : 'hidden' }}">
    @if ($receivedRequests->whereIn('status', ['accepted', 'in_progress', 'waiting_payment', 'disputed'])->isEmpty())
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
            @foreach ($receivedRequests->whereIn('status', ['accepted', 'in_progress', 'waiting_payment', 'disputed']) as $request)
                @php
                    // 1. Data Setup
                    $service = $request->studentService;
                    $displayId = str_pad($request->id, 5, '0', STR_PAD_LEFT);
                    
                    // 2. Dynamic Styling based on Status
                    $statusTheme = match ($request->status) {
                        'disputed' => ['color' => 'red', 'border' => 'border-red-200', 'bg' => 'bg-red-500'],
                        'waiting_payment' => ['color' => 'yellow', 'border' => 'border-yellow-200', 'bg' => 'bg-yellow-400'],
                        'in_progress' => ['color' => 'blue', 'border' => 'border-blue-200', 'bg' => 'bg-blue-500'],
                        default => ['color' => 'gray', 'border' => 'border-gray-200', 'bg' => 'bg-gray-400'], // accepted
                    };
                @endphp

                <div class="sr-request-item group relative overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-300 hover:shadow-md {{ $statusTheme['border'] }}"
                    data-category="{{ optional($service->category)->name ?? 'Other' }}">
                    
                    <div class="absolute top-0 left-0 right-0 h-1 {{ $statusTheme['bg'] }}"></div>

                    <div class="p-5 sm:p-6">
                        
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide bg-{{ $statusTheme['color'] }}-50 text-{{ $statusTheme['color'] }}-700 border border-{{ $statusTheme['color'] }}-100">
                                        {{ $request->formatted_status }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-mono">#{{ $displayId }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 leading-tight group-hover:text-indigo-600 transition-colors">
                                    {{ optional($request->studentService)->title ?? 'Custom Request' }}
                                </h4>
                                @if(optional($service)->category)
                      <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1" style="color:{{ $service->category->color }}; background-color: {{ $service->category->color }}10; border: 1px solid {{ $service->category->color }};">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span class="text-xs font-medium">
                                        {{ $service->category->name }}
                                    </span>
                                </div>
                        @endif
                            </div>
                            
                            @if ($request->offered_price)
                                <div class="text-left sm:text-right">
                                     <span class="text-xs text-gray-500 uppercase tracking-wide">Estimated</span>
                                    <span class="block text-2xl font-bold text-gray-900">
                                        RM {{ number_format($request->offered_price, 2) }}
                                    </span>
                                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                          {{ str_replace('"', '', $request->selected_package) ?? 'Custom'}} Package
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-6 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                                <span class="font-medium">{{ $request->requester->name }}</span>
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
                            @if ($request->status === 'waiting_payment')
                                
                                {{-- Case A: Verification Needed --}}
                                @if ($request->payment_status === 'verification_status')
                                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-center gap-2 text-blue-800">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                <span class="font-bold text-sm">Payment Proof Uploaded</span>
                                            </div>
                                            
                                            {{-- Button: Normal Width --}}
                                            <button onclick="openProofModal('{{ asset('storage/' . $request->payment_proof) }}', {{ $request->id }})" 
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition-all">
                                                Check Proof
                                            </button>
                                        </div>
                                    </div>

                                {{-- Case B: Waiting for buyer --}}
                                @else
                                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 flex flex-col md:flex-row items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <p class="text-sm font-bold text-yellow-800">Waiting for Payment</p>
                                        </div>
                                        
                                        {{-- Button: Normal Width --}}
                                        <button onclick="finalizeOrder({{ $request->id }}, 'paid')"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-yellow-300 bg-white text-xs font-bold text-yellow-700 hover:bg-yellow-50 transition-all">
                                            Mark Paid Manually
                                        </button>
                                        <form id="finalize-form-{{ $request->id }}" action="{{ route('service-requests.finalize', $request->id) }}" method="POST" class="hidden">@csrf<input type="hidden" name="outcome" id="finalize-outcome-{{ $request->id }}"></form>
                                    </div>
                                @endif

                            {{-- 2. DISPUTE ACTIVE --}}
                            @elseif ($request->status === 'disputed')
                                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                                    <div class="flex items-start gap-3 mb-3">
                                        <svg class="h-5 w-5 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <div>
                                            <h5 class="text-sm font-bold text-red-800">Order Disputed</h5>
                                            <p class="text-xs text-red-600 mt-1">Reason: "{{ $request->dispute_reason ?? 'Admin review required' }}"</p>
                                        </div>
                                    </div>
                                    <form id="cancel-dispute-form-{{ $request->id }}" action="{{ route('service-requests.cancel-dispute', $request->id) }}" method="POST">
                                        @csrf
                                        {{-- Button: Normal Width --}}
                                        <button type="button" onclick="confirmCancelDispute({{ $request->id }})"
                                            class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-white border border-red-200 text-xs font-bold text-red-600 hover:bg-red-50 transition-all shadow-sm">
                                            Resolve & Complete
                                        </button>
                                    </form>
                                </div>

                            {{-- 3. ACCEPTED (Start Work) --}}
                            @elseif ($request->status === 'accepted')
                                {{-- Button: Normal Width --}}
                                <button onclick="markInProgress({{ $request->id }})"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 transition-all">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Start Work
                                </button>
                                <form id="progress-form-{{ $request->id }}" action="{{ route('service-requests.mark-in-progress', $request->id) }}" method="POST" class="hidden">@csrf</form>

                            {{-- 4. IN PROGRESS (Finish Work) --}}
                            @elseif ($request->status === 'in_progress')
                                {{-- Button: Normal Width --}}
                                <button onclick="markWorkFinished({{ $request->id }})"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-700 transition-all">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Finish Work
                                </button>
                                <form id="finish-work-form-{{ $request->id }}" action="{{ route('service-requests.mark-work-finished', $request->id) }}" method="POST" class="hidden">@csrf</form>
                            @endif

                            {{-- Secondary Actions Row (Short Buttons) --}}
                            <div class="flex items-center gap-2 pt-2">
                                {{-- Short Button: WhatsApp --}}
                                <a href="https://wa.me/6{{ $request->requester->phone }}" target="_blank" 
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs font-bold text-green-700 hover:bg-green-100 transition-colors">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" /></svg>
                                    WhatsApp
                                </a>
                                
                                {{-- Short Button: Details --}}
                                <a href="{{ route('service-requests.show', $request) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                                    Details
                                </a>

                                {{-- Short Square Button: Report (Only if needed) --}}
                                @if ($request->isWorkFinished() && $request->status !== 'disputed')
                                    <button onclick="openDisputeModal({{ $request->id }})" class="h-[34px] w-[34px] flex items-center justify-center rounded-lg border border-red-200 bg-white text-red-500 hover:bg-red-50 transition-colors" title="Report Issue">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    </button>
                                    <form id="dispute-form-{{ $request->id }}" action="{{ route('service-requests.report', $request->id) }}" method="POST" class="hidden">@csrf<input type="hidden" name="reason" id="dispute-reason-{{ $request->id }}"></form>
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
    @if ($receivedRequests->whereIn('status', ['completed', 'cancelled', 'rejected'])->isEmpty())
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
            @foreach ($receivedRequests->whereIn('status', ['completed', 'cancelled', 'rejected']) as $request)
                @php
                    // 1. Data Setup
                    $service = $request->studentService;
                    $displayId = str_pad($request->id, 5, '0', STR_PAD_LEFT);

                    // 2. Styling Logic
                    $theme = match ($request->status) {
                        'completed' => [
                            'border' => 'border-green-200 hover:border-green-300',
                            'strip' => 'bg-green-500',
                            'badge' => 'bg-green-100 text-green-700',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                        ],
                        'cancelled' => [
                            'border' => 'border-gray-200 hover:border-gray-300',
                            'strip' => 'bg-gray-400',
                            'badge' => 'bg-gray-100 text-gray-600',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'
                        ],
                        'rejected' => [
                            'border' => 'border-red-200 hover:border-red-300',
                            'strip' => 'bg-red-500',
                            'badge' => 'bg-red-100 text-red-700',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                        ],
                        default => [
                            'border' => 'border-gray-200', 'strip' => 'bg-gray-400', 'badge' => 'bg-gray-100 text-gray-600', 'icon' => ''
                        ]
                    };
                @endphp

                <div class="sr-request-item group relative overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-300 hover:shadow-md {{ $theme['border'] }}"
                    data-category="{{ optional($service->category)->name ?? 'Other' }}">
                    
                    <div class="absolute top-0 left-0 right-0 h-1 {{ $theme['strip'] }}"></div>

                    <div class="p-5 sm:p-6">
                        
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $theme['badge'] }}">
                                        {{ strtoupper($request->status) }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-mono">#{{ $displayId }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ optional($request->studentService)->title ?? 'Custom Request' }}
                                </h4>
                                @if(optional($service)->category)
                      <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1" style="color:{{ $service->category->color }}; background-color: {{ $service->category->color }}10; border: 1px solid {{ $service->category->color }};">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span class="text-xs font-medium">
                                        {{ $service->category->name }}
                                    </span>
                                </div>
                        @endif
                            </div>
                            
                            @if ($request->offered_price)
                                <div class="text-left sm:text-right">
                                                                         <span class="text-xs text-gray-500 uppercase tracking-wide">Estimated</span>

                                    <span class="block text-2xl font-bold text-gray-900">
                                        RM {{ number_format($request->offered_price, 2) }}
                                    </span>
                                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                          {{ str_replace('"', '', $request->selected_package) ?? 'Custom'}} Package
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
                                    <p class="font-semibold text-gray-900">{{ $request->requester->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            @if ($request->status === 'completed' && $request->started_at && $request->completed_at)
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Work Log</p>
                                        <div class="flex gap-4 text-sm mt-0.5">
                                            <div>
                                                <span class="text-xs text-gray-500 block">Start</span>
                                                <span class="font-mono font-semibold">{{ $request->started_at->format('H:i') }}</span>
                                            </div>
                                            <div class="border-l border-gray-200"></div>
                                            <div>
                                                <span class="text-xs text-gray-500 block">End</span>
                                                <span class="font-mono font-semibold">{{ $request->completed_at->format('H:i') }}</span>
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
                                @if ($request->reviewForHelper)
                                    <button onclick='openReviewModal(@json($request->reviewForHelper), "{{ $request->requester->name }}")' 
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 transition-all">
                                        <div class="flex gap-0.5 text-yellow-500">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $request->reviewForHelper->rating ? 'fas' : 'far' }} fa-star text-xs"></i>
                                            @endfor
                                        </div>
                                        <span>{{ $request->reviewForHelper->reply ? 'See Reply' : 'Reply' }}</span>
                                    </button>
                                @elseif($request->status === 'completed')
                                    <span class="text-xs text-gray-400 italic py-2">Waiting for buyer review...</span>
                                @endif

                                {{-- 2. Seller's Review (Outgoing) --}}
                                @if ($request->reviewByHelper)
                                    <div class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium rounded-lg bg-green-50 text-green-700 border border-green-200">
                                        <i class="fas fa-check"></i> You rated buyer
                                    </div>
                                @elseif($request->status === 'completed')
                                    <button onclick="openSellerReviewModal({{ $request->id }})" 
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
                                    <button onclick="closeProofModal()"
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
                                    <button onclick="submitDecision('unpaid_problem')"
                                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Reject / Report
                                    </button>

                                    <button onclick="submitDecision('paid')"
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
                                                <button type="button" onclick="closeReviewModal()"
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
                                                <button type="button" onclick="closeSellerReviewModal()"
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
                                            <form id="sellerReviewForm" onsubmit="submitSellerReview(event)">
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
                                                                data-value="{{ $i }}"
                                                                onclick="setSellerRating({{ $i }})"></i>
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
                                                    <button type="button" onclick="closeSellerReviewModal()"
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

                        <script>
                            function openReviewModal(review, requesterName) {
                                // 1. Populate Buyer Review
                                document.getElementById('modalRequesterName').innerText = requesterName;
                                document.getElementById('modalComment').innerText = review.comment || 'No textual comment provided.';
                                document.getElementById('modalDate').innerText = new Date(review.created_at).toLocaleDateString(undefined, {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });

                                // Generate Stars
                                let starsHtml = '';
                                for (let i = 1; i <= 5; i++) {
                                    starsHtml += `<i class="${i <= review.rating ? 'fas' : 'far'} fa-star"></i>`;
                                }
                                document.getElementById('modalStars').innerHTML = starsHtml;

                                // 2. Handle Reply State
                                const replyForm = document.getElementById('replyForm');
                                const viewReplyContainer = document.getElementById('viewReplyContainer');

                                if (review.reply) {
                                    replyForm.classList.add('hidden');
                                    viewReplyContainer.classList.remove('hidden');
                                    document.getElementById('modalReplyText').innerText = review.reply;
                                    document.getElementById('modalRepliedAt').innerText = new Date(review.replied_at).toLocaleDateString();
                                } else {
                                    viewReplyContainer.classList.add('hidden');
                                    replyForm.classList.remove('hidden');
                                    // Ensure you have a named route like 'reviews.reply' that accepts the ID
                                    // If your route is resource based: /reviews/{id}/reply
                                   replyForm.action = "{{ url('reviews') }}/" + review.id + "/reply";

                                }

                                document.getElementById('reviewModal').classList.remove('hidden');
                            }

                            function finalizeOrder(id, outcome) {
                                // 1. Define Text based on outcome
                                let title = outcome === 'paid' ? 'Mark as Paid & Complete?' : 'Report Unpaid?';
                                let text = outcome === 'paid' ?
                                    "By proceeding, you confirm that the payment has been received. This action is irreversible and cannot be changed later." :
                                    "This will close the order as Unpaid. Are you sure?";
                                let btnColor = outcome === 'paid' ? '#16a34a' : '#dc2626';

                                // 2. Show Confirmation
                                Swal.fire({
                                    title: title,
                                    text: text,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: btnColor,
                                    confirmButtonText: 'Yes, Proceed'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // 3. Set the hidden input value ('paid' or 'unpaid_problem')
                                        document.getElementById('finalize-outcome-' + id).value = outcome;

                                        // 4. Submit the form
                                        document.getElementById('finalize-form-' + id).submit();
                                    }
                                });
                            }

                            function openDisputeModal(requestId) {
                                Swal.fire({
                                    title: 'Report / Dispute Transaction',
                                    html: `
                <div class="text-left text-sm text-gray-600 mb-4">
                    Please select a reason for reporting this buyer:
                </div>
                
                <div class="flex flex-col gap-3 text-left">
                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200">
                        <input type="radio" name="dispute_reason" value="Buyer did not confirm payment after services complete" class="mt-1" onchange="toggleOtherField(false)">
                        <span>Buyer did not confirm payment after services complete</span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200">
                        <input type="radio" name="dispute_reason" value="Buyer is unresponsive (Ghosting)" class="mt-1" onchange="toggleOtherField(false)">
                        <span>Buyer is unresponsive (Ghosting)</span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200">
                        <input type="radio" name="dispute_reason" value="Buyer refuses to pay the agreed amount" class="mt-1" onchange="toggleOtherField(false)">
                        <span>Buyer refuses to pay the agreed amount</span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200">
                        <input type="radio" name="dispute_reason" value="Buyer is demanding extra work not in agreement" class="mt-1" onchange="toggleOtherField(false)">
                        <span>Buyer is demanding extra work not in agreement</span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200">
                        <input type="radio" name="dispute_reason" value="Inappropriate behavior from buyer" class="mt-1" onchange="toggleOtherField(false)">
                        <span>Inappropriate behavior from buyer</span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200">
                        <input type="radio" name="dispute_reason" value="other" class="mt-1" onchange="toggleOtherField(true)">
                        <span class="font-semibold">Other (Specify below)</span>
                    </label>
                </div>

                <textarea id="swal-other-reason" class="swal2-textarea hidden" placeholder="Please describe the issue in detail..." style="display:none; margin-top: 15px; font-size: 0.9em;"></textarea>
            `,
                                    showCancelButton: true,
                                    confirmButtonText: 'Submit Report',
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    focusConfirm: false,
                                    preConfirm: () => {
                                        const selectedOption = document.querySelector('input[name="dispute_reason"]:checked');
                                        const otherText = document.getElementById('swal-other-reason').value;

                                        if (!selectedOption) {
                                            Swal.showValidationMessage('Please select a reason');
                                            return false;
                                        }

                                        if (selectedOption.value === 'other') {
                                            if (!otherText.trim()) {
                                                Swal.showValidationMessage('Please specify the reason for "Other"');
                                                return false;
                                            }
                                            return otherText; // Return the typed text
                                        }

                                        return selectedOption.value; // Return the predefined text
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // 1. Put the result into the hidden input
                                        document.getElementById('dispute-reason-' + requestId).value = result.value;

                                        // 2. Submit the form
                                        document.getElementById('dispute-form-' + requestId).submit();
                                    }
                                });
                            }

                            // Helper function to show/hide the textarea
                            function toggleOtherField(show) {
                                const textArea = document.getElementById('swal-other-reason');
                                if (show) {
                                    textArea.style.display = 'block';
                                    textArea.focus();
                                } else {
                                    textArea.style.display = 'none';
                                }
                            }

                            function confirmCancelDispute(requestId) {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "This will withdraw your report and immediately mark the order as Completed.",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#10B981', // Green color
                                    cancelButtonColor: '#d33', // Red color
                                    confirmButtonText: 'Yes, Complete Order',
                                    cancelButtonText: 'No'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Submit the specific form based on ID
                                        document.getElementById('cancel-dispute-form-' + requestId).submit();
                                    }
                                });
                            }

                            // 1. OPEN PROOF MODAL
                            function openProofModal(fileUrl, requestId) {
                                const modal = document.getElementById('proofModal');
                                const img = document.getElementById('proofImage');
                                const pdf = document.getElementById('proofPdf');
                                const fallback = document.getElementById('proofFallback');
                                const link = document.getElementById('proofLink');
                                const form = document.getElementById('finalizeOrderForm');

                                // Set Form Action Dynamically
                                // Ensure you have this route defined: Route::post('/service-requests/{id}/finalize', ...)
                               form.action = "{{ url('service-requests') }}/" + requestId + "/finalize";

                                // Reset viewers
                                img.classList.add('hidden');
                                pdf.classList.add('hidden');
                                fallback.classList.add('hidden');
                                img.src = '';
                                pdf.src = '';
                                link.href = fileUrl;

                                // Detect extension robustly (handles uppercase + query strings)
                                let ext = '';
                                try {
                                    const cleanPath = new URL(fileUrl, window.location.origin).pathname;
                                    const parts = cleanPath.split('.');
                                    ext = parts.length > 1 ? parts.pop().toLowerCase() : '';
                                } catch (e) {
                                    ext = (fileUrl.split('?')[0].split('.').pop() || '').toLowerCase();
                                }

                                const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                                const isImage = imageExts.includes(ext);
                                const isPdf = ext === 'pdf';

                                if (isImage) {
                                    img.onerror = function() {
                                        img.classList.add('hidden');
                                        fallback.classList.remove('hidden');
                                    };
                                    img.src = fileUrl;
                                    img.classList.remove('hidden');
                                } else if (isPdf) {
                                    pdf.src = fileUrl;
                                    pdf.classList.remove('hidden');
                                } else {
                                    fallback.classList.remove('hidden');
                                }

                                modal.classList.remove('hidden');
                            }

                            // 2. CLOSE MODAL
                            function closeProofModal() {
                                document.getElementById('proofModal').classList.add('hidden');
                                document.getElementById('proofImage').src = '';
                                document.getElementById('proofPdf').src = '';
                            }

                            // 3. SUBMIT DECISION (Paid or Unpaid)
                            function submitDecision(outcome) {
                                const form = document.getElementById('finalizeOrderForm');
                                const input = document.getElementById('finalizeOutcome');

                                input.value = outcome; // 'paid' or 'unpaid_problem'

                                let title = outcome === 'paid' ? 'Confirm Payment?' : 'Report Issue?';
                                let text = outcome === 'paid' ?
                                    'This will complete the order.' :
                                    'This will flag the order as unpaid. Are you sure?';
                                let color = outcome === 'paid' ? '#16a34a' : '#dc2626';

                                // SweetAlert Confirmation
                                Swal.fire({
                                    title: title,
                                    text: text,
                                    icon: outcome === 'paid' ? 'question' : 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: color,
                                    confirmButtonText: 'Yes, Proceed'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        form.submit();
                                    }
                                });
                            }

                            function closeReviewModal() {
                                document.getElementById('reviewModal').classList.add('hidden');
                            }

                            function openSellerReviewModal(requestId) {
                                // Reset form
                                document.getElementById('sellerReviewForm').reset();
                                document.getElementById('sellerReviewRequestId').value = requestId;
                                document.getElementById('sellerReviewRating').value = '';
                                document.getElementById('ratingError').classList.add('hidden');

                                // Reset Stars visual
                                document.querySelectorAll('.seller-star-input').forEach(star => {
                                    star.classList.remove('fas', 'text-yellow-400');
                                    star.classList.add('far', 'text-gray-300');
                                });

                                document.getElementById('sellerReviewModal').classList.remove('hidden');
                            }

                            function closeSellerReviewModal() {
                                document.getElementById('sellerReviewModal').classList.add('hidden');
                            }

                            function setSellerRating(rating) {
                                document.getElementById('sellerReviewRating').value = rating;
                                document.getElementById('ratingError').classList.add('hidden');

                                const stars = document.querySelectorAll('.seller-star-input');
                                stars.forEach(star => {
                                    const val = parseInt(star.getAttribute('data-value'));
                                    if (val <= rating) {
                                        star.classList.remove('far', 'text-gray-300');
                                        star.classList.add('fas', 'text-yellow-400');
                                    } else {
                                        star.classList.remove('fas', 'text-yellow-400');
                                        star.classList.add('far', 'text-gray-300');
                                    }
                                });
                            }

                            function submitSellerReview(event) {
                                event.preventDefault();
                                const rating = document.getElementById('sellerReviewRating').value;
                                const requestId = document.getElementById('sellerReviewRequestId').value;
                                const comment = document.getElementById('sellerComment').value;

                                if (!rating) {
                                    document.getElementById('ratingError').classList.remove('hidden');
                                    return;
                                }

                                Swal.fire({
                                    title: 'Submitting Review...',
                                    allowOutsideClick: false,
                                    didOpen: () => Swal.showLoading()
                                });

                                // Send AJAX Request to your existing Controller
                                fetch("{{ route('reviews.store') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                            "Accept": "application/json"
                                        },
                                        body: JSON.stringify({
                                            service_request_id: requestId,
                                            rating: rating,
                                            comment: comment
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            closeSellerReviewModal();
                                            Swal.fire({
                                                title: 'Review Submitted!',
                                                text: 'Thank you for rating the Buyer.',
                                                icon: 'success',
                                                timer: 1500,
                                                showConfirmButton: false
                                            }).then(() => {
                                                location.reload();
                                            });
                                        } else {
                                            Swal.fire('Error', data.message || 'Could not submit review', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error(error);
                                        Swal.fire('Error', 'An unexpected error occurred.', 'error');
                                    });
                            }

                            function openRejectModal(requestId) {
                                Swal.fire({
                                    title: 'Reject Request?',
                                    text: "Please provide a reason for the requester.",
                                    input: 'textarea', // Creates a text box
                                    inputPlaceholder: 'e.g. I am fully booked on this date...',
                                    inputAttributes: {
                                        'aria-label': 'Type your rejection reason here'
                                    },
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Yes, Reject it',
                                    preConfirm: (reason) => {
                                        if (!reason) {
                                            Swal.showValidationMessage('You must provide a reason!')
                                        }
                                        return reason; // Returns the text value
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // 1. Get the form
                                        const form = document.getElementById('reject-form-' + requestId);

                                        // 2. Create a hidden input for the reason
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'rejection_reason';
                                        input.value = result.value; // The text from SweetAlert

                                        // 3. Append and Submit
                                        form.appendChild(input);
                                        form.submit();
                                    }
                                });
                            }
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Tab Switching Logic
        function showStatusTab(status) {
            // Hide all tab contents
            document.querySelectorAll('.sr-status-tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Reset all tab buttons
            document.querySelectorAll('.sr-status-tab-button').forEach(button => {
                button.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                button.classList.add('text-gray-500', 'hover:text-custom-teal');
            });

            // Show active content
            const targetContent = document.getElementById(status + '-content');
            if (targetContent) targetContent.classList.remove('hidden');

            // Highlight active button
            const targetButton = document.getElementById(status + '-tab');
            if (targetButton) {
                targetButton.classList.remove('text-gray-500', 'hover:text-custom-teal');
                targetButton.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
            }
        }

        // Search function
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('request-search');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();

                    // Select all request cards across all tabs
                    const items = document.querySelectorAll('.sr-request-item');

                    items.forEach(item => {
                        // Get the text content of the card (Title, Name, Status, etc.)
                        const text = item.textContent.toLowerCase();

                        // Show or Hide based on match
                        if (text.includes(query)) {
                            item.style.display = ''; // Reset to default (show)
                        } else {
                            item.style.display = 'none'; // Hide
                        }
                    });
                });
            }
        });

        // 2. Action Triggers
        async function acceptRequest(id) {
            confirmAction(id, 'accept', 'Accept Request?', 'You can start working on this service once accepted.',
                '#16a34a', 'Yes, Accept');
        }

        async function rejectRequest(id) {
            confirmAction(id, 'reject', 'Reject Request?', 'The requester will be notified.', '#dc2626', 'Yes, Reject');
        }

        async function markInProgress(id) {
            confirmAction(id, 'progress', 'Start Work?', 'The status will change to In Progress.', '#2563eb',
                'Yes, Start');
        }

        async function markWorkFinished(id) {
            confirmAction(id, 'finish-work', 'Finish Work?',
                'This will notify Buyer the work is done and waiting for payment.', '#2563eb',
                'Yes, Finish');
        }

        async function markCompleted(id) {
            confirmAction(id, 'completed', 'Mark as Completed?', 'This will notify the Buyer the work is done.',
                '#4f46e5', 'Yes, Complete');
        }

        // 3. Reusable SweetAlert & Fetch Logic
        function confirmAction(id, type, title, text, confirmColor, confirmText) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Find the hidden form associated with the button
                    // Note: Ensure your forms have IDs like "accept-form-123"
                    const formId = `${type}-form-${id}`;
                    const form = document.getElementById(formId);

                    if (!form) {
                        Swal.fire('Error', 'Form not found for this action.', 'error');
                        return;
                    }

                    const url = form.action;
                    const token = form.querySelector('input[name="_token"]').value;

                    // Show loading state
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // Send AJAX Request
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // Reload page to update the lists
                                });
                            } else {
                                Swal.fire('Error', data.message || 'Something went wrong.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'System error occurred. Please try again.', 'error');
                        });
                }
            });
        }

        // 4. Initialize Tabs on Load
        document.addEventListener('DOMContentLoaded', function() {
            // Check URL for 'tab' parameter or default to 'pending'
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || '{{ $defaultStatusTab }}';
            showStatusTab(activeTab);
        });
    </script>
@endsection
