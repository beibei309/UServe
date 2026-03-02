@extends('admin.layout')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Manage Community Users</h1>

    <!-- Search + Export Row -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">

        <!-- Search -->
        <form method="GET" class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">

            <input type="text" name="search" placeholder="Search community users..."
                class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                value="{{ request('search') }}">

            <select name="rating_range"
                class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm cursor-pointer bg-white">
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
                class="w-full md:w-auto px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Search
            </button>

            @if (request('search') || request('rating_range'))
                <a href="{{ route('admin.community.index', ['status' => request('status')]) }}"
                    class="px-3 py-2 text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition"
                    title="Clear Filters">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <!-- Export -->
        <a href="{{ route('admin.community.export', array_merge(request()->only('search', 'status'), ['format' => 'csv'])) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600
              text-white rounded-lg hover:bg-green-700 transition text-sm">
            <i class="fa-solid fa-file-csv"></i>
            Export CSV
        </a>

    </div>

    <!-- FILTER PILLS -->
    <div class="flex flex-wrap gap-2 mb-6">

        @php
            $pill = 'px-4 py-2 rounded-full text-sm font-medium transition';
            $active = 'bg-blue-600 text-white';
            $inactive = 'bg-gray-100 text-gray-700 hover:bg-gray-200';
        @endphp

        <!-- ALL -->
        <a href="{{ route('admin.community.index', request()->except('status')) }}"
            class="{{ $pill }} {{ request('status') == null ? $active : $inactive }}">
            All
        </a>

        <!-- ACTIVE -->
        <a href="{{ route('admin.community.index', ['status' => 'active'] + request()->except('page')) }}"
            class="{{ $pill }} {{ request('status') == 'active' ? $active : $inactive }}">
            Active
        </a>

        <!-- BLACKLISTED -->
        <a href="{{ route('admin.community.index', ['status' => 'suspended'] + request()->except('page')) }}"
            class="{{ $pill }} {{ request('status') == 'blacklisted' ? $active : $inactive }}">
            Suspended
        </a>

    </div>


    <div class="bg-white shadow rounded-lg p-6">

        <table class="w-full text-center">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-3 px-4">User</th>
                    <th class="py-3 px-4">Phone</th>
                    <th class="py-3 px-4">Rating</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($communityUsers as $user)
                    <tr class="border-b hover:bg-gray-50">

                        <td class="py-3 px-4 flex items-center gap-3">
                            <img src="{{ $user->hu_profile_photo_path ? asset($user->hu_profile_photo_path) : asset('uploads/profile/default.png') }}"
                                class="w-12 h-12 rounded-full object-cover border">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $user->hu_name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->hu_email }}</p>
                            </div>
                        </td>

                        <td class="py-3 px-4 text-sm text-gray-700">
                            {{ $user->hu_phone ?? '-' }}
                        </td>

                        <td class="py-3 px-4 text-sm">
                            <div class="flex flex-col items-center justify-center">
                                {{-- Use the variable from withCount() --}}
                                @if ($user->reviews_received_count > 0)
                                    <div class="flex items-center gap-1 text-yellow-500">
                                        <span class="font-bold text-gray-900 text-base">
                                            {{-- Use the variable from withAvg() --}}
                                            {{ number_format($user->reviews_received_avg_rating, 1) }}
                                        </span>
                                        <i class="fa-solid fa-star text-sm"></i>
                                    </div>
                                    <span class="text-xs text-gray-400 mt-0.5">
                                        ({{ $user->reviews_received_count }}
                                        {{ Str::plural('review', $user->reviews_received_count) }})
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic bg-gray-100 px-2 py-1 rounded">
                                        No ratings
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="py-3 px-4 text-sm">
                            {{-- Check if either flag is true --}}
                            @if ($user->hu_is_blacklisted || $user->hu_is_suspended)
                                <span class="px-3 py-1 text-sm bg-red-200 text-red-800 rounded-full">Suspended</span>
                                @if ($user->hu_blacklist_reason)
                                    <p class="text-xs text-red-700 mt-1">{{ $user->hu_blacklist_reason }}</p>
                                @endif
                            @elseif($user->hu_verification_status == 'approved')
                                <span class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-full">
                                    Verified
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 rounded-full">
                                    Not Verified
                                </span>
                            @endif
                        </td>

                        <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                            <div class="flex justify-center gap-3 items-center">

                                {{-- BUTTON: View Reviews --}}
                                <button onclick="openReviewsModal('reviews-modal-{{ $user->hu_id }}')"
                                    class="text-yellow-600 hover:text-yellow-800 transition relative group"
                                    title="Read Reviews">
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                    @if ($user->reviews_received_count > 0)
                                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                        </span>
                                    @endif
                                </button>

                                {{-- VIEW --}}
                                <a href="{{ route('admin.community.view', $user->hu_id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 transition" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('admin.community.edit', $user->hu_id) }}"
                                    class="text-blue-600 hover:text-blue-900 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- BLACKLIST / UNBLACKLIST --}}
                                @if (!$user->hu_is_blacklisted && !$user->hu_is_suspended)
    {{-- Show Blacklist Button if user is active --}}
    <button onclick="openBlacklistModal({{ $user->hu_id }})"
        class="text-red-600 hover:text-red-900 transition" title="Blacklist">
        <i class="fa-solid fa-ban"></i>
    </button>
