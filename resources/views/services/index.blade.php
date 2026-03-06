<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>U-Serve | Upsi Service Circle</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f8fafc;
            /* Slate-50 */
        }



        /* Custom Scrollbar for cleaner look */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .rich-text ul {
            list-style-type: disc;
            padding-left: 1.25rem;
        }

        .rich-text ol {
            list-style-type: decimal;
            padding-left: 1.25rem;
        }
    </style>
</head>

<body class="antialiased text-slate-800">
    <div x-data="{
        mobileMenuOpen: false,
        stats: { students: 1250, services: 340, reviews: 890 },
        animateStats: false
    }" x-init="setTimeout(() => animateStats = true, 1000)">

        {{-- Navigation bar --}}
        @include('layouts.navbar')

        <div class="bg-white border-b border-gray-200 pt-24 pb-12">
            <div class="max-w-7xl mx-auto px-6">
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center hover:text-indigo-600 transition-colors">
                                <i class="fa-solid fa-house mr-2"></i> Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                                <span class="font-medium text-gray-800">Find Services</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-2">Find Student
                            Services</h1>
                        <p class="text-slate-500 text-lg">Browse services offered by UPSI students. Fast, reliable, and
                            affordable.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-10 ">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 ">

                {{-- ======================= --}}
                {{--       SIDEBAR           --}}
                {{-- ======================= --}}
                <aside class="lg:col-span-3 space-y-6">
                    <div
                        class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sticky top-24 transition-all duration-300">

                        <div class="flex items-center justify-between mb-6 px-2">
                            <h3 class="font-black text-slate-900 uppercase tracking-tighter text-sm">Explore Categories
                            </h3>
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        </div>

                        <div class="space-y-2">
                            {{-- All Categories Link --}}
                            <a href="{{ route('services.index') }}"
                                class="group flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-bold transition-all duration-300
                {{ !$category_id ? 'bg-slate-200 text-slate-700 shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">

                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-9 h-9 flex items-center justify-center rounded-xl transition-all duration-300
            {{ !$category_id ? 'bg-white text-indigo-600 shadow-sm' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:shadow-sm' }}">
                                        <i class="fas fa-th-large text-sm"></i>
                                    </span>
                                    <span>All Services</span>
                                </div>
                                @if (!$category_id)
                                    <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
                                @endif
                            </a>

                            <div class="py-2 px-4">
                                <div class="h-px bg-slate-100 w-full"></div>
                            </div>

                            {{-- Dynamic Categories --}}
                            @foreach ($categories as $cat)
                                <a href="?category_id={{ $cat->hc_id }}"
                                    class="group flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-300
                    {{ $category_id == $cat->hc_id ? 'bg-white shadow-lg shadow-slate-100 ring-1 ring-slate-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">

                                    <div class="flex items-center gap-3">
                                        {{-- Modern Icon Container --}}
                                        <span
                                            class="w-9 h-9 flex items-center justify-center rounded-xl transition-all duration-300 border shadow-sm"
                                            style="
                                background-color: {{ $category_id == $cat->hc_id ? $cat->hc_color : $cat->hc_color . '10' }}; 
                                border-color: {{ $cat->hc_color . '30' }};
                                color: {{ $category_id == $cat->hc_id ? '#FFFFFF' : $cat->hc_color }};
                            ">
                                            <i class="{{ $cat->hc_icon ?? 'fa-solid fa-folder' }} text-sm"></i>
                                        </span>

                                        <span class="transition-colors {{ $category_id == $cat->hc_id ? 'text-slate-900' : '' }}">
                                            {{ $cat->hc_name }}
                                        </span>
                                    </div>

                                    {{-- Count Badge (Jika anda ada withCount dalam controller) --}}
                                    @if (isset($cat->services_count))
                                        <span
                                            class="text-[10px] px-2 py-1 rounded-lg font-black tracking-tighter transition-all
                            {{ $category_id == $cat->hc_id ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600' }}">
                                            {{ $cat->services_count }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        @guest
                            <div
                                class="mt-8 p-5 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl text-white relative overflow-hidden group">
                                <div
                                    class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700">
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-1">New Opportunity?
                                </p>
                                <h4 class="text-xs font-bold leading-tight mb-3">Become a seller and start earning today!
                                </h4>
                                <a href="{{ route('register') }}"
                                    class="text-[10px] bg-white text-indigo-600 px-3 py-1.5 rounded-lg font-black uppercase hover:bg-indigo-50 transition-colors">Join
                                    Now</a>
                            </div>
                        @endguest
                    </div>
                </aside>

                {{-- ======================= --}}
                {{--      MAIN CONTENT       --}}
                {{-- ======================= --}}
                <main class="lg:col-span-9">

                    {{-- Top Filters --}}
                    <div class="sticky top-4 z-30 mb-10 px-2 sm:px-0">
                        <div
                            class="bg-white/80 backdrop-blur-md border border-slate-200 shadow-xl shadow-slate-200/50 rounded-[2rem] p-2 transition-all duration-300">

                            <form method="GET" action="{{ route('services.index') }}"
                                class="flex flex-col lg:flex-row gap-2">
                                <input type="hidden" name="category_id" value="{{ $category_id }}">

                                <div class="relative flex-1 group">
                                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="q" value="{{ request('q') }}"
                                        placeholder="Search services (Cleaning, Tutoring...)"
                                        class="block w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-[1.5rem] text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium">
                                </div>

                                <div class="flex flex-wrap md:flex-nowrap gap-2">
                                    <div class="relative flex-1 md:w-44 group">
                                        <select name="sort" data-auto-submit
                                            class="block w-full pl-4 pr-10 py-4 bg-slate-50 border-none rounded-[1.5rem] text-slate-700 focus:bg-white focus:ring-2 focus:ring-indigo-500 text-sm cursor-pointer appearance-none font-bold transition-all">
                                            <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest
                                                First</option>
                                            <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest
                                                First</option>
                                            <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>
                                                Price: Low-High</option>
                                            <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>
                                                Price: High-Low</option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="relative flex-1 md:w-44 group">
                                        <select name="available_only" data-auto-submit
                                            class="block w-full pl-4 pr-10 py-4 bg-slate-50 border-none rounded-[1.5rem] text-slate-700 focus:bg-white focus:ring-2 focus:ring-indigo-500 text-sm cursor-pointer appearance-none font-bold transition-all">
                                            <option value="">🔘 All Status</option>
                                            <option value="1"
                                                {{ request('available_only') == '1' ? 'selected' : '' }}>🟢 Available
                                            </option>
                                            <option value="0"
                                                {{ request('available_only') == '0' ? 'selected' : '' }}>🔴 Unavailable
                                            </option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="w-full md:w-14 h-14 bg-indigo-500 hover:bg-indigo-600 text-white rounded-[1.2rem] transition-all duration-300 shadow-lg shadow-slate-900/20 active:scale-90 flex items-center justify-center group"
                                        title="Search">

                                        <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform duration-300"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                                            </path>
                                        </svg>

                                        <span class="sr-only">Search</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="space-y-8">
                        @foreach ($services as $service)
                            <div
                                class="group bg-white rounded-[2rem] border border-slate-100 p-2 sm:p-3 shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 relative overflow-hidden">

                                <div class="flex flex-col md:flex-row gap-6">
                                    {{-- IMAGE SECTION --}}
                                    <div
                                        class="md:w-72 h-64 md:h-auto flex-shrink-0 relative rounded-[1.5rem] overflow-hidden bg-slate-50">
                                   <img src="{{ $service->ui_image_url }}"
    alt="{{ $service->hss_title }}"
     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
    data-fallback-src="{{ $service->ui_image_fallback }}">

                                        {{-- Category Badge --}}
                                        @if ($service->category)
                                            <div class="absolute top-4 left-4">
                                                <span
                                                    class="backdrop-blur-md bg-white/80 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm"
                                                    style="color: {{ $service->category->hc_color }}">
                                                    {{ $service->category->hc_name }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Price Tag (Mobile Only) --}}
                                        @if ($service->hss_basic_price)
                                            <div
                                                class="md:hidden absolute bottom-4 right-4 bg-slate-900/90 backdrop-blur text-white px-4 py-2 rounded-2xl text-sm font-bold shadow-lg">
                                                RM{{ number_format($service->hss_basic_price, 0) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- CONTENT SECTION --}}
                                    <div class="flex-1 px-4 py-4 md:py-6 flex flex-col">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                                    <i class="far fa-clock text-indigo-500"></i>
                                                    {{ $service->ui_created_human }}
                                                </span>
                                                <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                                <span
                                                    class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter border {{ $service->hss_status === 'available' ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-rose-500 bg-rose-50 border-rose-100' }}">
                                                    {{ $service->hss_status }}
                                                </span>
                                            </div>

                                            <button type="button" data-share-trigger
                                                data-url="{{ $service->ui_details_url }}"
                                                class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 hover:bg-indigo-600 hover:text-white transition-all duration-300 shadow-sm"
                                                title="Share Service">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                        </div>

                                        <h2
                                            class="text-2xl font-black text-slate-900 mb-3 leading-tight group-hover:text-indigo-600 transition-colors duration-300">
                                            <a href="{{ route('services.details', $service->hss_id) }}">
                                                {{ $service->hss_title }}
                                            </a>
                                        </h2>

                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="flex text-yellow-400 text-xs">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="{{ $i <= ($service->reviews_avg_rating ?? 0) ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <span
                                                class="text-sm font-bold text-slate-700">{{ number_format($service->reviews_avg_rating ?? 0, 1) }}</span>
                                            <span
                                                class="text-xs text-slate-400 font-medium">({{ $service->reviews_count }}
                                                reviews)</span>
                                        </div>

                                        <div class="text-slate-500 text-sm line-clamp-2 leading-relaxed mb-6 flex-1">
                                            {!! strip_tags($service->hss_description) !!}
                                        </div>

                                        <div class="flex items-center justify-between border-t border-slate-50 pt-6">
                                            {{-- Seller Info --}}
                                            <a href="{{ $service->ui_profile_url }}"
                                                class="flex items-center gap-3 group/user overflow-hidden max-w-[200px]">
                                                <div class="relative flex-shrink-0">

                                                    <img src="{{ $service->ui_seller_avatar_url }}"
                                                        class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-md group-hover/user:ring-2 group-hover/user:ring-indigo-500 transition-all duration-300 @guest blur-sm @endguest">

                                                    @guest
                                                        <div
                                                            class="absolute inset-0 flex items-center justify-center text-white bg-slate-900/20 rounded-2xl">
                                                            <i class="fas fa-lock text-[10px]"></i>
                                                        </div>
                                                    @endguest

                                                    @if ($service->user->hu_trust_badge)
                                                        <div
                                                            class="absolute -top-1 -right-1 bg-blue-500 text-white w-5 h-5 rounded-full flex items-center justify-center text-[8px] border-2 border-white shadow-sm">
                                                            <i class="fas fa-check"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex flex-col min-w-0">
                                                    <span
                                                        class="text-sm font-black text-slate-800 group-hover/user:text-indigo-600 transition truncate"
                                                        @guest title="Login to view full name" @endguest>
                                                        {{ $service->ui_seller_display_name }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                                        Student Seller
                                                    </span>
                                                </div>
                                            </a>

                                            <div class="flex items-center gap-6">
                                                @if ($service->hss_basic_price)
                                                    <div class="text-right hidden sm:block">
                                                        <p
                                                            class="text-[10px] text-slate-400 font-black uppercase tracking-tighter">
                                                            Starts at</p>
                                                        <p class="text-xl font-black text-slate-900">
                                                            <span class="text-sm font-bold text-indigo-600">RM
                                                            </span>{{ number_format($service->hss_basic_price, 2) }}
                                                        </p>
                                                    </div>
                                                @endif

                                                <a href="{{ route('services.details', $service->hss_id) }}"
                                                    class="bg-white text-slate-700 border border-slate-200 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 px-7 py-3 rounded-2xl text-sm font-bold transition-all duration-300 shadow-sm hover:shadow-indigo-200 hover:-translate-y-1 flex items-center justify-center">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-10">
                        {{ $services->appends(request()->query())->links() }}
                    </div>
                </main>
            </div>
        </div>

        <div id="shareModal"
            class="fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" data-close-share></div>

            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 relative transform scale-95 transition-transform duration-300 mx-4">
                <button type="button" data-close-share
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <div class="text-center mb-6">
                    <div
                        class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-share-alt text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Share Service</h3>
                    <p class="text-sm text-gray-500 mt-1">Copy the link below to share this service.</p>
                </div>

                <div class="relative">
                    <input type="text" id="shareLinkInput"
                        class="w-full bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-xl px-4 py-3 pr-24 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                        readonly>
                    <button type="button" data-copy-share
                        class="absolute right-1 top-1 bottom-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 rounded-lg text-sm font-medium transition">
                        Copy
                    </button>
                </div>
                <p id="copyMessage"
                    class="text-green-600 text-xs font-semibold text-center mt-3 h-4 opacity-0 transition-opacity">Link
                    Copied Successfully!</p>
            </div>
        </div>

        <div id="servicesIndexConfig" data-page="services-index"></div>
        <script src="{{ asset('js/nonadmin-services-index.js') }}"></script>
    </div>

    {{-- Footer bar --}}
    @include('layouts.footer')

</body>

</html>
