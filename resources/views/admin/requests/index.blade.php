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
                                <td class="py-4 px-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $request->status_style }} capitalize">
                                        {{ $request->status_label }}
                                    </span>
                                    @if ($request->hsr_status === 'disputed')
                                        <div class="text-[10px] text-red-600 font-medium mt-1">Admin Action Required</div>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 px-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" data-request-open-view
                                            data-request='@json($request)'
                                            data-service='@json($request->studentService)'
                                            data-requester='@json($request->requester)'
                                            data-provider='@json($request->provider)'
                                            class="text-blue-500 hover:text-blue-700 transition-colors duration-300" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        @if ($request->hsr_status === 'disputed')
                                            <button type="button" data-request-open-discipline
                                                data-request-id="{{ $request->hsr_id }}"
                                                data-dispute-reason='@json($request->hsr_dispute_reason)'
                                                data-requester-payload='@json($request->requester_payload)'
                                                data-provider-payload='@json($request->provider_payload)'
                                                data-reporter-payload='@json($request->reporter_payload)'
                                                class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded-lg text-xs font-bold transition-colors">
                                                <i class="fa-solid fa-gavel"></i> Review
                                            </button>
                                        @endif

                                        <form action="{{ route('admin.requests.destroy', $request->hsr_id) }}" method="POST" data-confirm-message="Delete?" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors duration-300" title="Delete">
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
            <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 z-10 transition-colors duration-300"
                 style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                <div>
                    <h3 class="text-lg font-bold transition-colors duration-300" style="color: var(--text-primary);">Request Details</h3>
                    <p class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">ID: #<span id="viewId"></span></p>
                </div>
                <button type="button" data-request-close-view
                    class="rounded-full p-1 shadow-sm border transition-all duration-300"
                    style="color: var(--text-secondary); background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="flex justify-between items-start bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                    <div>
                        <h4 id="viewServiceTitle" class="font-bold text-gray-900 text-lg mb-1"></h4>
                        <span id="viewPackage"
                            class="text-xs font-semibold bg-white px-2 py-1 rounded border border-indigo-200 text-indigo-700 uppercase tracking-wide"></span>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-indigo-700">RM <span id="viewPrice"></span></p>
                        <span id="viewStatus"
                            class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold capitalize bg-white border"></span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-3">Requester (Buyer)</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold"
                                id="viewReqAvatar"></div>
                            <div>
                                <p id="viewReqName" class="font-bold text-sm text-gray-900"></p>
                                <p id="viewReqEmail" class="text-xs text-gray-500"></p>
                                <p id="viewReqPhone" class="text-xs text-gray-500 mt-0.5"></p>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-3">Provider (Seller)</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold"
                                id="viewProvAvatar"></div>
                            <div>
                                <p id="viewProvName" class="font-bold text-sm text-gray-900"></p>
                                <p id="viewProvEmail" class="text-xs text-gray-500"></p>
                                <p id="viewProvPhone" class="text-xs text-gray-500 mt-0.5"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-2">Schedule</p>
                        <div class="text-sm font-medium text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <div class="mb-2"><i class="fa-regular fa-calendar text-gray-400 mr-2"></i><span
                                    id="viewDate"></span></div>
                            <div><i class="fa-regular fa-clock text-gray-400 mr-2"></i><span id="viewTime"></span></div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-2">Client Message</p>
                        <div
                            class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-sm text-gray-700 italic min-h-[80px]">
                            "<span id="viewMessage"></span>"</div>
                    </div>
                </div>
                <div id="viewDisputeSection" class="hidden mt-4 bg-red-50 border border-red-200 rounded-xl p-4">
                    <h4 class="text-sm font-bold text-red-700 flex items-center gap-2 mb-2"><i
                            class="fa-solid fa-circle-exclamation"></i> Dispute Reason</h4>
                    <p id="viewDisputeReason" class="text-sm text-red-600"></p>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 text-right">
                <button type="button" data-request-close-view
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition">Close</button>
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
