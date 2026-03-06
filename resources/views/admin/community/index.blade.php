@extends('admin.layout')

@section('content')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Manage Community Users</h1>
                <p class="mt-1 font-medium transition-colors duration-300" style="color: var(--text-secondary);">Monitor and manage community members, their verification status, and ratings.</p>
            </div>
        </div>

        <!-- Search + Export Row -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

            <!-- Search -->
            <form method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">

                <input type="text" name="search" placeholder="Search community users..."
                    class="w-full sm:w-64 px-4 py-3 border rounded-xl text-sm transition-all duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);"
                    value="{{ request('search') }}">

                <select name="rating_range"
                    class="w-full sm:w-48 px-4 py-3 border rounded-xl text-sm cursor-pointer transition-all duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                    <option value="">All Ratings</option>
                    <option value="4-5" {{ request('rating_range') == '4-5' ? 'selected' : '' }}>4.0 - 5.0 Stars</option>
                    <option value="3-4" {{ request('rating_range') == '3-4' ? 'selected' : '' }}>3.0 - 3.9 Stars</option>
                    <option value="2-3" {{ request('rating_range') == '2-3' ? 'selected' : '' }}>2.0 - 2.9 Stars</option>
                    <option value="1-2" {{ request('rating_range') == '1-2' ? 'selected' : '' }}>1.0 - 1.9 Stars</option>
                    <option value="0-1" {{ request('rating_range') == '0-1' ? 'selected' : '' }}>0.0 - 0.9 Stars</option>
                </select>

                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl hover:from-cyan-400 hover:to-blue-500 transition-all duration-300 text-sm font-medium shadow-lg hover:shadow-xl">
                    Search
                </button>

                @if (request('search') || request('rating_range'))
                    <a href="{{ route('admin.community.index', ['status' => request('status')]) }}"
                        class="px-3 py-3 border rounded-xl text-sm transition-all duration-300 hover:shadow-md surface-hover"
                        style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                        title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            <!-- Export -->
            <a href="{{ route('admin.community.export', array_merge(request()->only('search', 'status'), ['format' => 'csv'])) }}"
                class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white rounded-xl transition-all duration-300 text-sm font-medium shadow-lg hover:shadow-xl">
                <i class="fa-solid fa-file-csv"></i>
                Export CSV
            </a>

        </div>

        <!-- FILTER PILLS -->
        <div class="flex flex-wrap gap-3 mb-8">
            <!-- ALL -->
            <a href="{{ route('admin.community.index', request()->except('status')) }}"
                class="px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300 shadow-lg {{ request('status') == null ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-cyan-500/25 hover:shadow-xl' : 'border shadow-md hover:shadow-lg' }}"
                @if(request('status') != null)
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                @endif>
                All
            </a>

            <!-- ACTIVE -->
            <a href="{{ route('admin.community.index', ['status' => 'active'] + request()->except('page')) }}"
                class="px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300 shadow-lg {{ request('status') == 'active' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-cyan-500/25 hover:shadow-xl' : 'border shadow-md hover:shadow-lg' }}"
                @if(request('status') != 'active')
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                @endif>
                Active
            </a>

            <!-- BLACKLISTED -->
            <a href="{{ route('admin.community.index', ['status' => 'suspended'] + request()->except('page')) }}"
                class="px-6 py-3 rounded-xl text-sm font-medium transition-all duration-300 shadow-lg {{ request('status') == 'blacklisted' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-cyan-500/25 hover:shadow-xl' : 'border shadow-md hover:shadow-lg' }}"
                @if(request('status') != 'blacklisted')
                style="color: var(--text-secondary); background-color: var(--bg-tertiary); border-color: var(--border-color);"
                @endif>
                Suspended
            </a>

        </div>


    <!-- Data Table -->
    <div class="rounded-2xl shadow-xl border transition-all duration-300 overflow-hidden"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%);">
                        <th class="py-4 px-6 text-left font-semibold transition-colors duration-300" style="color: var(--text-secondary);">User</th>
                        <th class="py-4 px-6 text-left font-semibold transition-colors duration-300" style="color: var(--text-secondary);">Phone</th>
                        <th class="py-4 px-6 text-center font-semibold transition-colors duration-300" style="color: var(--text-secondary);">Rating</th>
                        <th class="py-4 px-6 text-center font-semibold transition-colors duration-300" style="color: var(--text-secondary);">Status</th>
                        <th class="py-4 px-6 text-center font-semibold transition-colors duration-300" style="color: var(--text-secondary);">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($communityUsers as $user)
                        <tr class="border-b transition-all duration-300 hover:shadow-lg surface-hover"
                            style="border-color: var(--border-color);">

                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <img src="{{ $user->profile_image_url }}"
                                    class="w-12 h-12 rounded-full object-cover border-2 transition-colors duration-300 shadow-md"
                                    style="border-color: var(--border-color);">
                                <div>
                                    <p class="font-semibold text-sm transition-colors duration-300" style="color: var(--text-primary);">{{ $user->hu_name }}</p>
                                    <p class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">{{ $user->hu_email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-6 text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                            {{ $user->hu_phone ?? '-' }}
                        </td>

                        <td class="py-4 px-6 text-center">
                            <div class="flex flex-col items-center justify-center">
                                {{-- Use the variable from withCount() --}}
                                @if ($user->reviews_received_count > 0)
                                    <div class="flex items-center gap-1 text-yellow-500">
                                        <span class="font-bold text-base transition-colors duration-300" style="color: var(--text-primary);">
                                            {{-- Use the variable from withAvg() --}}
                                            {{ number_format($user->reviews_received_avg_rating, 1) }}
                                        </span>
                                        <i class="fa-solid fa-star text-sm"></i>
                                    </div>
                                    <span class="text-xs mt-0.5 transition-colors duration-300" style="color: var(--text-secondary);">
                                        ({{ $user->reviews_received_count }}
                                        {{ Str::plural('review', $user->reviews_received_count) }})
                                    </span>
                                @else
                                    <span class="text-xs italic px-3 py-1 rounded-full transition-colors duration-300"
                                          style="color: var(--text-muted); background-color: var(--bg-tertiary);">
                                        No ratings
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="py-4 px-6 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $user->status_badge_class }}">{{ $user->status_label }}</span>
                            @if (($user->hu_is_blacklisted || $user->hu_is_suspended) && $user->hu_blacklist_reason)
                                <p class="text-xs text-red-600 mt-1">{{ $user->hu_blacklist_reason }}</p>
                            @endif
                        </td>

                        <td class="py-4 px-6 text-center">
                            <div class="flex flex-wrap justify-center gap-2 items-center">

                                {{-- BUTTON: View Reviews --}}
                                <button type="button" data-reviews-open data-modal-id="reviews-modal-{{ $user->hu_id }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg text-xs font-semibold transition-all duration-200 relative"
                                    title="Read Reviews">
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                    @if ($user->reviews_received_count > 0)
                                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                        </span>
                                    @endif
                                </button>

                                {{-- VIEW --}}
                                <a href="{{ route('admin.community.view', $user->hu_id) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-cyan-100 hover:bg-cyan-200 text-cyan-700 rounded-lg text-xs font-semibold transition-all duration-200" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('admin.community.edit', $user->hu_id) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-semibold transition-all duration-200" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- BLACKLIST / UNBLACKLIST --}}
                                @if (!$user->hu_is_blacklisted && !$user->hu_is_suspended)
                                    <button type="button" data-blacklist-open data-user-id="{{ $user->hu_id }}"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-semibold transition-all duration-200" title="Blacklist">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                @else
                                    <form action="{{ route('admin.community.unblacklist', $user->hu_id) }}" method="POST"
                                        class="inline-flex unblacklist-form">
                                        @csrf
                                        <button type="button" data-unblacklist-confirm
                                            class="inline-flex items-center justify-center w-8 h-8 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-semibold transition-all duration-200" title="Unblacklist">
                                            <i class="fa-solid fa-unlock"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div id="reviews-modal-{{ $user->hu_id }}" class="fixed inset-0 z-50 hidden text-left"
                                aria-labelledby="modal-title" role="dialog" aria-modal="true">

                                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity backdrop-blur-sm"
                                    data-reviews-close data-modal-id="reviews-modal-{{ $user->hu_id }}"></div>

                                <div class="fixed inset-0 z-10 overflow-y-auto">
                                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                                        <div
                                            class="relative transform overflow-hidden rounded-lg text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border"
                                            style="background-color: var(--bg-secondary); border-color: var(--border-color);">

                                            <div
                                                class="px-4 py-3 sm:px-6 flex justify-between items-center border-b transition-colors duration-300"
                                                style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                                                <h3 class="text-lg font-semibold leading-6 transition-colors duration-300"
                                                    style="color: var(--text-primary);" id="modal-title">
                                                    Reviews for <span class="text-cyan-400">{{ $user->hu_name }}</span>
                                                </h3>
                                                <button type="button"
                                                    data-reviews-close data-modal-id="reviews-modal-{{ $user->hu_id }}"
                                                    class="hover:text-cyan-400 transition-colors duration-300"
                                                    style="color: var(--text-muted);">
                                                    <i class="fa-solid fa-xmark text-xl"></i>
                                                </button>
                                            </div>

                                            <div
                                                class="px-4 py-5 sm:p-6 max-h-[60vh] overflow-y-auto custom-scrollbar transition-colors duration-300"
                                                style="background-color: var(--bg-secondary);">

                                                {{-- Use eager loaded relationship 'reviewsReceived' --}}
                                                @if ($user->reviewsReceived->isNotEmpty())
                                                    <div class="space-y-6">
                                                        @foreach ($user->reviewsReceived as $review)
                                                            <div
                                                                class="flex gap-4 p-4 rounded-lg border transition-colors duration-300"
                                                                style="background-color: var(--bg-tertiary); border-color: var(--border-color);">

                                                                <div class="flex-shrink-0">
                                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                                        src="{{ $review->reviewer_image_url }}"
                                                                        alt="">
                                                                </div>

                                                                <div class="flex-1">
                                                                    <div class="flex items-center justify-between mb-1">
                                                                        <h4 class="text-sm font-bold transition-colors duration-300" style="color: var(--text-primary);">
                                                                            {{ $review->reviewer->hu_name ?? 'Unknown User' }}
                                                                        </h4>
                                                                        <span class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">
                                                                            {{ optional($review->hr_created_at)->format('M d, Y') ?? '-' }}
                                                                        </span>
                                                                    </div>

                                                                    <div class="flex items-center mb-2">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            <i
                                                                                class="fa-solid fa-star text-xs {{ $i <= $review->hr_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                                        @endfor
                                                                        <span
                                                                                class="ml-2 text-xs font-medium transition-colors duration-300" style="color: var(--text-secondary);">({{ $review->hr_rating }}.0)</span>
                                                                    </div>

                                                                            @if ($review->hr_comment)
                                                                        <p class="text-sm italic transition-colors duration-300" style="color: var(--text-secondary);">
                                                                                "{{ $review->hr_comment }}"</p>
                                                                    @else
                                                                        <p class="text-sm italic transition-colors duration-300" style="color: var(--text-muted);">No written
                                                                            comment.</p>
                                                                    @endif

                                                                            @if ($review->hr_reply)
                                                                        <div
                                                                            class="mt-3 ml-2 pl-3 border-l-2 border-cyan-500 p-2 rounded-r transition-colors duration-300"
                                                                            style="background-color: var(--bg-primary);">
                                                                            <p
                                                                                class="text-xs font-bold text-cyan-400 mb-0.5">
                                                                                Reply:</p>
                                                                            <p class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">
                                                                                {{ $review->hr_reply }}</p>
                                                                            <span class="text-[10px] transition-colors duration-300" style="color: var(--text-muted);">
                                                                                {{ $review->replied_at_human }}
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-center py-8">
                                                        <div
                                                            class="inline-flex items-center justify-center w-12 h-12 rounded-full mb-3 transition-colors duration-300"
                                                            style="background-color: var(--bg-tertiary);">
                                                            <i
                                                                class="fa-regular fa-comment-dots text-xl transition-colors duration-300" style="color: var(--text-muted);"></i>
                                                        </div>
                                                        <h3 class="mt-2 text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">No reviews yet
                                                        </h3>
                                                        <p class="mt-1 text-sm transition-colors duration-300" style="color: var(--text-secondary);">This user hasn't received any
                                                            feedback.</p>
                                                    </div>
                                                @endif

                                            </div>

                                            <div class="px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t transition-colors duration-300"
                                                 style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                                                <button type="button"
                                                    data-reviews-close data-modal-id="reviews-modal-{{ $user->hu_id }}"
                                                    class="mt-3 inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset sm:mt-0 sm:w-auto transition-colors duration-300"
                                                    style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t px-6 py-4" style="border-color: var(--border-color); background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%);">
            {{ $communityUsers->links() }}
        </div>

    </div>

