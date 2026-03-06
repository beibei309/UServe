<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $service->hss_title ?? 'Service Page' }} - S2U</title>

    {{-- Fonts & CSS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        h1,
        h2,
        h3,
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @media (min-width: 1024px) {
            .sticky-sidebar {
                position: sticky;
                top: 100px;
            }
        }

        /* Custom Flatpickr Styling */
        .flatpickr-calendar {
            border-radius: 1rem;
            border: none;
            box-shadow: none;
            margin: 0 auto;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
        }

        .rich-text ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .rich-text ol {
            list-style-type: decimal;
            padding-left: 1.25rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body class="antialiased text-slate-800">

    @include('layouts.navbar')

    <div class="bg-white border-b border-gray-200 pt-24 pb-6">
        <div class="max-w-7xl mx-auto px-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500">
                    <li class="inline-flex items-center"><a href="{{ route('dashboard') }}"
                            class="hover:text-indigo-600"><i class="fa-solid fa-house mr-2"></i> Home</a></li>
                    <li><i class="fa-solid fa-chevron-right text-gray-400 mx-2 text-xs"></i><a
                            href="{{ route('services.index') }}" class="hover:text-indigo-600">Find Services</a></li>
                    <li><i class="fa-solid fa-chevron-right text-gray-400 mx-2 text-xs"></i><span
                            class="font-medium text-gray-800">{{ $service->hss_title }}</span></li>
                </ol>
            </nav>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-6 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- LEFT COLUMN (Service Details) --}}
            <div class="lg:col-span-8 space-y-8">
                <div>
    {{-- TITLE --}}
    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 leading-tight">
        {{ $service->hss_title }}
    </h1>

    {{-- CATEGORY (Below Title) --}}
    @if ($service->category)
        <div class="mt-2">
                <span
                class="inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1 text-xs font-semibold" style="color: {{ $service->category->hc_color }}; background-color: {{ $service->category->hc_color }}20; border: 1px solid {{ $service->category->hc_color }};" >
                <svg class="h-3.5 w-3.5 text-current" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>

                {{ $service->category->hc_name }}
            </span>
        </div>
    @endif

    {{-- META INFO --}}
    <div class="flex flex-wrap items-center gap-4 text-sm mt-4">
        <span class="font-semibold text-slate-900">
            {{ $detailsUi['provider_display_name'] }}
        </span> |
        <span class="text-slate-500">
            <i class="fa-solid fa-star text-yellow-400"></i>
            {{ $service->hss_rating ?? '0.0' }}
        </span>

        {{-- STATUS BADGE --}}
        @if ($service->hss_status === 'available')
            <span
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold
                bg-green-50 text-green-700 border border-green-200">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Available
            </span>
        @else
            <span
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold
                bg-gray-100 text-gray-500 border border-gray-200">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                Unavailable
            </span>
        @endif
    </div>
</div>


                <div
                    class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-md transition-all duration-300 hover:shadow-xl">
                    {{-- Image Container --}}
                    <div class="aspect-video h-[400px] w-full overflow-hidden bg-gray-100">
                        @if ($detailsImageUrl)
                            <img src="{{ $detailsImageUrl }}"
                                alt="{{ $service->hss_title ?? 'Service Image' }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                data-fallback-src="{{ $detailsImagePlaceholder }}">
                        @else
                            <div
                                class="flex h-full w-full flex-col items-center justify-center bg-gray-50 text-gray-400">
                                <svg class="mb-2 h-12 w-12 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-medium uppercase tracking-wider">No Image Available</span>
                            </div>
                        @endif

                        {{-- Optional: Gradient Overlay (Makes text easier to read if you add titles over the image later) --}}
                    </div>
                </div>


                <section class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2">Description</h2>
                    <div class="prose prose-slate max-w-none text-gray-600 rich-text">{!! $service->hss_description !!}</div>
                </section>

                {{-- Helper Profile Section --}}
                <section
                    class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex flex-col md:flex-row gap-8 items-start">

                        {{-- Left: Profile Image & Badge --}}
                        <div class="relative mx-auto md:mx-0 flex-shrink-0 group">

                            {{-- 1. WRAPPER FOR BLUR LOGIC --}}
                            <div class="relative">
                                @if ($service->user->hu_profile_photo_path)
                                    <img src="{{ asset( $service->user->hu_profile_photo_path) }}"
                                        class="w-24 h-24 md:w-28 md:h-28 rounded-full object-cover border-4 border-white shadow-lg transition-all duration-300 
                        {{-- BLUR IF GUEST --}}
                        @guest blur-md brightness-90 @endguest">
                                @else
                                    <div
                                        class="w-24 h-24 md:w-28 md:h-28 rounded-full bg-indigo-600 flex items-center justify-center text-3xl md:text-4xl text-white font-bold border-4 border-white shadow-lg 
                        {{-- BLUR IF GUEST --}}
                        @guest blur-md brightness-90 @endguest">
                                        {{ $detailsUi['provider_initial_upper'] }}
                                    </div>
                                @endif

                                {{-- LOCK ICON OVERLAY FOR GUESTS --}}
                                @guest
                                    <div class="absolute inset-0 flex items-center justify-center z-10">
                                        <div class="bg-black/30 p-2 rounded-full">
                                            <i class="fas fa-lock text-white text-lg"></i>
                                        </div>
                                    </div>
                                @endguest
                            </div>

                            {{-- Verified Badge (Only show if logged in, or keep visible but on top of blur) --}}
                            @if ($service->user->hu_trust_badge ?? false)
                                <div class="absolute bottom-1 right-1 bg-blue-500 text-white w-7 h-7 flex items-center justify-center rounded-full border-2 border-white shadow-sm z-20"
                                    title="Verified Student">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Right: Info & Stats --}}
                        <div class="flex-1 w-full text-center md:text-left">
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-slate-900 mb-1">
                                    {{-- Optional: Mask name for guests if you want extra privacy --}}
                                    {{ $detailsUi['provider_display_name'] }}
                                </h3>
                                <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-sm">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-indigo-50 text-indigo-700">
                                        <i class="fa-solid fa-graduation-cap mr-1.5 text-xs"></i>
                                        {{ $service->user->hu_faculty ?? 'Faculty of Computing' }}
                                    </span>
                                    <span class="text-gray-400 hidden sm:inline">�</span>
                                    <span class="text-gray-500">Member since
                                        {{ $service->user->created_at->format('M Y') }}</span>
                                </div>
                            </div>

                            {{-- Bio Box --}}
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-left relative">
                                <i
                                    class="fa-solid fa-quote-left text-slate-200 text-2xl absolute top-3 left-3 -z-0"></i>
                                <p class="text-gray-600 italic text-sm relative z-10 pl-6">
                                    "{{ $service->user->hu_bio ?? 'Hi! I am a dedicated student at UPSI looking to help the community. I ensure all tasks are completed with care and punctuality.' }}"
                                </p>
                            </div>

                            {{-- Quick Stats Row --}}
                            <div class="grid grid-cols-2 gap-4 mt-5 pt-5 border-t border-gray-100">
                                {{-- Stats content here --}}
                            </div>

                            <div class="mt-5 text-center md:text-left">
                                {{-- 2. LOGIC FOR VIEW PROFILE LINK --}}
                                @auth
                                    {{-- User IS logged in --}}
                                    <a href="{{ route('students.profile', $service->user) }}"
                                        class="text-sm font-bold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors">
                                        View Full Profile <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                                    </a>
                                @else
                                    {{-- User is GUEST (Redirect to login) --}}
                                    <a href="{{ route('login') }}"
                                        data-requires-login-confirm
                                        class="text-sm font-bold text-gray-500 hover:text-indigo-600 hover:underline transition-colors cursor-pointer">
                                        <i class="fas fa-lock mr-1 text-xs"></i> Sign in to view profile
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Reviews Section --}}
               {{-- Reviews Section --}}
