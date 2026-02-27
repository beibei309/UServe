<x-app-layout>
    <div class="min-h-screen bg-slate-50 py-12 font-sans">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>

            {{-- User Identity Card --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                <div class="px-8 pb-8 relative">
                    <div class="absolute -top-16 left-8">
                        @if ($user->hu_profile_photo_path)
                            <img src="{{ asset($user->hu_profile_photo_path) }}"
                                class="w-32 h-32 rounded-full border-4 border-white shadow-md object-cover bg-white">
                        @else
                            <div
                                class="w-32 h-32 rounded-full border-4 border-white shadow-md bg-indigo-600 flex items-center justify-center text-white text-4xl font-black">
                                {{ substr($user->hu_name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <div class="pt-20 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        {{-- Left Side: User Info --}}
                        <div>
                            <h1 class="text-3xl font-black text-slate-900">{{ $user->hu_name }}</h1>
                            <p class="text-slate-500 font-medium mt-1">
                                Member since {{ $user->hu_created_at->format('F Y') }}
                            </p>
                            @if ($user->hu_role === 'student')
                                <span
                                    class="inline-flex mt-2 items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                    Student
                                </span>
                            @endif
                        </div>

                        {{-- Right Side: Metrics (Rating & Reports) --}}
                        <div class="flex flex-wrap items-center gap-3">
                            
                            {{-- NEW: Report Count Badge (Only shows if > 0) --}}
                          @if(($user->reports_count ?? 0) > 0)
                                <div class="flex items-start gap-3 bg-red-50 px-4 py-3 rounded-xl border border-red-100 max-w-sm">
                                    <div class="mt-0.5">
                                        {{-- Icon changed to indicate Payment Issue --}}
                                        <i class="fas fa-file-invoice-dollar text-red-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-red-800 uppercase tracking-widest mb-1">
                                            Report
                                        </p>
                                        <p class="text-xs text-red-700 leading-snug">
                                            This account has been reported <span class="font-black">{{ $user->reports_count }} time(s)</span> for failing to settle payments for completed services.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Rating Summary Badge --}}
                            <div class="flex items-center gap-4 bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100">
                                <div class="text-center">
                                    <div class="text-3xl font-black text-slate-900 leading-none">
                                        {{ number_format($averageRating, 1) }}</div>
                                    <div class="flex text-yellow-400 text-[10px] justify-center mt-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($averageRating) ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="h-8 w-px bg-slate-200"></div>
                                <div class="text-sm font-bold text-slate-500 uppercase tracking-wide">
                                    {{ $totalReviews }} Reviews
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Reviews List --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-900">Seller Review</h3>
                </div>

                <div class="divide-y divide-slate-100">
                    @if ($reviews->count() > 0)
                        @foreach ($reviews as $review)
                            <div class="p-8 hover:bg-slate-50/30 transition-colors">
                                <div class="flex items-start gap-4">
                                    {{-- Reviewer Avatar --}}
                                    <div class="flex-shrink-0">
                                        {{-- Check if reviewer exists AND has a photo --}}
                                        @if ($review->reviewer && $review->reviewer->hu_profile_photo_path)
                                            <img src="{{ asset($review->reviewer->hu_profile_photo_path) }}"
                                                class="w-10 h-10 rounded-full object-cover border border-slate-200"
                                                alt="{{ $review->reviewer->hu_name }}">
                                        @else
                                            {{-- Fallback: Show Initials if no photo --}}
                                            <div
                                                class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold">
                                                {{ substr($review->reviewer->hu_name ?? 'A', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <h4 class="font-bold text-slate-900">
                                                <a href="{{ route('students.profile', $review->reviewer->hu_id) }}"
                                                    class="hover:text-indigo-600 transition-colors hover:underline decoration-indigo-300 decoration-2">
                                                    {{ $review->reviewer->hu_name ?? 'Anonymous' }}
                                                </a>
                                            </h4>
                                            <span
                                                class="text-xs text-slate-400">{{ optional($review->hr_created_at)->diffForHumans() ?? 'Recently' }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 mb-3">
                                            {{-- 1. Star Rating --}}
                                            <div class="flex text-yellow-400 text-xs">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $review->hr_rating ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>

                                            {{-- 2. "Review from Seller" Badge --}}
                                            <span
                                                class="text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase tracking-wide">
                                                Review from Seller
                                            </span>
                                        </div>

                                        @if ($review->hr_comment)
                                            <p class="text-slate-600 text-sm leading-relaxed mb-3">
                                                "{{ $review->hr_comment }}"
                                            </p>
                                        @endif

                                        {{-- Context: Which service was this for? --}}
                                        @if ($review->studentService)
                                            <a href="{{ route('services.details', $review->studentService->hss_id) }}"
                                                class="inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition-colors group">
                                                <i
                                                    class="fas fa-handshake text-xs text-slate-400 group-hover:text-slate-600"></i>
                                                <span
                                                    class="text-xs font-bold text-slate-600 group-hover:text-slate-800">
                                                    Transacted: {{ Str::limit($review->studentService->hss_title, 40) }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-12 text-center">
                            <div
                                class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="far fa-comment-dots text-2xl text-slate-300"></i>
                            </div>
                            <h3 class="text-slate-900 font-bold">No reviews yet</h3>
                            <p class="text-slate-500 text-sm mt-1">This user hasn't received any feedback yet.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