</div>



    <!-- BLACKLIST MODAL -->
    <div id="blacklistModal"
        class="hidden fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm
            flex items-center justify-center z-50">

        <div class="w-full max-w-md p-6 rounded-lg shadow-xl border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">

            <h2 class="text-xl font-bold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Suspend User</h2>
            <p class="mb-3 transition-colors duration-300" style="color: var(--text-secondary);">Please provide a reason:</p>

            <textarea id="blacklistReason" rows="3" 
                      class="w-full border rounded p-2 transition-colors duration-300"
                      style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);"
                      placeholder="Write reason..."></textarea>

            <div class="mt-5 flex justify-end gap-3">
                <button type="button" data-blacklist-close
                        class="px-4 py-2 rounded transition-colors duration-300 hover:text-cyan-400"
                        style="background-color: var(--bg-tertiary); color: var(--text-secondary);">
                    Cancel
                </button>

                <button type="button" data-blacklist-submit
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition-colors duration-300">
                    Confirm
                </button>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <div id="adminModuleCommunityIndexConfig"
        data-csrf-token="{{ csrf_token() }}"
        data-blacklist-route-template="{{ route('admin.community.blacklist', 'ID_PLACEHOLDER') }}"
        data-success-message="{{ session('success') }}"></div>
    <script src="{{ asset('js/admin-community-index.js') }}"></script>
@endsection