@else
    {{-- Show Unblacklist Button if user is Blacklisted OR Suspended --}}
    <form action="{{ route('admin.community.unblacklist', $user->hu_id) }}" method="POST"
        class="inline unblacklist-form">
        @csrf
        <button type="button" onclick="confirmUnblacklist(this)"
            class="text-green-600 hover:text-green-900 transition" title="Unblacklist">
            <i class="fa-solid fa-unlock"></i>
        </button>
    </form>
@endif
                            </div>

                            <div id="reviews-modal-{{ $user->hu_id }}" class="fixed inset-0 z-50 hidden text-left"
                                aria-labelledby="modal-title" role="dialog" aria-modal="true">

                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"
                                    onclick="closeReviewsModal('reviews-modal-{{ $user->hu_id }}')"></div>

                                <div class="fixed inset-0 z-10 overflow-y-auto">
                                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                                        <div
                                            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

                                            <div
                                                class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between items-center border-b">
                                                <h3 class="text-lg font-semibold leading-6 text-gray-900"
                                                    id="modal-title">
                                                    Reviews for <span class="text-blue-600">{{ $user->hu_name }}</span>
                                                </h3>
                                                <button type="button"
                                                    onclick="closeReviewsModal('reviews-modal-{{ $user->hu_id }}')"
                                                    class="text-gray-400 hover:text-gray-500">
                                                    <i class="fa-solid fa-xmark text-xl"></i>
                                                </button>
                                            </div>

                                            <div
                                                class="bg-white px-4 py-5 sm:p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">

                                                {{-- Use eager loaded relationship 'reviewsReceived' --}}
                                                @if ($user->reviewsReceived->isNotEmpty())
                                                    <div class="space-y-6">
                                                        @foreach ($user->reviewsReceived as $review)
                                                            <div
                                                                class="flex gap-4 p-4 rounded-lg bg-gray-50 border border-gray-100">

                                                                <div class="flex-shrink-0">
                                                                    @php
                                                                        $reviewerPath = $review->reviewer->hu_profile_photo_path ?? null;
                                                                        if ($reviewerPath && \Illuminate\Support\Str::startsWith($reviewerPath, ['http://', 'https://'])) {
                                                                            $reviewerImage = $reviewerPath;
                                                                        } elseif ($reviewerPath && file_exists(public_path('storage/' . $reviewerPath))) {
                                                                            $reviewerImage = asset('storage/' . $reviewerPath);
                                                                        } elseif ($reviewerPath) {
                                                                            $reviewerImage = asset($reviewerPath);
                                                                        } else {
                                                                            $reviewerImage = asset('uploads/profile/default.png');
                                                                        }
                                                                    @endphp
                                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                                        src="{{ $reviewerImage }}"
                                                                        alt="">
                                                                </div>

                                                                <div class="flex-1">
                                                                    <div class="flex items-center justify-between mb-1">
                                                                        <h4 class="text-sm font-bold text-gray-900">
                                                                            {{ $review->reviewer->hu_name ?? 'Unknown User' }}
                                                                        </h4>
                                                                        <span class="text-xs text-gray-500">
                                                                            {{ optional($review->hr_created_at)->format('M d, Y') ?? '-' }}
                                                                        </span>
                                                                    </div>

                                                                    <div class="flex items-center mb-2">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            <i
                                                                                class="fa-solid fa-star text-xs {{ $i <= $review->hr_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                                        @endfor
                                                                        <span
                                                                                class="ml-2 text-xs font-medium text-gray-600">({{ $review->hr_rating }}.0)</span>
                                                                    </div>

                                                                            @if ($review->hr_comment)
                                                                        <p class="text-sm text-gray-700 italic">
                                                                                "{{ $review->hr_comment }}"</p>
                                                                    @else
                                                                        <p class="text-sm text-gray-400 italic">No written
                                                                            comment.</p>
                                                                    @endif

                                                                            @if ($review->hr_reply)
                                                                        <div
                                                                            class="mt-3 ml-2 pl-3 border-l-2 border-blue-300 bg-blue-50 p-2 rounded-r">
                                                                            <p
                                                                                class="text-xs font-bold text-blue-800 mb-0.5">
                                                                                Reply:</p>
                                                                            <p class="text-xs text-blue-900">
                                                                                {{ $review->hr_reply }}</p>
                                                                            <span class="text-[10px] text-blue-400">
                                                                                {{ \Carbon\Carbon::parse($review->hr_replied_at)->diffForHumans() }}
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
                                                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                                            <i
                                                                class="fa-regular fa-comment-dots text-gray-400 text-xl"></i>
                                                        </div>
                                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No reviews yet
                                                        </h3>
                                                        <p class="mt-1 text-sm text-gray-500">This user hasn't received any
                                                            feedback.</p>
                                                    </div>
                                                @endif

                                            </div>

                                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                <button type="button"
                                                    onclick="closeReviewsModal('reviews-modal-{{ $user->hu_id }}')"
                                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $communityUsers->links() }}
        </div>

    </div>



    <!-- BLACKLIST MODAL -->
    <div id="blacklistModal"
        class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm
            flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-xl">

            <h2 class="text-xl font-bold mb-4">Suspend User</h2>
            <p class="text-gray-600 mb-3">Please provide a reason:</p>

            <textarea id="blacklistReason" rows="3" class="w-full border rounded p-2 focus:ring focus:ring-red-300"
                placeholder="Write reason..."></textarea>

            <div class="mt-5 flex justify-end gap-3">
                <button onclick="closeBlacklistModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                    Cancel
                </button>

                <button onclick="submitBlacklist()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                    Confirm
                </button>

            </div>
        </div>
    </div>


    <script>
        const csrfToken = "{{ csrf_token() }}";
        let selectedUserId = null;

        function openReviewsModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            // Prevent background scrolling
            document.body.style.overflow = 'hidden';
        }

        function closeReviewsModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            // Restore background scrolling
            document.body.style.overflow = 'auto';
        }

        function openBlacklistModal(id) {
            selectedUserId = id;
            document.getElementById("blacklistModal").classList.remove("hidden");
        }

        function closeBlacklistModal() {
            document.getElementById("blacklistModal").classList.add("hidden");
            document.getElementById("blacklistReason").value = "";
        }

        function submitBlacklist() {
            const reason = document.getElementById("blacklistReason").value.trim();

            if (!reason) {
                alert("Please enter account suspended reason.");
                return;
            }

            let form = document.createElement("form");
            form.method = "POST";
            form.action = "{{ route('admin.community.blacklist', 'ID_PLACEHOLDER') }}"
                .replace('ID_PLACEHOLDER', selectedUserId);

            let token = document.createElement("input");
            token.type = "hidden";
            token.name = "_token";
            token.value = "{{ csrf_token() }}";
            form.appendChild(token);

            let reasonInput = document.createElement("input");
            reasonInput.type = "hidden";
            reasonInput.name = "blacklist_reason";
            reasonInput.value = reason;
            form.appendChild(reasonInput);

            document.body.appendChild(form);
            form.submit();
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        function confirmUnblacklist(button) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This user will regain access to the platform.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Reactivate user account.'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>

@endsection
