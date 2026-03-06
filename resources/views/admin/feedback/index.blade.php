@extends('admin.layout')

@section('content')

<div class="px-2 sm:px-4 lg:px-6 py-4">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 gap-4">
        <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Feedback & Moderation</h1>
    </div>
    <p class="text-xs mb-2 transition-colors duration-300" style="color: var(--text-muted);">
        Feedback moderation covers student, community and helper accounts. Warning is shared, final action is role-based.
    </p>
    <p class="text-xs mb-6 transition-colors duration-300" style="color: var(--text-muted);">
        At {{ $userWarningLimit }}/{{ $userWarningLimit }} warnings: helper = {{ $finalActions['helper'] }}, student/community = {{ $finalActions['student'] }}.
    </p>

    {{-- Search Bar --}}
    <div class="rounded-xl border shadow-sm mb-6 p-4 transition-colors duration-300"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <form method="GET" action="{{ route('admin.feedback.index') }}">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-sm" style="color: var(--text-muted);"></i>
                    </div>
                    <input type="text" name="search" placeholder="Search by name or email..."
                           class="w-full pl-9 pr-4 py-2 border rounded-lg transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                           style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);"
                           value="{{ request('search') }}">
                </div>
                @if ($selectedRole)
                    <input type="hidden" name="role" value="{{ $selectedRole }}">
                @endif
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-lg hover:from-cyan-400 hover:to-blue-500 transition-all duration-300 font-medium">
                    Search
                </button>
            </div>
        </form>
    </div>

    {{-- Role Filter Tabs --}}
    <div class="rounded-xl border shadow-sm mb-6 p-3 transition-colors duration-300 flex flex-wrap gap-2"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <a href="{{ route('admin.feedback.index', request()->except('role')) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2
                {{ $selectedRole === '' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-sm' : 'border hover:opacity-80' }}"
            style="{{ $selectedRole !== '' ? 'background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);' : '' }}">
            All
        </a>
        @foreach ($roleOptions as $role)
            <a href="{{ route('admin.feedback.index', array_merge(request()->except('page', 'role'), ['role' => $role])) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                    {{ $selectedRole === $role ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-sm' : 'border hover:opacity-80' }}"
                style="{{ $selectedRole !== $role ? 'background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);' : '' }}">
                {{ ucfirst($role) }}
            </a>
        @endforeach
    </div>

    <div class="shadow-lg rounded-lg p-4 sm:p-6 border transition-colors duration-300" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[760px]">
            <thead class="transition-colors duration-300" style="background-color: var(--bg-tertiary);">
                <tr class="border-b">
                    <th class="py-3 px-4 text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">User Details</th>
                    <th class="py-3 px-4 text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Reviews</th>
                    <th class="py-3 px-4 text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Warnings</th>
                    <th class="py-3 px-4 text-sm font-medium text-center transition-colors duration-300" style="color: var(--text-secondary);">Status</th>
                    <th class="py-3 px-4 text-sm font-medium text-center transition-colors duration-300" style="color: var(--text-secondary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usersWithReviews as $user)
                    <tr class="border-b transition-colors duration-300 surface-hover">
                        <td class="py-3 px-4 text-sm">
                            <p class="font-bold transition-colors duration-300" style="color: var(--text-primary);">{{ $user->hu_name }}</p>
                            <p class="text-xs transition-colors duration-300" style="color: var(--text-muted);">{{ $user->hu_email }} ({{ $user->hu_role }})</p>
                        </td>
                        
                        <td class="py-3 px-4 text-sm">
                            <span class="font-semibold {{ ($user->reviews_received_avg_rating ?? 0) < 3.0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($user->reviews_received_avg_rating ?? 0, 2) }} / 5.0
                            </span>
                            <span class="text-xs transition-colors duration-300" style="color: var(--text-muted);">({{ $user->reviews_received_count ?? 0 }} reviews)</span>
                            <div class="mt-1">
                                <a href="{{ route('students.profile', $user->hu_id) }}"
                                    class="text-xs text-blue-600 hover:underline">
                                    View Reviews
                                </a>
                            </div>
                        </td>
                        
                        {{-- Warning Count --}}
                        <td class="py-3 px-4 text-sm font-semibold {{ $user->feedback_warning_class }}">
                            {{ $user->hu_warning_count }} / {{ $userWarningLimit }}
                        </td>
                        
                        {{-- Block Status --}}
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $user->feedback_status_badge_class }}">
                                {{ $user->feedback_status_label }}
                            </span>
                        </td>
                        
                        {{-- Actions: Notify/Block --}}
                        <td class="py-3 px-4 align-middle">
                            <div class="flex items-center justify-center">
                                @if ($user->feedback_can_warn)
                                    <button type="button"
                                        data-feedback-open-warning
                                        data-url="{{ route('admin.feedback.warning', $user->hu_id) }}"
                                        data-name="{{ $user->hu_name }}"
                                        data-count="{{ $user->feedback_next_warning_count }}"
                                        data-limit="{{ $userWarningLimit }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg text-xs font-semibold transition-all duration-200">
                                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                        Warn ({{ $user->feedback_next_warning_count }}/{{ $userWarningLimit }})
                                    </button>
                                @elseif ($user->feedback_can_enforce)
                                    <button type="button"
                                        data-feedback-open-enforce
                                        data-url="{{ route('admin.feedback.enforce', $user->hu_id) }}"
                                        data-name="{{ $user->hu_name }}"
                                        data-action="{{ $finalActions[$user->hu_role] }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-semibold transition-all duration-200">
                                        <i class="fa-solid fa-ban text-xs"></i>
                                        {{ $finalActions[$user->hu_role] }}
                                    </button>
                                @elseif ($user->feedback_can_unsuspend)
                                    <button type="button"
                                        data-feedback-open-unsuspend
                                        data-url="{{ route('admin.feedback.unsuspend', $user->hu_id) }}"
                                        data-name="{{ $user->hu_name }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-semibold transition-all duration-200">
                                        <i class="fa-solid fa-rotate-left text-xs"></i>
                                        Unsuspend
                                    </button>
                                @elseif ($user->feedback_can_unblock)
                                    <button type="button"
                                        data-feedback-open-unblock
                                        data-url="{{ route('admin.feedback.unblock', $user->hu_id) }}"
                                        data-name="{{ $user->hu_name }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-semibold transition-all duration-200">
                                        <i class="fa-solid fa-unlock text-xs"></i>
                                        Unblock Seller
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600">
                                        <i class="fa-solid fa-lock text-xs"></i> Locked
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $usersWithReviews->links() }}
        </div>
    </div>
