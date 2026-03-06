@extends('admin.layout')

@section('content')
    <div class="px-4 sm:px-6 py-4">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Service Request Management</h1>
        </div>

        <div class="p-4 rounded-lg shadow-xl mb-6 border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <form method="GET" action="{{ route('admin.requests.index') }}" class="flex flex-wrap gap-3 sm:gap-4">

                {{-- Search Input --}}
                <div class="flex-1 min-w-0 w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by requester, provider or service..."
                        class="w-full px-4 py-2 border rounded-lg transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                        style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                {{-- Status Filter --}}
                <div class="w-full sm:w-auto">
                    <select name="status"
                        class="w-full sm:w-auto px-4 py-2 border rounded-lg transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                        style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="disputed" {{ request('status') == 'disputed' ? 'selected' : '' }}>⚠ Disputed</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                {{-- Category Filter --}}
                <div class="w-full sm:w-auto">
                    <select name="category"
                        class="w-full sm:w-auto px-4 py-2 border rounded-lg transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                        style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                        <option value="">All Categories</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->hc_id }}"
                                {{ request('category') == $category->hc_id ? 'selected' : '' }}>
                                {{ $category->hc_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-lg hover:from-cyan-400 hover:to-blue-500 transition-all duration-300">
                    Search
                </button>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="p-4 rounded-lg shadow-xl border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr style="background-color: var(--bg-tertiary);">
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Request Details</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Parties Involved</th>
                            <th class="py-3 px-3 text-center text-xs font-medium" style="color: var(--text-secondary);">Schedule</th>
                            <th class="py-3 px-3 text-center text-xs font-medium" style="color: var(--text-secondary);">Status</th>
                            <th class="py-3 px-3 text-center text-xs font-medium" style="color: var(--text-secondary);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($requests as $request)
                        <tr class="border-b transition-all duration-300 {{ $request->hsr_status === 'disputed' ? 'bg-red-50' : '' }}" style="border-color: var(--border-color);">

                            {{-- Request Details --}}
                            <td class="py-4 px-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-cyan-100 flex items-center justify-center text-cyan-700 font-bold text-sm">
                                                {{ $request->service_initial }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                                                    {{ Str::limit($request->studentService->hss_title, 25) }}</div>
                                                <div class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">
                                                    <span class="capitalize text-cyan-600 font-medium">{{ $request->selected_package_label !== '' ? $request->selected_package_label : '—' }}</span>
                                                    • RM {{ number_format($request->hsr_offered_price, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                {{-- Parties --}}
                                <td class="py-4 px-3">
                                    <div class="flex flex-col gap-1">
                                        <div class="text-xs flex items-center gap-2">
                                            <span class="w-12 transition-colors duration-300" style="color: var(--text-muted);">From:</span>
                                            <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $request->requester->hu_name }}</span>
                                        </div>
                                        <div class="text-xs flex items-center gap-2">
                                            <span class="w-12 transition-colors duration-300" style="color: var(--text-muted);">To:</span>
                                            <span class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $request->provider->hu_name }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Schedule --}}
                                <td class="py-4 px-3">
                                    <div class="text-sm transition-colors duration-300" style="color: var(--text-primary);">
                                        {{ $request->first_selected_date_display }}
                                    </div>
                                    @if (count($request->selected_date_values) > 1)
                                        <div class="text-xs text-cyan-600">
                                            +{{ count($request->selected_date_values) - 1 }} more date(s)
                                        </div>
                                    @endif
                                    <div class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">
                                        {{ $request->created_at_human }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="py-4 px-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $request->status_style }} capitalize">
                                        {{ $request->status_label }}
                                    </span>
                                    @if ($request->hsr_status === 'disputed')
                                        <div class="text-[10px] text-red-600 font-medium mt-1">Admin Action Required</div>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 px-3">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        <button type="button" data-request-open-view
                                            data-request='@json($request)'
                                            data-service='@json($request->studentService)'
                                            data-requester='@json($request->requester)'
                                            data-provider='@json($request->provider)'
                                            class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-semibold transition-all duration-200" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        @if ($request->hsr_status === 'disputed')
                                            <button type="button" data-request-open-discipline
                                                data-request-id="{{ $request->hsr_id }}"
                                                data-dispute-reason='@json($request->hsr_dispute_reason)'
                                                data-requester-payload='@json($request->requester_payload)'
                                                data-provider-payload='@json($request->provider_payload)'
                                                data-reporter-payload='@json($request->reporter_payload)'
                                                class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-semibold transition-all duration-200" title="Review Dispute">
                                                <i class="fa-solid fa-gavel"></i>
                                            </button>
                                        @endif

                                        <form action="{{ route('admin.requests.destroy', $request->hsr_id) }}" method="POST" data-confirm-message="Delete?" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-semibold transition-all duration-200" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if ($requests->isEmpty())
                            <tr>
                                <td colspan="5" class="py-8 text-center transition-colors duration-300" style="color: var(--text-secondary);">
                                    No service requests found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if ($requests->hasPages())
            <div class="mt-4 px-4">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- VIEW DETAIL MODAL --}}
    <div id="viewDetailModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4">
        <div class="rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] overflow-y-auto transition-all duration-300"
             style="background-color: var(--bg-primary);">

            {{-- Gradient Header --}}
            <div class="relative bg-gradient-to-r from-cyan-600 to-indigo-700 px-6 py-5 flex justify-between items-center sticky top-0 z-10">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-20 flex items-center justify-center">
                            <i class="fa-solid fa-file-contract text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Request Details</h3>
                    </div>
                    <p class="text-cyan-200 text-xs ml-13 pl-1">Request ID: #<span id="viewId"></span></p>
                </div>
                <button type="button" data-request-close-view
                    class="rounded-full p-2 bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-200 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">

                {{-- Service Overview Card --}}
                <div class="rounded-xl overflow-hidden border transition-colors duration-300" style="border-color: var(--border-color);">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 flex items-center gap-2">
                        <i class="fa-solid fa-briefcase text-white text-sm"></i>
                        <span class="text-white text-sm font-semibold uppercase tracking-wide">Service Overview</span>
                    </div>
                    <div class="p-4 flex justify-between items-start transition-colors duration-300" style="background-color: var(--bg-secondary);">
                        <div>
                            <h4 id="viewServiceTitle" class="font-bold text-lg mb-1 transition-colors duration-300" style="color: var(--text-primary);"></h4>
                            <span id="viewPackage"
                                class="text-xs font-semibold px-2 py-1 rounded-md border uppercase tracking-wide transition-colors duration-300"
                                style="background-color: var(--bg-tertiary); color: var(--text-secondary); border-color: var(--border-color);"></span>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-indigo-500">RM <span id="viewPrice"></span></p>
                            <span id="viewStatus"
                                class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold capitalize border"></span>
                        </div>
                    </div>
                </div>

                {{-- Parties Involved --}}
                <div class="rounded-xl overflow-hidden border transition-colors duration-300" style="border-color: var(--border-color);">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-4 py-3 flex items-center gap-2">
                        <i class="fa-solid fa-users text-white text-sm"></i>
                        <span class="text-white text-sm font-semibold uppercase tracking-wide">Parties Involved</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x transition-colors duration-300"
                         style="background-color: var(--bg-secondary); divide-color: var(--border-color);">
                        <div class="p-4">
                            <p class="text-xs font-bold uppercase mb-3 transition-colors duration-300" style="color: var(--text-muted);">
                                <i class="fa-solid fa-user-graduate mr-1 text-blue-400"></i> Requester (Buyer)
                            </p>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold flex-shrink-0"
                                    id="viewReqAvatar"></div>
                                <div>
                                    <p id="viewReqName" class="font-bold text-sm transition-colors duration-300" style="color: var(--text-primary);"></p>
                                    <p id="viewReqEmail" class="text-xs transition-colors duration-300" style="color: var(--text-secondary);"></p>
                                    <p id="viewReqPhone" class="text-xs mt-0.5 transition-colors duration-300" style="color: var(--text-muted);"></p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="text-xs font-bold uppercase mb-3 transition-colors duration-300" style="color: var(--text-muted);">
                                <i class="fa-solid fa-handshake mr-1 text-emerald-400"></i> Provider (Seller)
                            </p>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold flex-shrink-0"
                                    id="viewProvAvatar"></div>
                                <div>
                                    <p id="viewProvName" class="font-bold text-sm transition-colors duration-300" style="color: var(--text-primary);"></p>
                                    <p id="viewProvEmail" class="text-xs transition-colors duration-300" style="color: var(--text-secondary);"></p>
                                    <p id="viewProvPhone" class="text-xs mt-0.5 transition-colors duration-300" style="color: var(--text-muted);"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Schedule & Message --}}
                <div class="rounded-xl overflow-hidden border transition-colors duration-300" style="border-color: var(--border-color);">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-white text-sm"></i>
                        <span class="text-white text-sm font-semibold uppercase tracking-wide">Schedule & Message</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x transition-colors duration-300"
                         style="background-color: var(--bg-secondary); divide-color: var(--border-color);">
                        <div class="p-4 md:col-span-1">
                            <p class="text-xs font-bold uppercase mb-3 transition-colors duration-300" style="color: var(--text-muted);">Schedule</p>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm transition-colors duration-300" style="color: var(--text-primary);">
                                    <i class="fa-regular fa-calendar w-4 text-center text-emerald-500"></i>
                                    <span id="viewDate"></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm transition-colors duration-300" style="color: var(--text-primary);">
                                    <i class="fa-regular fa-clock w-4 text-center text-teal-500"></i>
                                    <span id="viewTime"></span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 md:col-span-2">
                            <p class="text-xs font-bold uppercase mb-3 transition-colors duration-300" style="color: var(--text-muted);">Client Message</p>
                            <div class="rounded-lg p-3 border text-sm italic min-h-[70px] transition-colors duration-300"
                                 style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-secondary);">
                                "<span id="viewMessage"></span>"
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dispute Section (shown only when disputed) --}}
                <div id="viewDisputeSection" class="hidden rounded-xl overflow-hidden border border-red-300">
                    <div class="bg-gradient-to-r from-red-500 to-rose-600 px-4 py-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-white text-sm"></i>
                        <span class="text-white text-sm font-semibold uppercase tracking-wide">Dispute Reason</span>
                    </div>
                    <div class="bg-red-50 p-4">
                        <p id="viewDisputeReason" class="text-sm text-red-700 leading-relaxed"></p>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t flex justify-end transition-colors duration-300"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <button type="button" data-request-close-view
                    class="px-5 py-2 rounded-lg border font-medium text-sm transition-all duration-200 hover:opacity-80"
                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- DISPUTE RESOLUTION MODAL --}}
    <div id="disciplineModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden z-50 flex items-start sm:items-center justify-center backdrop-blur-sm p-3 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-hidden flex flex-col">

            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-red-500"></i> Resolve Dispute & Discipline
                </h3>
                <button type="button" data-request-close-discipline class="text-gray-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="p-5 sm:p-6 overflow-y-auto">
                <div class="mb-6 bg-red-50 border border-red-100 p-4 rounded-xl">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-xs font-bold text-red-500 uppercase tracking-wide">Reported Statement</h4>
                        <div
                            class="flex items-center gap-1 text-xs bg-white border border-red-100 px-2 py-1 rounded-md shadow-sm">
                            <span class="text-gray-400">Reported by:</span>
                            <span class="font-bold text-gray-800" id="discReporterName">...</span>
                            <span class="text-xs text-red-500 font-semibold uppercase tracking-wider"
                                id="discReporterRole">...</span>
                        </div>
                    </div>
                    <p id="discModalReason" class="text-gray-800 text-sm italic font-medium leading-relaxed">...</p>
                    <p class="text-[11px] text-red-700 mt-2">
                        This is the reporter claim only. Verify payment proof, timeline, and delivery evidence before assigning fault.
                    </p>
                </div>

                <p class="text-sm text-gray-500 mb-4 text-center">Who is at fault? Select a user and choose action.</p>

                <form id="disciplineForm" method="POST" action="">
    @csrf
    <input type="hidden" name="action_type" id="inputActionType"> 
    <input type="hidden" name="target_user_id" id="inputTargetUserId">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- 1. REQUESTER CARD --}}
        <div class="border border-gray-200 rounded-xl p-4 hover:border-gray-300 transition-colors relative group">
            <div class="absolute top-2 right-2 text-xs font-bold text-gray-400">Buyer</div>
            <h4 id="discReqName" class="font-bold text-gray-900 text-lg"></h4>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                <span class="bg-gray-100 px-2 py-0.5 rounded">ID: <span id="discReqId"></span></span>
                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> <span id="discReqWarnings">0</span>/{{ $warningLimit }} Warnings
                </span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" data-request-discipline-submit data-action="warn" data-target-role="requester" class="text-yellow-500 hover:text-yellow-400 transition border rounded-lg py-2" title="Warn"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Warn</button>
                <button type="button" data-request-discipline-submit data-action="suspend_or_blacklist" data-target-role="requester" class="text-red-600 hover:text-red-900 transition border rounded-lg py-2" title="Suspend or Blacklist"><i class="fa-solid fa-ban mr-1"></i> Suspend / Blacklist</button>
            </div>
        </div>

        {{-- 2. PROVIDER CARD --}}
        <div class="border border-gray-200 rounded-xl p-4 hover:border-gray-300 transition-colors relative group">
            <div class="absolute top-2 right-2 text-xs font-bold text-gray-400">Seller</div>
            <h4 id="discProvName" class="font-bold text-gray-900 text-lg"></h4>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                <span class="bg-gray-100 px-2 py-0.5 rounded">ID: <span id="discProvId"></span></span>
                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> <span id="discProvWarnings">0</span>/{{ $warningLimit }} Warnings
                </span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" data-request-discipline-submit data-action="warn" data-target-role="provider" class="text-yellow-500 hover:text-yellow-400 transition border rounded-lg py-2" title="Warn"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Warn</button>
                <button type="button" data-request-discipline-submit data-action="suspend_or_blacklist" data-target-role="provider" class="text-red-600 hover:text-red-900 transition border rounded-lg py-2" title="Suspend or Blacklist"><i class="fa-solid fa-ban mr-1"></i> Suspend / Blacklist</button>
            </div>
        </div>
    </div>

    {{-- Admin Note Input (FIXED ID HERE) --}}
    <div class="mt-6">
        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
            Warning Message / Restriction Reason <span class="text-red-500">*</span>
        </label>
        <textarea 
            name="admin_note" 
            id="adminNoteInput"  {{-- THIS WAS MISSING --}}
            rows="2" 
            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-red-500 outline-none" 
            placeholder="Write the warning message or suspension/blacklist reason..." 
            required></textarea>
        <p id="actionPreview" class="hidden text-[11px] mt-2 rounded-md px-2 py-1 border"></p>
    </div>
</form>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-3">
                        <form action="" method="POST" id="resumeForm">
                            @csrf
                            <input type="hidden" name="action_type" value="resume">
                            <button type="submit"
                                class="w-full py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-800 rounded-lg text-xs font-bold transition-colors">
                                Close Without Penalty (Resume to Waiting Payment)
                            </button>
                        </form>
                        <form action="" method="POST" id="completePaidForm">
                            @csrf
                            <input type="hidden" name="action_type" value="complete_paid">
                            <button type="submit"
                                class="w-full py-2 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg text-xs font-bold transition-colors">
                                Close Without Penalty (Mark Completed & Paid)
                            </button>
                        </form>
                    </div>
                    <div class="text-center">
                    <form action="" method="POST" id="dismissForm">
                        @csrf
                        <input type="hidden" name="action_type" value="dismiss">
                        <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 underline">Dismiss dispute
                            without penalty (Mark as Cancelled)</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <div id="adminModuleRequestsConfig"
        data-warning-limit="{{ $warningLimit }}"
        data-resolve-base-url="{{ url('admin/requests') }}"></div>
    <script src="{{ asset('js/admin-requests.js') }}"></script>
@endsection