<section class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-900">
            {{-- OPTIONAL: You might want to filter the count in the controller to be accurate --}}
            Reviews
        </h2>

        {{-- Show Service Rating Summary --}}
        @if ($reviews->count() > 0)
            <div class="flex items-center gap-2 bg-yellow-50 px-3 py-1 rounded-lg border border-yellow-100">
                <i class="fas fa-star text-yellow-500"></i>
                <span class="font-bold text-slate-800">{{ $detailsUi['rating_display'] }}</span>
                <span class="text-xs text-gray-500">/ 5.0</span>
            </div>
        @endif
    </div>

   @if (isset($reviews) && count($reviews) > 0)
    <div class="space-y-6">
        @foreach ($reviews as $review)
            
            <div class="border-b border-gray-50 pb-6 last:border-0 last:pb-0">

                {{-- 1. Client Review --}}
                <div class="flex items-start gap-3">
                    {{-- Avatar Client --}}
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-sm uppercase overflow-hidden">
                            @if($review->reviewer->hu_profile_photo_path)
                                <img src="{{ asset($review->reviewer->hu_profile_photo_path) }}" class="w-full h-full object-cover">
                            @else
                                {{ $review->ui_reviewer_initial }}
                            @endif
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-2">
                                {{-- Name --}}
                                <span class="font-bold text-slate-900 text-sm">
                                    {{ $review->ui_reviewer_display_name }}
                                </span>

                                {{-- NEW: Role Badge (Student / Community) --}}
                                @if ($review->ui_reviewer_role === 'student')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-100 text-indigo-800 capitalize border border-indigo-200">
                                        Student
                                    </span>
                                @elseif ($review->ui_reviewer_role === 'community')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 capitalize border border-green-200">
                                        Community
                                    </span>
                                @endif
                            </div>

                            <span class="text-xs text-gray-400">{{ $review->ui_created_human }}</span>
                        </div>

                        <div class="flex text-yellow-400 text-xs my-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $review->hr_rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>

                        <p class="text-gray-600 text-sm leading-relaxed">{{ $review->hr_comment }}</p>
                    </div>
                </div>

                {{-- 2. Helper Reply --}}
                @if ($review->hr_reply)
                    <div class="mt-4 ml-2 pl-8 border-l-2 border-indigo-100 relative">
                        <div class="bg-slate-50 p-4 rounded-r-xl rounded-bl-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-bold text-gray-700 flex items-center gap-1">
                                    Reply from seller: 
                                    {{ $detailsUi['provider_display_name'] }}
                                    @if ($service->user->hu_trust_badge)
                                        <i class="fas fa-check-circle text-[10px] text-blue-500"></i>
                                    @endif
                                </span>
                                @if ($review->ui_replied_ago)
                                    <span class="text-[10px] text-gray-400">�
                                        {{ $review->ui_replied_ago }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 italic">"{{ $review->hr_reply }}"</p>
                        </div>
                    </div>
                @endif

            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-8">
        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fa-regular fa-comments text-gray-300 text-xl"></i>
        </div>
        <p class="text-gray-500 text-sm">No reviews yet for this service.</p>
    </div>
@endif
       
</section>
            </div>

            {{-- RIGHT COLUMN (Booking System) --}}
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-6" x-data="bookingSystem()" x-init="init()">

                    {{-- 1. CALENDAR MODAL (Hidden by default) --}}
                    <template x-teleport="body">
                        <div x-show="showFullCalendar"
                            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                            style="display: none;" x-transition.opacity>
                            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all"
                                @click.away="showFullCalendar = false" x-transition.scale>
                                <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-gray-50">
                                    <h3 class="font-bold text-slate-800">Select Date</h3>
                                    <button @click="showFullCalendar = false"
                                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="p-4 flex justify-center">
                                    <div id="full-calendar-container"></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- 2. MAIN BOOKING CARD --}}
                    <div
                        class="bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden relative">

                        <div class="grid grid-cols-3 border-b border-gray-200 bg-gray-50">
                            @if ($service->hss_basic_price)
                                <button @click="switchPackage('basic')"
                                    :class="currentPackage === 'basic' ?
                                        'border-b-2 border-teal-600 text-teal-600 font-bold bg-white' :
                                        'text-gray-500 hover:text-gray-700'"
                                    class="py-4 text-sm transition-all border-b-2 border-transparent">
                                    Basic
                                </button>
                            @endif
                            @if ($service->hss_standard_price)
                                <button @click="switchPackage('standard')"
                                    :class="currentPackage === 'standard' ?
                                        'border-b-2 border-yellow-500 text-yellow-600 font-bold bg-white' :
                                        'text-gray-500 hover:text-gray-700'"
                                    class="py-4 text-sm transition-all border-b-2 border-transparent">
                                    Standard
                                </button>
                            @endif
                            @if ($service->hss_premium_price)
                                <button @click="switchPackage('premium')"
                                    :class="currentPackage === 'premium' ?
                                        'border-b-2 border-red-600 text-red-600 font-bold bg-white' :
                                        'text-gray-500 hover:text-gray-700'"
                                    class="py-4 text-sm transition-all border-b-2 border-transparent">
                                    Premium
                                </button>
                            @endif
                        </div>

                        <div class="p-6">
                            {{-- Price & Simple Info Display --}}
                            <div class="flex flex-col items-end mb-6 text-right">
                                <span class="font-bold text-gray-400 text-xs uppercase tracking-wider mb-1">
                                    <span x-text="isSessionBased ? 'From' : 'Task Price'"></span>
                                </span>

                                {{-- Price --}}
                                <span class="text-4xl font-extrabold" :class="priceColorClass"
                                    x-text="'RM' + calculateTotal()"></span>

                                {{-- ?? UPDATED: Simple Data Display (No labels) --}}
                                <div class="text-sm font-medium text-gray-500 mt-1 flex items-center gap-1"
                                    x-show="packages[currentPackage].duration || packages[currentPackage].frequency">
                                    <span x-text="packages[currentPackage].duration"></span>

                                    {{-- Show divider/text only if both exist --}}
                                    <span
                                        x-show="packages[currentPackage].duration && packages[currentPackage].frequency">

                                    </span>

                                    <span x-text="packages[currentPackage].frequency"></span>
                                </div>
                            </div>

                            {{-- Description Box --}}
                            <div class="bg-slate-50 rounded-xl p-4 mb-6 border border-slate-100 text-sm" x-transition>
                                <div class="text-slate-700 prose prose-sm max-w-none rich-text"
                                    x-html="packages[currentPackage].description || 'No description provided.'"></div>
                            </div>

                            {{-- Duration (Session Based Only) --}}
                            {{-- Duration --}}
                            <div class="mb-6" x-show="isSessionBased">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-xs font-bold text-gray-700 uppercase">Duration</label>
                                    <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded"
                                        x-text="formatDuration(selectedDuration * sessionDuration)"></span>
                                </div>
                                <div class="grid grid-cols-5 gap-2">
                                    <template x-for="h in [1, 2, 3, 4, 5]" :key="h">
                                        <button @click="selectDuration(h)" type="button"
                                            class="py-2.5 rounded-xl border text-sm font-bold transition-all"
                                            :class="selectedDuration === h ?
                                                'bg-slate-800 text-white border-slate-800 shadow-md transform -translate-y-0.5' :
                                                'bg-white text-gray-600 border-gray-200 hover:border-gray-300 hover:bg-gray-50'">
                                            <span x-text="formatDuration(h * sessionDuration)"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div class="w-full h-px bg-gray-100 mb-6"></div>

                            {{-- Date Scroller --}}
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="text-xs font-bold text-gray-700 uppercase">Select Date</label>
                                    <button @click="openCalendar()"
                                        class="text-xs text-indigo-600 font-bold hover:text-indigo-800 flex items-center gap-1">
                                        <i class="fa-regular fa-calendar"></i> Full Calendar
                                    </button>
                                </div>

                                <div class="relative group">
                                    {{-- Prev Button --}}
                                    <button type="button"
                                        @click="$refs.dateScroller.scrollBy({ left: -200, behavior: 'smooth' })"
                                        class="absolute -left-2 top-1/2 -translate-y-1/2 z-10 w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md border border-gray-100 text-gray-600 hover:text-indigo-600 hover:scale-110 transition-all opacity-0 group-hover:opacity-100">
                                        <i class="fa-solid fa-chevron-left text-xs"></i>
                                    </button>

                                    <div x-ref="dateScroller"
                                        class="flex space-x-2 overflow-x-auto pb-4 pt-1 px-1 no-scrollbar scroll-smooth">
                                        <template x-for="day in upcomingDays" :key="day.dateStr">
                                            <button @click="selectDate(day)" :disabled="!day.isAvailable"
                                                class="flex flex-col items-center justify-center min-w-[4.5rem] py-3 rounded-2xl border transition-all flex-shrink-0 relative group/date"
                                                :class="{
                                                    'bg-slate-900 text-white border-slate-900 shadow-lg shadow-slate-900/20 transform -translate-y-1': selectedDate ===
                                                        day.dateStr,
                                                    'bg-white text-gray-600 border-gray-200 hover:border-indigo-300 hover:shadow-md': selectedDate !==
                                                        day.dateStr && day.isAvailable,
                                                    'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed opacity-60':
                                                        !day.isAvailable
                                                }">
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-wider mb-1 opacity-80"
                                                    x-text="day.dayName"></span>
                                                <span class="text-lg font-black" x-text="day.dayNumber"></span>

                                                {{-- Today Indicator --}}
                                                <span
                                                    x-show="new Date().toDateString() === new Date(day.dateStr).toDateString()"
                                                    class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full"
                                                    :class="selectedDate === day.dateStr ? 'bg-indigo-400' : 'bg-indigo-500'"></span>
                                            </button>
                                        </template>
                                    </div>

                                    {{-- Next Button --}}
                                    <button type="button"
                                        @click="$refs.dateScroller.scrollBy({ left: 200, behavior: 'smooth' })"
                                        class="absolute -right-2 top-1/2 -translate-y-1/2 z-10 w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md border border-gray-100 text-gray-600 hover:text-indigo-600 hover:scale-110 transition-all opacity-0 group-hover:opacity-100">
                                        <i class="fa-solid fa-chevron-right text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Time Slots --}}
                            <div x-show="selectedDate && isSessionBased" x-transition class="mb-6">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Start Time</label>
                                <div class="flex flex-wrap gap-2" x-show="timeSlots.length > 0">
                                    <template x-for="slot in timeSlots" :key="slot.time">
                                        <button type="button" @click="selectedTime = slot.time"
                                            :disabled="!slot.available"
                                            class="px-4 py-2 rounded-lg text-sm font-bold transition-all border"
                                            :class="{
                                                'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/20': selectedTime ===
                                                    slot.time,
                                                'bg-white text-slate-600 border-slate-200 hover:border-indigo-400 hover:text-indigo-600': selectedTime !==
                                                    slot.time && slot.available,
                                                'bg-slate-50 text-slate-300 border-slate-100 cursor-not-allowed': !slot
                                                    .available
                                            }">
                                            <span x-text="formatTimeOnly(slot.time)"></span>
                                        </button>
                                    </template>
                                </div>
                                <div x-show="timeSlots.length === 0"
                                    class="text-sm bg-orange-50 text-orange-600 px-3 py-2 rounded-lg border border-orange-100">
                                    <i class="fa-regular fa-circle-xmark mr-1"></i> No times available.
                                </div>
                            </div>

                            {{-- Task Based Feedback --}}
                            <div x-show="selectedDate && !isSessionBased" x-transition
                                class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-indigo-600 shadow-sm shrink-0">
                                    <i class="fa-regular fa-calendar-check"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-indigo-900"
                                        x-text="new Date(selectedDate).toDateString()"></p>
                                    <p class="text-xs text-indigo-700">Full day service allocated.</p>
                                </div>
                            </div>

                            {{-- CTA Button --}}
                           @auth
                                @if ($hasActiveRequest)
                                    {{-- User already has an active request for THIS service --}}
                                    <button disabled
                                        class="w-full py-4 rounded-xl font-bold text-indigo-400 bg-indigo-50 border border-indigo-100 cursor-not-allowed flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-spinner animate-spin text-sm"></i> 
                                        <span>Request Active</span>
                                    </button>
                                    <p class="text-xs text-center text-gray-400 mt-2">
                                        You already have a pending or active order for this service.
                                    </p>
                                @elseif ($service->hss_status === 'available')
                                    <button @click="submitBooking()"
                                        :disabled="!selectedDate || (isSessionBased && !selectedTime)"
                                        class="group w-full py-4 rounded-xl font-bold text-white shadow-xl transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:shadow-none disabled:bg-gray-300 disabled:cursor-not-allowed hover:-translate-y-1"
                                        :class="(!selectedDate || (isSessionBased && !selectedTime)) ? '' :
                                        'bg-slate-900 hover:bg-indigo-600 hover:shadow-indigo-500/30'">
                                        <span>Request Appointment</span>
                                        <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-1"
                                            x-show="!(!selectedDate || (isSessionBased && !selectedTime))"></i>
                                    </button>
                                @else
                                    <button disabled
                                        class="w-full py-4 rounded-xl font-bold text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-ban text-sm"></i> <span>Service Unavailable</span>
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                    class="w-full py-4 rounded-xl font-bold text-white shadow-lg transition-all flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 hover:-translate-y-0.5">
                                    <span>Sign in to Request</span> <i class="fa-solid fa-right-to-bracket text-sm"></i>
                                </a>
                            @endauth
                        </div>
                    </div>

                    {{-- 3. CONTACT & INFO CARD --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="w-1 h-5 bg-indigo-500 rounded-full"></span> Contact
                        </h3>

                        {{-- WhatsApp Button --}}
                        @if ($detailsHasPhone)
                            @auth
                                <a href="{{ $detailsWhatsappUrl }}" target="_blank"
                                    class="flex items-center justify-center w-full py-3 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-xl font-bold transition-all shadow-md shadow-green-500/20 hover:shadow-green-500/40 hover:-translate-y-0.5 mb-6 group">
                                    <i class="fa-brands fa-whatsapp text-xl mr-2 transition-transform group-hover:scale-110"></i>
                                    Chat on WhatsApp
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="flex items-center justify-center w-full py-3 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-xl font-bold transition-all shadow-md shadow-green-500/20 hover:shadow-green-500/40 hover:-translate-y-0.5 mb-6 group">
                                    <i class="fa-brands fa-whatsapp text-xl mr-2 transition-transform group-hover:scale-110"></i>
                                    Sign in to Chat
                                </a>
                            @endauth
                        @endif

                        {{-- Collapsible Operating Hours --}}
                        <div x-data="{ showHours: false }" class="border-t border-gray-100 pt-5 mb-6">
                            <button @click="showHours = !showHours"
                                class="flex items-center justify-between w-full text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-gray-400"></i> Operating Hours
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                    :class="showHours ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="showHours" x-collapse style="display: none;">
                                <ul class="space-y-2 text-sm mt-3 pl-6 border-l-2 border-gray-50">
                                    @foreach ($detailsOperatingDays as $day)
                                        <li
                                            class="flex justify-between items-center {{ $day['is_today'] ? 'text-indigo-600 font-bold' : 'text-gray-500' }}">
                                            <span class="w-10">{{ $day['name'] }}</span>
                                            @if ($day['is_open'])
                                                <span>{{ $day['start'] }} - {{ $day['end'] }}</span>
                                            @else
                                                <span
                                                    class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-400">Closed</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Utility Buttons Grid --}}
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Share --}}
                            <button type="button" data-share-trigger
                                data-url="{{ route('student-services.show', $service->hss_id) }}"
                                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-bold hover:bg-gray-50 hover:border-gray-300 transition-all">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Share
                            </button>

                            {{-- Save / Favourite --}}
                            <button
                                type="button"
                                data-favourite-service="{{ $service->hss_id }}"
                                data-logged-in="{{ $detailsUi['is_authenticated'] ? 'true' : 'false' }}"
                                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-gray-200 text-sm font-bold transition-all group
                    {{ $detailsUi['favourite_button_class'] }}">
                                <i id="heart-{{ $service->hss_id }}"
                                    class="{{ $detailsUi['favourite_icon_class'] }} fa-heart transition-transform group-active:scale-90"></i>
                                <span id="text-{{ $service->hss_id }}">{{ $detailsUi['favourite_text'] }}</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')

    {{-- Share Modal --}}
    <div id="shareModal"
        class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-80 p-6 transform scale-95 transition-transform duration-300">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-900">Share Service</h3>
                <button type="button" data-close-share class="text-gray-400 hover:text-gray-600"><i
                        class="fas fa-times"></i></button>
            </div>
            <div class="flex items-center border rounded-lg overflow-hidden bg-gray-50">
                <input type="text" id="shareLinkInput"
                    class="flex-1 px-3 py-2 text-sm bg-transparent outline-none text-gray-600" readonly>
                <button type="button" data-copy-share
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-medium">Copy</button>
            </div>
            <p id="copyMessage" class="text-xs text-green-600 mt-2 text-center opacity-0 transition-opacity">Copied!
            </p>
        </div>
    </div>

    <div id="servicesDetailsConfig"
        data-authenticated="{{ $detailsUi['is_authenticated'] ? 'true' : 'false' }}"
        data-has-active-request="{{ $hasActiveRequest ? 'true' : 'false' }}"
        data-is-session-based="{{ $service->hss_session_duration ? 'true' : 'false' }}"
        data-holidays='@json($detailsHolidays)'
        data-schedule='@json($detailsSchedule)'
        data-booked-slots='@json($bookedAppointments ?? [])'
        data-manual-blocks='@json($manualBlocks ?? [])'
        data-packages='@json($detailsPackages)'
        data-current-package="{{ $detailsCurrentPackage }}"
        data-session-duration="{{ $detailsSessionDuration }}"
        data-service-id="{{ $service->hss_id }}"
        data-store-request-url="{{ route('service-requests.store') }}"
        data-orders-url="{{ route('service-requests.index') }}"
        data-login-url="{{ route('login') }}"
        data-favourite-toggle-url="{{ route('favorites.services.toggle') }}"
        data-csrf-token="{{ csrf_token() }}"></div>
    <script src="{{ asset('js/nonadmin-services-details.js') }}"></script>
</body>

</html>