</div>
@endsection

{{-- UNSUSPEND MODAL --}}
<div id="feedbackUnsuspendModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="feedback-unsuspend-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true" id="feedbackUnsuspendBackdrop"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border"
             style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <form id="feedbackUnsuspendForm" method="POST" action="">
                @csrf

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-green-100">
                            <i class="fas fa-rotate-left text-green-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold transition-colors duration-300 mb-1" style="color: var(--text-primary);" id="feedback-unsuspend-modal-title">
                                Unsuspend Account
                            </h3>
                            <p class="text-sm transition-colors duration-300 mb-4" style="color: var(--text-secondary);" id="feedbackUnsuspendSubtitle"></p>
                            <p class="text-sm transition-colors duration-300" style="color: var(--text-muted);">
                                This will restore full access to the account. Please confirm this action is appropriate.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t px-6 py-4 flex gap-3 justify-end" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <button type="button" id="feedbackUnsuspendCancel"
                            class="px-6 py-3 rounded-lg border transition-all duration-300 font-medium"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white px-6 py-3 rounded-lg transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                        Confirm Unsuspend
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- UNBLOCK MODAL --}}
<div id="feedbackUnblockModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="feedback-unblock-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true" id="feedbackUnblockBackdrop"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border"
             style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <form id="feedbackUnblockForm" method="POST" action="">
                @csrf

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-green-100">
                            <i class="fas fa-unlock text-green-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold transition-colors duration-300 mb-1" style="color: var(--text-primary);" id="feedback-unblock-modal-title">
                                Unblock Seller Access
                            </h3>
                            <p class="text-sm transition-colors duration-300 mb-4" style="color: var(--text-secondary);" id="feedbackUnblockSubtitle"></p>
                            <p class="text-sm transition-colors duration-300" style="color: var(--text-muted);">
                                This will restore the helper's ability to offer services on the platform. Please confirm this action is appropriate.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t px-6 py-4 flex gap-3 justify-end" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <button type="button" id="feedbackUnblockCancel"
                            class="px-6 py-3 rounded-lg border transition-all duration-300 font-medium"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white px-6 py-3 rounded-lg transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                        Confirm Unblock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ENFORCE MODAL --}}
