@extends('admin.layout')

@section('content')

<div class="px-2 sm:px-4 lg:px-6 py-4">
    <h1 class="text-2xl sm:text-3xl font-bold mb-6 transition-colors duration-300" style="color: var(--text-primary);">Manage Reviews, Warnings & Final Action</h1>
    <p class="text-xs mb-4 transition-colors duration-300" style="color: var(--text-muted);">
        Feedback moderation covers student, community and helper accounts. Warning is shared, final action is role-based.
    </p>
    <p class="text-xs mb-4 transition-colors duration-300" style="color: var(--text-muted);">
        At {{ $userWarningLimit }}/{{ $userWarningLimit }} warnings: helper = {{ $finalActions['helper'] }}, student/community = {{ $finalActions['student'] }}.
    </p>

    <form class="mb-6" method="GET" action="{{ route('admin.feedback.index') }}">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <input type="text" name="search" placeholder="Search by name or email..."
                   class="p-2 border rounded w-full sm:w-80 transition-colors duration-300" style="background-color: var(--bg-secondary); color: var(--text-primary); border-color: var(--border-color);" value="{{ request('search') }}">
            @if ($selectedRole)
                <input type="hidden" name="role" value="{{ $selectedRole }}">
            @endif
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">Search</button>
        </div>
    </form>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.feedback.index', request()->except('role')) }}"
            class="px-4 py-2 rounded-full text-sm font-medium transition {{ $selectedRole === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All
        </a>
        @foreach ($roleOptions as $role)
            <a href="{{ route('admin.feedback.index', array_merge(request()->except('page', 'role'), ['role' => $role])) }}"
                class="px-4 py-2 rounded-full text-sm font-medium transition {{ $selectedRole === $role ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
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
                    <th class="py-3 px-4 text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Status</th>
                    <th class="py-3 px-4 text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Actions</th>
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
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->feedback_status_badge_class }}"> 
                                {{ $user->feedback_status_label }} 
                            </span> 
                        </td>
                        
                        {{-- Actions: Notify/Block --}}
                        <td class="py-3 px-4 text-sm">
                            @if ($user->feedback_can_warn)
                                    {{-- Form untuk send warning --}}
                                    <form action="{{ route('admin.feedback.warning', $user->hu_id) }}" method="POST" class="inline-block"
                                        data-feedback-reason data-action-label="Notify Warning">
                                        @csrf
                                        <input type="hidden" name="reason" value="">
                                        <button type="submit" class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600 transition-colors">
                                            Notify Warning ({{ $user->feedback_next_warning_count }}/{{ $userWarningLimit }})
                                        </button>
                                    </form>
                            @elseif ($user->feedback_can_enforce)
                                    <form action="{{ route('admin.feedback.enforce', $user->hu_id) }}" method="POST" class="inline-block"
                                        data-feedback-reason data-action-label="{{ $finalActions[$user->hu_role] }}">
                                        @csrf
                                        <input type="hidden" name="reason" value="">
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors">
                                            {{ $finalActions[$user->hu_role] }}
                                        </button>
                                    </form>
                            @elseif ($user->feedback_can_unblock)
                                <form action="{{ route('admin.feedback.unblock', $user->hu_id) }}" method="POST" class="inline-block"
                                    data-confirm-message="Unblock seller access for {{ $user->hu_name }}?">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                                        UNBLOCK SELLER ACCESS
                                    </button>
                                </form>
                            @else
                                <span class="text-red-500 font-semibold">Action Locked</span>
                            @endif
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

@section('scripts')
    <div id="adminModuleFeedbackConfig"></div>
    <script src="{{ asset('js/admin-feedback.js') }}"></script>
@endsection
