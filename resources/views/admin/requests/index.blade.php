@extends('admin.layout')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Service Request Management</h1>
                <p class="text-sm text-gray-500 mt-1">Monitor transactions, track status, and resolve disputes.</p>
            </div>

            {{-- Filters & Search Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('admin.requests.index') }}"
                    class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                    {{-- Search Input --}}
                    <div class="md:col-span-4">
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Requester, Provider or Service..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div class="md:col-span-3">
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="disputed" {{ request('status') == 'disputed' ? 'selected' : '' }}
                                class="text-red-600 font-bold">⚠ Disputed</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>

                    {{-- Category Filter --}}
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Service
                            Category</label>
                        <select name="category"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">All Categories</option>
                            @foreach ($categories ?? [] as $category)
                                <option value="{{ $category->hc_id }}"
                                    {{ request('category') == $category->hc_id ? 'selected' : '' }}>
                                    {{ $category->hc_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Buttons --}}
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg text-sm transition-colors shadow-sm">
                            Filter
                        </button>
                        <a href="{{ route('admin.requests.index') }}"
                            class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition-colors border border-gray-200"
                            title="Reset Filters">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Container --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Request
                                    Details</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Parties
                                    Involved</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Schedule</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($requests as $request)
                                <tr
                                    class="hover:bg-gray-50/50 transition-colors {{ $request->hsr_status === 'disputed' ? 'bg-red-50/30' : '' }}">

                                    {{-- Request Details --}}
                                    <td class="py-4 px-6">
                                        @php
                                            $selectedPackage = $request->hsr_selected_package;
                                            $selectedPackageLabel = is_array($selectedPackage)
                                                ? implode(', ', array_filter($selectedPackage))
                                                : ($selectedPackage ?? '');

                                            $selectedDates = $request->hsr_selected_dates;
                                            $selectedDateValues = is_array($selectedDates)
                                                ? array_values(array_filter($selectedDates))
                                                : (filled($selectedDates) ? [$selectedDates] : []);
                                            $firstSelectedDate = $selectedDateValues[0] ?? null;
                                        @endphp
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-lg">
                                                {{ substr($request->studentService->hss_title, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ Str::limit($request->studentService->hss_title, 25) }}</div>
                                                <div class="text-xs text-gray-500">
                                                    <span
                                                        class="capitalize text-indigo-600 font-medium">{{ $selectedPackageLabel !== '' ? $selectedPackageLabel : '—' }}</span>
                                                    • RM {{ number_format($request->hsr_offered_price, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Parties --}}
                                    <td class="py-4 px-6">
                                        <div class="flex flex-col gap-1">
                                            <div class="text-xs flex items-center gap-2">
                                                <span class="text-gray-400 w-12">From:</span>
                                                <span
                                                    class="font-medium text-gray-900">{{ $request->requester->hu_name }}</span>
                                            </div>
                                            <div class="text-xs flex items-center gap-2">
                                                <span class="text-gray-400 w-12">To:</span>
                                                <span
                                                    class="font-medium text-gray-900">{{ $request->provider->hu_name }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Schedule --}}
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-gray-900">
                                            {{ $firstSelectedDate ? \Carbon\Carbon::parse($firstSelectedDate)->format('d M Y') : 'Not set' }}
                                        </div>
                                        @if (count($selectedDateValues) > 1)
                                            <div class="text-xs text-indigo-600">
                                                +{{ count($selectedDateValues) - 1 }} more date(s)
                                            </div>
                                        @endif
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-4 px-6">
                                        @php
                                            $statusStyles = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'completed' => 'bg-green-100 text-green-800 border-green-200',
                                                'disputed' => 'bg-red-100 text-red-800 border-red-200 animate-pulse',
                                                'cancelled' => 'bg-gray-100 text-gray-600 border-gray-200',
                                                'rejected' => 'bg-gray-100 text-gray-600 border-gray-200',
                                            ];
                                            $style = $statusStyles[$request->hsr_status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $style }} capitalize">
                                            {{ str_replace('_', ' ', $request->hsr_status) }}
                                        </span>
                                        @if ($request->hsr_status === 'disputed')
                                            <div class="text-[10px] text-red-600 font-medium mt-1">Admin Action Required
                                            </div>
                                        @endif
                                    </td>

                                    {{-- ACTIONS COLUMN --}}
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- View Details --}}
                                            <button
                                                onclick='openViewModal(@json($request), @json($request->studentService), @json($request->requester), @json($request->provider))'
                                                class="text-gray-400 hover:text-indigo-600 p-1">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            {{-- DISPUTE BUTTON --}}
                                            @if ($request->hsr_status === 'disputed')
                                                @php
                                                    // --- NEW: FETCH REPORTER INFO ---
                                                    $reporterName = 'Unknown';
                                                    $reporterRole = 'System';

                                                    if ($request->hsr_reported_by) {
                                                        // Find the user by the reported_by ID
                                                        $reporterUser = \App\Models\User::find($request->hsr_reported_by);
                                                        if ($reporterUser) {
                                                            $reporterName = $reporterUser->hu_name;

                                                            // Determine Role
                                                            if ($request->hsr_reported_by == $request->hsr_requester_id) {
                                                                $reporterRole = 'Buyer';
                                                            } elseif ($request->hsr_reported_by == $request->hsr_provider_id) {
                                                                $reporterRole = 'Seller';
                                                            } else {
                                                                $reporterRole = 'Admin';
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                <button
                                                    onclick="openDisciplineModal(
                                                '{{ $request->hsr_id }}', 
                                                '{{ addslashes($request->hsr_dispute_reason) }}', 
                                                { id: '{{ $request->requester->hu_id }}', name: '{{ $request->requester->hu_name }}', warnings: '{{ $request->requester->hu_warning_count }}', role: '{{ $request->requester->hu_role }}' },
                                                { id: '{{ $request->provider->hu_id }}', name: '{{ $request->provider->hu_name }}', warnings: '{{ $request->provider->hu_warning_count }}', role: '{{ $request->provider->hu_role }}' },
                                                { name: '{{ $reporterName }}', role: '{{ $reporterRole }}' } 
                                            )"
                                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1 transition-colors">
                                                    <i class="fa-solid fa-gavel"></i> Resolve
                                                </button>
                                            @endif

                                            {{-- Delete --}}
                                            <form action="{{ route('admin.requests.destroy', $request->hsr_id) }}"
                                                method="POST" onsubmit="return confirm('Delete?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 p-1"><i
                                                        class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($requests->isEmpty())
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fa-regular fa-folder-open text-3xl mb-2 text-gray-300"></i>
                                            <p>No service requests found matching your filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($requests->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $requests->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- VIEW DETAIL MODAL --}}
    <div id="viewDetailModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Request Details</h3>
                    <p class="text-xs text-gray-500">ID: #<span id="viewId"></span></p>
                </div>
                <button onclick="closeViewModal()"
                    class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-1 shadow-sm border border-gray-200">
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
                <button onclick="closeViewModal()"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition">Close</button>
            </div>
        </div>
    </div>

    {{-- DISPUTE RESOLUTION MODAL --}}
    <div id="disciplineModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden z-50 flex items-start sm:items-center justify-center backdrop-blur-sm p-3 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-hidden flex flex-col">

            <div class="bg-gray-900 px-5 sm:px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-red-500"></i> Resolve Dispute
                </h3>
                <button onclick="closeDisciplineModal()" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="p-5 sm:p-6 overflow-y-auto">
                <div class="mb-4 bg-red-50 border border-red-100 p-3 rounded-xl">
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
                        This is the reporter claim only. Verify evidence (payment proof, chat timeline, work delivery) before assigning fault.
                    </p>
                </div>

                <div class="mb-4 rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-3 text-xs text-indigo-900">
                    <div class="font-bold uppercase tracking-wide mb-2">How this works</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>1) Pick Buyer or Seller card based on who is at fault.</div>
                        <div>2) Write message/reason in the note box.</div>
                        <div>3) Click Warn or Suspend/Blacklist.</div>
                        <div>4) Or close without penalty using Resume/Complete options below.</div>
                    </div>
                </div>

                <p class="text-sm text-gray-500 mb-4 text-center">Who is at fault? Choose a card and action.</p>

                <form id="disciplineForm" method="POST" action="">
    @csrf
    <input type="hidden" name="action_type" id="inputActionType"> 
    <input type="hidden" name="target_user_id" id="inputTargetUserId">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        {{-- 1. REQUESTER CARD --}}
        <div class="border border-gray-200 rounded-xl p-3.5 hover:border-gray-300 transition-colors relative group">
            <div class="absolute top-2 right-2 text-xs font-bold text-gray-400">Buyer</div>
            <h4 id="discReqName" class="font-bold text-gray-900 text-base"></h4>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                <span class="bg-gray-100 px-2 py-0.5 rounded">ID: <span id="discReqId"></span></span>
                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> <span id="discReqWarnings">0</span>/{{ config('moderation.user_warning_limit', 3) }} Warnings
                </span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="submitDiscipline('warn', 'requester')" title="Send warning email, increase warning count, and resume request"
                    class="py-2 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 rounded-lg text-xs sm:text-sm font-bold transition-colors inline-flex items-center justify-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> Warn
                </button>
                <button type="button" onclick="submitDiscipline('suspend_or_blacklist', 'requester')" title="Community becomes blacklisted, others suspended; request cancelled"
                    class="py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs sm:text-sm font-bold transition-colors inline-flex items-center justify-center gap-1">
                    <i class="fa-solid fa-ban"></i> Suspend / Blacklist
                </button>
            </div>
        </div>

        {{-- 2. PROVIDER CARD --}}
        <div class="border border-gray-200 rounded-xl p-3.5 hover:border-gray-300 transition-colors relative group">
            <div class="absolute top-2 right-2 text-xs font-bold text-gray-400">Seller</div>
            <h4 id="discProvName" class="font-bold text-gray-900 text-base"></h4>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                <span class="bg-gray-100 px-2 py-0.5 rounded">ID: <span id="discProvId"></span></span>
                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> <span id="discProvWarnings">0</span>/{{ config('moderation.user_warning_limit', 3) }} Warnings
                </span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="submitDiscipline('warn', 'provider')" title="Send warning email, increase warning count, and resume request"
                    class="py-2 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 rounded-lg text-xs sm:text-sm font-bold transition-colors inline-flex items-center justify-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> Warn
                </button>
                <button type="button" onclick="submitDiscipline('suspend_or_blacklist', 'provider')" title="Community becomes blacklisted, others suspended; request cancelled"
                    class="py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs sm:text-sm font-bold transition-colors inline-flex items-center justify-center gap-1">
                    <i class="fa-solid fa-ban"></i> Suspend / Blacklist
                </button>
            </div>
        </div>
    </div>

    {{-- Admin Note Input (FIXED ID HERE) --}}
    <div class="mt-4">
        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
            Warning Message / Restriction Reason <span class="text-red-500">*</span>
        </label>
        <textarea 
            name="admin_note" 
            id="adminNoteInput"  {{-- THIS WAS MISSING --}}
            rows="3" 
            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-red-500 outline-none" 
            placeholder="Write the warning message or suspension/blacklist reason..." 
            required></textarea>
        <p class="text-[11px] text-gray-500 mt-1">Warn: request resumes. Suspend/Blacklist: request is cancelled.</p>
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

    <script>
        function openViewModal(req, service, requester, provider) {
            const reqId = req.hsr_id ?? req.id;
            const serviceTitle = service?.hss_title ?? service?.title ?? 'Unknown Service';
            const selectedPackage = req.hsr_selected_package ?? req.selected_package;
            const packageLabel = Array.isArray(selectedPackage)
                ? (selectedPackage[0] ?? 'Custom')
                : (selectedPackage ?? 'Custom');
            const offeredPrice = req.hsr_offered_price ?? req.offered_price ?? 0;
            const status = req.hsr_status ?? req.status ?? 'pending';
            const requesterName = requester?.hu_name ?? requester?.name ?? 'Unknown';
            const providerName = provider?.hu_name ?? provider?.name ?? 'Unknown';
            const requesterEmail = requester?.hu_email ?? requester?.email ?? '-';
            const providerEmail = provider?.hu_email ?? provider?.email ?? '-';
            const requesterPhone = requester?.hu_phone ?? requester?.phone ?? null;
            const providerPhone = provider?.hu_phone ?? provider?.phone ?? null;
            const selectedDates = req.hsr_selected_dates ?? req.selected_dates;
            const dateLabel = Array.isArray(selectedDates) ? (selectedDates[0] ?? null) : selectedDates;
            const startTime = req.hsr_start_time ?? req.start_time;
            const endTime = req.hsr_end_time ?? req.end_time;
            const message = req.hsr_message ?? req.message;
            const disputeReason = req.hsr_dispute_reason ?? req.dispute_reason;

            document.getElementById('viewId').textContent = reqId;
            document.getElementById('viewServiceTitle').textContent = serviceTitle;
            document.getElementById('viewPackage').textContent = packageLabel;
            document.getElementById('viewPrice').textContent = parseFloat(offeredPrice || 0).toFixed(2);

            const statusSpan = document.getElementById('viewStatus');
            statusSpan.textContent = status.replace('_', ' ');
            statusSpan.className = `inline-block mt-1 px-3 py-1 rounded-full text-xs font-bold capitalize border `;
            if (status === 'completed') statusSpan.classList.add('bg-green-100', 'text-green-700', 'border-green-200');
            else if (status === 'pending') statusSpan.classList.add('bg-yellow-100', 'text-yellow-700',
                'border-yellow-200');
            else if (status === 'disputed') statusSpan.classList.add('bg-red-100', 'text-red-700', 'border-red-200');
            else statusSpan.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');

            document.getElementById('viewReqAvatar').textContent = requesterName.charAt(0);
            document.getElementById('viewReqName').textContent = requesterName;
            document.getElementById('viewReqEmail').textContent = requesterEmail;
            document.getElementById('viewReqPhone').textContent = requesterPhone || 'No Phone';

            document.getElementById('viewProvAvatar').textContent = providerName.charAt(0);
            document.getElementById('viewProvName').textContent = providerName;
            document.getElementById('viewProvEmail').textContent = providerEmail;
            document.getElementById('viewProvPhone').textContent = providerPhone || 'No Phone';

            document.getElementById('viewDate').textContent = dateLabel || 'Flexible';
            document.getElementById('viewTime').textContent = (startTime || '??') + ' - ' + (endTime || '??');
            document.getElementById('viewMessage').textContent = message || 'No additional message.';

            const disputeDiv = document.getElementById('viewDisputeSection');
            if (status === 'disputed') {
                disputeDiv.classList.remove('hidden');
                document.getElementById('viewDisputeReason').textContent = disputeReason || 'No reason provided';
            } else {
                disputeDiv.classList.add('hidden');
            }
            document.getElementById('viewDetailModal').classList.remove('hidden');
        }

        function closeViewModal() {
            document.getElementById('viewDetailModal').classList.add('hidden');
        }

        let currentRequesterId = null;
        let currentProviderId = null;
        let currentRequesterRole = null;
        let currentProviderRole = null;
        let currentRequesterWarnings = 0;
        let currentProviderWarnings = 0;

        function openDisciplineModal(requestId, reason, requester, provider, reporter) {
            document.getElementById('discModalReason').textContent = reason || 'No reason provided.';

            if (reporter) {
                document.getElementById('discReporterName').textContent = reporter.name;
                document.getElementById('discReporterRole').textContent = `(${reporter.role})`;
            } else {
                document.getElementById('discReporterName').textContent = 'Unknown';
                document.getElementById('discReporterRole').textContent = '';
            }

            document.getElementById('discReqName').textContent = requester.name;
            document.getElementById('discReqId').textContent = requester.id;
            document.getElementById('discReqWarnings').textContent = requester.warnings;
            currentRequesterId = requester.id;
            currentRequesterRole = requester.role;
            currentRequesterWarnings = parseInt(requester.warnings || 0, 10);

            document.getElementById('discProvName').textContent = provider.name;
            document.getElementById('discProvId').textContent = provider.id;
            document.getElementById('discProvWarnings').textContent = provider.warnings;
            currentProviderId = provider.id;
            currentProviderRole = provider.role;
            currentProviderWarnings = parseInt(provider.warnings || 0, 10);

            const baseUrl = "{{ url('admin/requests') }}/" + requestId + "/resolve";
            document.getElementById('disciplineForm').action = baseUrl;
            document.getElementById('dismissForm').action = baseUrl;
            document.getElementById('resumeForm').action = baseUrl;
            document.getElementById('completePaidForm').action = baseUrl;
            document.getElementById('actionPreview').classList.add('hidden');
            document.getElementById('actionPreview').textContent = '';

            document.getElementById('disciplineModal').classList.remove('hidden');
        }

        function closeDisciplineModal() {
            document.getElementById('disciplineModal').classList.add('hidden');
            document.getElementById('actionPreview').classList.add('hidden');
        }

        function submitDiscipline(action, target) {
            // 1. VALIDATION: Check if message is written
            const noteInput = document.getElementById('adminNoteInput');
            const noteValue = noteInput.value.trim();

            if (!noteValue) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Message Required',
                    text: 'Please write a warning message or reason in the text box before proceeding.',
                    confirmButtonColor: '#3085d6',
                }).then(() => {
                    // Focus the textarea so they know where to write
                    setTimeout(() => noteInput.focus(), 300);
                });
                return; // Stop execution
            }

            // 2. Identify Target
            const targetId = (target === 'requester') ? currentRequesterId : currentProviderId;
            const targetName = (target === 'requester') ? document.getElementById('discReqName').textContent : document
                .getElementById('discProvName').textContent;
            const targetWarnings = (target === 'requester') ? currentRequesterWarnings : currentProviderWarnings;
            const warningLimit = {{ config('moderation.user_warning_limit', 3) }};

            if (action === 'warn' && targetWarnings >= warningLimit) {
                Swal.fire({
                    icon: 'info',
                    title: 'Warning Limit Reached',
                    text: `This user is already at ${warningLimit}/${warningLimit}. Use Suspend/Blacklist for the next action.`,
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            const targetRole = (target === 'requester') ? currentRequesterRole : currentProviderRole;
            let confirmMsg;
            let previewMsg;
            const previewEl = document.getElementById('actionPreview');
            if (action === 'suspend_or_blacklist') {
                const penaltyLabel = (targetRole === 'community') ? 'BLACKLIST' : 'SUSPEND';
                previewMsg = (targetRole === 'community')
                    ? 'Preview: Sends blacklist email, sets account to blacklisted, and cancels this request.'
                    : 'Preview: Sends suspension email, sets account to suspended, and cancels this request.';
                confirmMsg = `Are you sure you want to ${penaltyLabel} ${targetName}?\n${previewMsg}`;
            } else {
                previewMsg = 'Preview: Sends warning email, increments warning count, and resumes this request to Waiting Payment.';
                confirmMsg = `Send this warning to ${targetName}?\n${previewMsg}`;
            }
            previewEl.textContent = previewMsg;
            previewEl.className = 'text-[11px] mt-2 rounded-md px-2 py-1 border bg-indigo-50 border-indigo-200 text-indigo-800';
            previewEl.classList.remove('hidden');

            // 3. Confirm and Submit
            if (confirm(confirmMsg)) {
                document.getElementById('inputActionType').value = action;
                document.getElementById('inputTargetUserId').value = targetId;
                document.getElementById('disciplineForm').submit();
            }
        }
    </script>
@endsection
