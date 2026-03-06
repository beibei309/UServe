<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Service Requests') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="sent-content" class="sr-tab-content">
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-b from-white to-slate-50/50 shadow-sm">
                    <div class="p-6 text-gray-800">
                        <h3 class="font-medium mb-4 700" style="font-size: 25px;">My Orders ({{ $sentRequests->count() }}
                            total)</h3>

                        {{-- SEARCH & FILTER SECTION --}}
                        <div class="mb-6 flex flex-col md:flex-row gap-4">
                            {{-- Search Bar --}}
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="request-search"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm transition"
                                    placeholder="Search by title, seller, etc...">
                            </div>

                            {{-- Category Filter Dropdown --}}
                            <div class="w-full md:w-64">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                    </div>

                                    <select id="category-filter"
                                        class="block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm appearance-none cursor-pointer">
                                        <option value="">All Categories</option>
                                        @foreach ($uniqueCategories as $category)
                                            <option value="{{ $category }}">{{ $category }}</option>
                                        @endforeach
                                    </select>

                                    {{-- Custom chevron icon for select --}}
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex space-x-4 border-b border-gray-200">
                                <button data-status-tab="pending" id="pending-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'pending' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    Pending
                                </button>
                                <button data-status-tab="in-progress" id="in-progress-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'in-progress' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    In Progress
                                </button>
                                <button data-status-tab="completed" id="completed-tab"
                                    class="sr-status-tab-button py-2 px-4 text-sm font-medium {{ $defaultStatusTab === 'completed' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-custom-teal' }} focus:outline-none">
                                    Completed
                                </button>
                            </div>
                        </div>

                      {{-- ========================== --}}
                        {{-- 1. PENDING TAB CONTENT     --}}
                        {{-- ========================== --}}
                        <div id="pending-content"
                            class="sr-status-tab-content {{ $defaultStatusTab === 'pending' ? '' : 'hidden' }}">
                            @if ($sentRequests->where('hsr_status', 'pending')->isEmpty())
                                <div
                                    class="flex flex-col items-center justify-center py-16 text-center bg-white rounded-xl border border-dashed border-gray-300">
                                    <div class="rounded-full bg-gray-50 p-4 mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">No Pending Requests</h3>
                                    <p class="mt-2 text-sm text-gray-500 max-w-sm">You're all caught up! Browse services
                                        to start a new learning journey.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('services.index') }}"
                                            class="inline-flex items-center px-5 py-2.5 shadow-sm text-sm font-medium rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition-colors focus:ring-4 focus:ring-teal-100">
                                            Find a service
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-6">
                                    @foreach ($sentRequests->where('hsr_status', 'pending') as $request)
                                        <div
                                            class="sr-request-item group relative overflow-hidden rounded-2xl border {{ $request->ui_is_seller_restricted ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white hover:border-yellow-300' }} shadow-sm transition-all duration-300 hover:shadow-md"
                                            data-category="{{ optional(optional($request->studentService)->category)->hc_name ?? 'Other' }}">

                                            <div
                                                class="absolute top-0 left-0 right-0 h-1 {{ $request->ui_is_seller_restricted ? 'bg-red-500' : 'bg-gradient-to-r from-yellow-400 to-orange-300' }}">
                                            </div>

                                            <div class="p-5 sm:p-6">
                                                <div
                                                    class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-3 mb-2">
                                                            @if ($request->ui_is_seller_restricted)
                                                                <span
                                                                    class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-700">
                                                                    SELLER RESTRICTED
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-bold text-yellow-700">
                                                                    {{ strtoupper($request->hsr_status) }}
                                                                </span>
                                                            @endif
                                                            <span
                                                                class="text-xs text-gray-400">#{{ $request->hsr_id }}</span>
                                                        </div>

                                                        <h4
                                                            class="text-lg font-bold {{ $request->ui_is_seller_restricted ? 'text-gray-500 line-through' : 'text-gray-900' }} group-hover:text-yellow-600 transition-colors leading-tight">
                                                            {{ optional($request->studentService)->hss_title ?? 'Custom Request' }}
                                                        </h4>

                                                        @if (optional(optional($request->studentService)->category) && !$request->ui_is_seller_restricted)
                                                            <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1"
                                                                style="color:{{ $request->studentService->category->hc_color }}; background-color: {{ $request->studentService->category->hc_color }}10; border: 1px solid {{ $request->studentService->category->hc_color }};">
                                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                                </svg>
                                                                <span class="text-xs font-medium">
                                                                    {{ $request->studentService->category->hc_name }}
                                                                </span>
                                                            </div>
                                                        @endif

                                                        <p class="text-xs text-gray-400 mt-2">
                                                            Sent {{ $request->ui_created_human }}
                                                        </p>
                                                    </div>

                                                    <div class="text-left sm:text-right mt-2 sm:mt-0">
                                                        @if ($request->hsr_offered_price)
                                                            <div
                                                                class="text-2xl font-bold {{ $request->ui_is_seller_restricted ? 'text-gray-400' : 'text-gray-900' }}">
                                                                RM {{ number_format($request->hsr_offered_price, 2) }}
                                                            </div>
                                                            <div
                                                                class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                                                {{ $request->ui_package_label }}
                                                                Package
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Hide details if banned to keep it clean --}}
                                                @if (!$request->ui_is_seller_restricted)
                                                    <div class="h-px w-full bg-gray-100 my-4"></div>

                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

                                                        <div class="flex items-start gap-3">
                                                            <div
                                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs font-medium text-gray-500 uppercase">
                                                                    Provider</p>
                                                                <p class="text-sm font-semibold text-gray-900">
                                                                    {{ $request->provider->hu_name }}</p>
                                                            </div>
                                                        </div>

                                                        @if ($request->ui_first_date_display)
                                                            <div class="flex items-start gap-3">
                                                                <div
                                                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                    </svg>
                                                                </div>
                                                                <div>
                                                                    <p class="text-xs font-medium text-gray-500 uppercase">
                                                                        Start Date</p>
                                                                    <p class="text-sm font-semibold text-gray-900">
                                                                        {{ $request->ui_first_date_display }}
                                                                        @if ($request->ui_date_count > 1)
                                                                            <span
                                                                                class="text-xs font-normal text-gray-500 ml-1">(+{{ $request->ui_date_count - 1 }}
                                                                                days)</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="flex items-start gap-3">
                                                            <div
                                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-purple-50 text-purple-600">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs font-medium text-gray-500 uppercase">
                                                                    Details</p>
                                                                <p class="text-sm font-semibold text-gray-900">
                                                                    {{ $request->ui_pkg_duration ? $request->ui_pkg_duration . ' Hrs' : 'N/A' }}
                                                                    <span class="text-gray-300 mx-1">|</span>
                                                                    {{ $request->ui_pkg_frequency ? ucfirst($request->ui_pkg_frequency) : 'One-time' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($request->hsr_message && !$request->ui_is_seller_restricted)
                                                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-100 mb-6">
                                                        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Your
                                                            Note</p>
                                                        <p class="text-sm text-gray-600 italic">
                                                            "{{ $request->hsr_message }}"</p>
                                                    </div>
                                                @endif

                                                <div
                                                    class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-2">
                                                    <div class="flex items-center gap-4 w-full sm:w-auto">
                                                        @if ($request->ui_is_seller_restricted)
                                                            {{-- BANNED STATE: No Cancel button (visually cancelled) --}}
                                                            <span
                                                                class="inline-flex items-center gap-2 text-sm font-bold text-red-600 bg-red-50 px-3 py-2 rounded-lg border border-red-100 w-full sm:w-auto justify-center">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                </svg>
                                                                Service Cancelled
                                                            </span>
                                                        @else
                                                            <button data-cancel-request="{{ $request->hsr_id }}"
                                                                class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition-colors focus:ring-2 focus:ring-offset-1 focus:ring-red-500">
                                                                Cancel
                                                            </button>
                                                            <form id="cancel-form-{{ $request->hsr_id }}"
                                                                action="{{ route('service-requests.cancel', $request->hsr_id) }}"
                                                                method="POST" class="hidden">@csrf</form>
                                                        @endif

                                                        <a href="{{ route('service-requests.show', $request) }}"
                                                            class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors hidden sm:block">
                                                            View Details
                                                        </a>
                                                    </div>

                                                    {{-- Only show WhatsApp if NOT banned --}}
                                                    @if (!$request->ui_is_seller_restricted)
                                                        <div class="flex gap-3 w-full sm:w-auto">
                                                            <a href="{{ route('service-requests.show', $request) }}"
                                                                class="w-full sm:hidden text-center text-sm font-medium text-gray-500 hover:text-gray-900 border border-gray-200 rounded-lg py-2">
                                                                Details
                                                            </a>
                                                            <a href="https://wa.me/6{{ $request->provider->hu_phone }}"
                                                                target="_blank"
                                                                class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 px-6 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 shadow-sm transition-all hover:-translate-y-0.5">
                                                                <svg class="w-4 h-4" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                                                </svg>
                                                                WhatsApp
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- ========================== --}}
                        {{-- 2. IN PROGRESS TAB CONTENT --}}
                        {{-- ========================== --}}
                        <div id="in-progress-content"
                            class="sr-status-tab-content {{ $defaultStatusTab === 'in-progress' ? '' : 'hidden' }}">
                            @if ($sentRequests->whereIn('hsr_status', ['accepted', 'in_progress', 'waiting_payment', 'disputed'])->isEmpty())
                                {{-- Empty State --}}
                                <div
                                    class="flex flex-col items-center justify-center py-16 text-center bg-white rounded-xl border border-dashed border-gray-300">
                                    <div class="rounded-full bg-blue-50 p-4 mb-4">
                                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">No Ongoing Services</h3>
                                    <p class="mt-2 text-sm text-gray-500">You have no active services at the moment.
                                    </p>
                                </div>
                            @else
                                <div class="space-y-6">
                                    @foreach ($sentRequests->whereIn('hsr_status', ['accepted', 'in_progress', 'waiting_payment', 'disputed']) as $request)
                                        <div
                                            class="sr-request-item group relative overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-300 hover:shadow-md {{ $request->ui_inprogress_style['card'] }}"
                                            data-category="{{ optional(optional($request->studentService)->category)->hc_name ?? 'Other' }}">
                                            <div class="absolute top-0 left-0 right-0 h-1 {{ $request->ui_inprogress_style['stripe'] }}"></div>
                                            <div class="p-5 sm:p-6">
                                                {{-- Top Header Section --}}
                                                <div
                                                    class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-3 mb-2">
                                                            <span
                                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $request->ui_inprogress_style['badge'] }}">
                                                                {{ $request->ui_inprogress_style['status_text'] }}
                                                            </span>
                                                            <span class="text-xs text-gray-400">#{{ $request->hsr_id }}</span>
                                                        </div>
                                                        <h4
                                                            class="text-lg font-bold {{ $request->ui_is_seller_restricted ? 'text-gray-500 line-through' : 'text-gray-900' }} group-hover:text-blue-600 transition-colors leading-tight">
                                                            {{ optional($request->studentService)->hss_title ?? 'Custom Request' }}
                                                        </h4>
                                                        @if (optional(optional($request->studentService)->category) && !$request->ui_is_seller_restricted)
                                                            <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1"
                                                                style="color:{{ $request->studentService->category->hc_color }}; background-color: {{ $request->studentService->category->hc_color }}10; border: 1px solid {{ $request->studentService->category->hc_color }};">
                                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                                </svg>
                                                                <span class="text-xs font-medium">
                                                                    {{ $request->studentService->category->hc_name }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <p class="text-xs text-gray-400 mt-2">Started
                                                            {{ $request->ui_updated_human }}</p>
                                                    </div>
                                                    <div class="text-left sm:text-right mt-2 sm:mt-0">
                                                        @if ($request->hsr_offered_price)
                                                            <div
                                                                class="text-2xl font-bold {{ $request->ui_is_seller_restricted ? 'text-gray-400' : 'text-gray-900' }}">
                                                                RM {{ number_format($request->hsr_offered_price, 2) }}
                                                            </div>
                                                            <div
                                                                class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                                                {{ $request->ui_package_label }}
                                                                Package
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="h-px w-full bg-gray-100 my-4"></div>

                                                {{-- Details Grid - Only show if valid --}}
                                                @if (!$request->ui_is_seller_restricted)
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                                                        {{-- Provider --}}
                                                        <div class="flex items-start gap-3">
                                                            <div
                                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs font-medium text-gray-500 uppercase">
                                                                    Provider</p>
                                                                <p class="text-sm font-semibold text-gray-900">
                                                                    {{ $request->provider->hu_name }}</p>
                                                            </div>
                                                        </div>

                                                        {{-- Date --}}
                                                        <div class="flex items-start gap-3">
                                                            <div
                                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs font-medium text-gray-500 uppercase">
                                                                    Service Date</p>
                                                                <p class="text-sm font-semibold text-gray-900">
                                                                    {{ $request->ui_first_date_display ?? '-' }}
                                                                    @if ($request->ui_has_date_passed)
                                                                        <span
                                                                            class="text-xs text-orange-500 font-normal ml-1">(Passed)</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif


                                                {{-- ACTION BUTTONS ROW --}}
                                                <div
                                                    class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-2">

                                                    {{-- LEFT SIDE: Cancel & Report --}}
                                                    <div class="flex items-center gap-4 w-full sm:w-auto">

                                                        @if ($request->ui_is_seller_restricted)
                                                            {{-- Banned State: Show cancelled text --}}
                                                            <span
                                                                class="inline-flex items-center gap-2 text-sm font-bold text-red-600 bg-red-50 px-3 py-2 rounded-lg border border-red-100 w-full sm:w-auto justify-center">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                </svg>
                                                                Service Cancelled
                                                            </span>
                                                        @else
                                                            {{-- 1. Cancel Button (Only if NOT waiting payment/disputed/completed) --}}
                                                            @if (!in_array($request->hsr_status, ['waiting_payment', 'disputed', 'completed', 'canceled']))
                                                                <button data-cancel-request="{{ $request->hsr_id }}"
                                                                    class="text-sm font-medium text-red-600 hover:text-red-700 hover:underline">
                                                                    Cancel Request
                                                                </button>
                                                                <form id="cancel-form-{{ $request->hsr_id }}"
                                                                    action="{{ route('service-requests.cancel', $request->hsr_id) }}"
                                                                    method="POST" class="hidden">
                                                                    @csrf
                                                                </form>
                                                            @endif

                                                            {{-- 2. REPORT ISSUE BUTTON --}}
                                                            @if (in_array($request->hsr_status, ['accepted', 'in_progress']) && $request->ui_has_date_passed && !$request->hsr_dispute_reason)
                                                                <button data-open-report="{{ $request->hsr_id }}"
                                                                    class="inline-flex items-center gap-1 text-sm font-medium text-orange-600 hover:text-orange-800 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg border border-orange-200 transition-colors">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                    </svg>
                                                                    Report Issue
                                                                </button>
                                                            @endif
                                                        @endif

                                                        {{-- Show if already reported --}}
                                                        @if ($request->hsr_dispute_reason)
                                                            <span
                                                                class="text-xs text-red-500 font-semibold bg-red-50 px-2 py-1 rounded border border-red-100">
                                                                Reported:
                                                                "{{ Str::limit($request->hsr_dispute_reason, 20) }}"
                                                            </span>
                                                        @endif

                                                        <a href="{{ route('service-requests.show', $request) }}"
                                                            class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors hidden sm:block">
                                                            View Details
                                                        </a>
                                                    </div>

                                                    {{-- RIGHT SIDE: Actions (Payment & Contact) --}}
                                                    @if (!$request->ui_is_seller_restricted)
                                                        <div class="flex gap-3 w-full sm:w-auto">

                                                            {{-- 1. PAY NOW BUTTON --}}
                                                            @if ($request->hsr_status == 'waiting_payment')
                                                                @if ($request->hsr_payment_status == 'verification_status')
                                                                    <button disabled
                                                                        class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 px-6 py-2 text-sm font-bold text-yellow-700 bg-yellow-100 border border-yellow-200 rounded-lg cursor-not-allowed">
                                                                        <svg class="w-4 h-4 animate-spin"
                                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                            viewBox="0 0 24 24">
                                                                            <circle class="opacity-25" cx="12" cy="12"
                                                                                r="10" stroke="currentColor"
                                                                                stroke-width="4">
                                                                            </circle>
                                                                            <path class="opacity-75" fill="currentColor"
                                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                            </path>
                                                                        </svg>
                                                                        Verifying Payment
                                                                    </button>
                                                                @else
                                                                    <button data-open-payment="{{ $request->hsr_id }}"
                                                                        data-payment-price="{{ number_format($request->hsr_offered_price, 2) }}"
                                                                        class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-all hover:-translate-y-0.5">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                                        </svg>
                                                                        Mark as Paid
                                                                    </button>
                                                                @endif
                                                            @endif

                                                            {{-- 2. Contact WhatsApp --}}
                                                            <a href="https://wa.me/6{{ $request->provider->hu_phone }}"
                                                                target="_blank"
                                                                class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 px-6 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 shadow-sm transition-all hover:-translate-y-0.5">
                                                                <svg class="w-4 h-4" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                                                </svg>
                                                                WhatsApp
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div id="completed-content"
                            class="sr-status-tab-content {{ $defaultStatusTab === 'completed' ? '' : 'hidden' }}">
                            @if ($sentRequests->whereIn('hsr_status', ['completed', 'cancelled', 'rejected'])->isEmpty())
                                <div
                                    class="flex flex-col items-center justify-center py-16 text-center bg-white rounded-xl border border-dashed border-gray-300">
                                    <div class="rounded-full bg-gray-50 p-4 mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">No Completed Requests Yet</h3>
                                    <p class="mt-2 text-sm text-gray-500">Completed and cancelled requests will appear
                                        here.</p>
                                </div>
                            @else
                                <div class="space-y-6">
                                    @foreach ($sentRequests->whereIn('hsr_status', ['completed', 'cancelled', 'rejected']) as $request)
                                        <div
                                            class="sr-request-item group relative overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-300 hover:shadow-md {{ $request->ui_completed_theme['border'] }}"
                                            data-category="{{ optional(optional($request->studentService)->category)->hc_name ?? 'Other' }}">

                                            <div class="absolute top-0 left-0 right-0 h-1 {{ $request->ui_completed_theme['strip'] }}">
                                            </div>

                                            <div class="p-5 sm:p-6">
                                                <div
                                                    class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-3 mb-2">
                                                            <span
                                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $request->ui_completed_theme['badge'] }}">
                                                                {{ strtoupper($request->hsr_status) }}
                                                            </span>
                                                            <span
                                                                class="text-xs text-gray-400">#{{ $request->hsr_id }}</span>
                                                        </div>

                                                        <h4 class="text-lg font-bold text-gray-900 leading-tight">
                                                            {{ optional($request->studentService)->hss_title ?? 'Custom Request' }}
                                                        </h4>

                                                        @if (optional(optional($request->studentService)->category))
                                                            <div class="mt-2 inline-flex items-center gap-1.5 rounded-md px-2 py-1"
                                                                style="color:{{ $request->studentService->category->hc_color }}; background-color: {{ $request->studentService->category->hc_color }}10; border: 1px solid {{ $request->studentService->category->hc_color }};">
                                                                <svg class="h-3 w-3" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                                </svg>
                                                                <span class="text-xs font-medium">
                                                                    {{ $request->studentService->category->hc_name }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="text-left sm:text-right mt-2 sm:mt-0">
                                                        @if ($request->hsr_offered_price)
                                                            <div
                                                                class="text-2xl font-bold text-gray-400 group-hover:text-gray-900 transition-colors">
                                                                RM {{ number_format($request->hsr_offered_price, 2) }}
                                                            </div>
                                                            <div
                                                                class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                                                {{ $request->ui_package_label }}
                                                                Package
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="h-px w-full bg-gray-100 my-4"></div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                                                    <div class="flex items-start gap-3">
                                                        <div
                                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs font-medium text-gray-500 uppercase">
                                                                Provider</p>
                                                            <p class="text-sm font-semibold text-gray-700">
                                                                {{ $request->provider->hu_name }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-start gap-3">
                                                        <div
                                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $request->ui_completed_theme['icon_bg'] }} {{ $request->ui_completed_theme['icon_text'] }}">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                {!! $request->ui_completed_theme['icon'] !!}
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs font-medium text-gray-500 uppercase">
                                                                {{ $request->hsr_status === 'completed' ? 'Completed On' : 'Updated On' }}
                                                            </p>
                                                            <p class="text-sm font-semibold text-gray-700">
                                                                {{ $request->ui_updated_date }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-start gap-3">
                                                        <div
                                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs font-medium text-gray-500 uppercase">
                                                                Details</p>
                                                            <p class="text-sm font-semibold text-gray-700">
                                                                {{ $request->ui_pkg_duration ? $request->ui_pkg_duration . ' Hrs' : 'Custom' }}
                                                                <span class="text-gray-300 mx-1">|</span>
                                                                {{ $request->ui_pkg_frequency ? ucfirst($request->ui_pkg_frequency) : 'N/A' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex flex-col md:flex-row items-center justify-between gap-4 pt-2">
                                                    <a href="{{ route('service-requests.show', $request) }}"
                                                        class="text-sm font-medium text-gray-400 hover:text-gray-900 transition-colors flex items-center gap-1">
                                                        View Request Details
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </a>

                                                    @if (
                                                        $request->isCompleted() &&
                                                            !$request->ui_reviewed_by_auth)
                                                        <button data-open-review="{{ $request->hsr_id }}"
                                                            class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 hover:shadow-md transition-all focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                            </svg>
                                                            Leave a Review
                                                        </button>
                                                    @elseif ($request->isCompleted())
                                                        <div
                                                            class="flex items-center gap-2 text-sm text-green-600 font-medium bg-green-50 px-3 py-1.5 rounded-md border border-green-100">
                                                            <svg class="w-4 h-4" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Reviewed
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------- --}}
    {{-- MODAL --}}
    {{-- -------------------------- --}}

    {{-- Payment Proof Modal --}}
    <div id="paymentModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-xl rounded-xl bg-white">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                <h3 class="text-lg font-bold text-gray-900">Confirm Payment</h3>
                <button type="button" data-close-payment class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">
                    Estimated Total Amount: <span class="font-bold text-green-600">RM <span
                            id="paymentModalPrice">0.00</span></span>
                </p>
                <p class="text-xs text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <i class="fas fa-info-circle mr-1"></i>
                    Please ensure you have transferred the money to the seller before confirming.
                </p>
            </div>

            {{-- Form --}}
            <form id="paymentProofForm" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- File Input --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Proof (Optional)</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2 text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="text-xs text-gray-500"><span class="font-semibold">Click to upload</span>
                                </p>
                                <p class="text-[10px] text-gray-400">JPG, PNG or PDF (MAX. 2MB)</p>
                            </div>
                            <input id="dropzone-file" name="payment_proof" type="file" class="hidden"
                                accept="image/*,application/pdf" />
                        </label>
                    </div>
                    {{-- File Name Preview --}}
                    <div id="fileNamePreview" class="text-xs text-center text-indigo-600 mt-2 font-medium hidden">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <button type="button" data-close-payment
                        class="flex-1 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 rounded-lg text-sm font-bold text-white hover:bg-indigo-700 shadow-md">
                        Confirm & Notify
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Review modal --}}
    <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Rate & Review</h3>
                    <button type="button" data-close-review class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="reviewForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="reviewServiceRequestId" name="service_request_id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex space-x-1 justify-center">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" data-set-rating="{{ $i }}"
                                    class="star-button text-3xl text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors">★</button>
                            @endfor
                        </div>
                        <input type="hidden" id="rating" name="rating" required>
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                        <textarea id="comment" name="comment" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="How was your experience?"></textarea>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="button" data-close-review
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="serviceRequestsIndexConfig"
        data-default-status-tab="{{ $defaultStatusTab }}"
        data-review-url="{{ url('/reviews') }}"
        data-report-url-template="{{ url('/service-requests/__ID__/report-issue') }}"
        data-payment-url-template="{{ url('/service-requests/__ID__/buyer-confirm-payment') }}"
        data-success-message="{{ session('success', '') }}"
        data-error-message="{{ $errors->any() ? 'Please check your input or file format.' : '' }}"></div>
    @push('scripts')
        <script src="{{ asset('js/nonadmin-service-requests-index.js') }}"></script>
    @endpush
</x-app-layout>