<div id="feedbackEnforceModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="feedback-enforce-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true" id="feedbackEnforceBackdrop"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border"
             style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <form id="feedbackEnforceForm" method="POST" action="">
                @csrf

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-red-100">
                            <i class="fas fa-ban text-red-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold transition-colors duration-300 mb-1" style="color: var(--text-primary);" id="feedback-enforce-modal-title">
                                Enforce Action
                            </h3>
                            <p class="text-sm transition-colors duration-300 mb-1" style="color: var(--text-secondary);" id="feedbackEnforceSubtitle"></p>
                            <p class="text-sm transition-colors duration-300 mb-4" style="color: var(--text-muted);">
                                Provide a clear reason for this action. The user will be notified via email.
                            </p>
                            <textarea name="reason" id="feedbackEnforceReason" rows="4"
                                class="w-full rounded-xl border-2 p-4 transition-all duration-300 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                placeholder="e.g. Repeated policy violations, abusive behaviour, fraud..." required></textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t px-6 py-4 flex gap-3 justify-end" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <button type="button" id="feedbackEnforceCancel"
                            class="px-6 py-3 rounded-lg border transition-all duration-300 font-medium"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit" id="feedbackEnforceSubmit"
                            class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white px-6 py-3 rounded-lg transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                        Confirm Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- WARNING MODAL --}}
<div id="feedbackWarningModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="feedback-warning-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true" id="feedbackWarningBackdrop"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border"
             style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <form id="feedbackWarningForm" method="POST" action="">
                @csrf

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-yellow-100">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold transition-colors duration-300 mb-1" style="color: var(--text-primary);" id="feedback-warning-modal-title">
                                Issue Warning
                            </h3>
                            <p class="text-sm transition-colors duration-300 mb-1" style="color: var(--text-secondary);" id="feedbackWarningSubtitle"></p>
                            <p class="text-sm transition-colors duration-300 mb-4" style="color: var(--text-muted);">
                                Provide a clear reason for this warning. The user will be notified via email.
                            </p>
                            <textarea name="reason" id="feedbackWarningReason" rows="4"
                                class="w-full rounded-xl border-2 p-4 transition-all duration-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                placeholder="e.g. Inappropriate feedback, spam content, policy violation..." required></textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t px-6 py-4 flex gap-3 justify-end" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <button type="button" id="feedbackWarningCancel"
                            class="px-6 py-3 rounded-lg border transition-all duration-300 font-medium"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-400 hover:to-orange-400 text-white px-6 py-3 rounded-lg transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                        Issue Warning
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
    <div id="adminModuleFeedbackConfig"
        data-success-message="{{ session('success') }}"
        data-error-message="{{ session('error') }}"
        data-warning-message="{{ session('warning') }}"></div>
    <script src="{{ asset('js/admin-feedback.js') }}"></script>
@endsection
