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
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            /* Slate-50 */
        }

        h1,
        h2,
        h3,
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .hero-pattern {
            background-color: #0f172a;
            /* Slate-900 */
            background-image: radial-gradient(#1e293b 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Custom Scrollbar for horizontal lists */
        .hide-scroll::-webkit-scrollbar {
            display: none;
        }

        .hide-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
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
        {{-- Navigation bar --}}
        @include('layouts.navbar')

        <section class="relative pt-28 pb-20 overflow-hidden">

            {{-- 1. BACKGROUND IMAGE START --}}
            <div class="absolute inset-0"> {{-- Removed -z-10 --}}
                <img src="{{ asset('images/bgupsi.jpg') }}" alt="Background" class="w-full h-full object-cover">

                {{-- Keep the overlay here --}}
                <div class="absolute inset-0 bg-gray-900/80"></div>
            </div>
            {{-- BACKGROUND IMAGE END --}}

            {{-- 2. EXISTING ANIMATED BLOBS --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-7xl pointer-events-none">
                <div
                    class="absolute top-20 left-20 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
                </div>
                <div
                    class="absolute top-20 right-20 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
                </div>
            </div>

            {{-- 3. CONTENT --}}
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
                <span
                    class="inline-block py-1 px-3 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-sm font-semibold mb-6">
                    Welcome back, {{ $dashboardUi['welcome_name'] }}!
                </span>

                <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6 leading-tight">
                    Find the perfect <span class="text-indigo-400">student seller</span><br>for your needs.
                </h1>

                <p class="text-lg text-slate-300 mb-10 max-w-2xl mx-auto"> {{-- Changed text-slate-400 to text-slate-300 for better contrast on bg --}}
                    Discover talented UPSI students offering professional services. From design to daily tasks, get it
                    done by your community.
                </p>

                <div class="w-full max-w-3xl mx-auto mb-8">
                    <form action="{{ route('services.index') }}" method="GET" class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="h-6 w-6 text-slate-400 group-focus-within:text-indigo-500 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="q"
                            id="dashboard-search-input"
                            value="{{ $dashboardUi['search_query'] }}"
                            class="block w-full pl-14 pr-4 py-5 bg-white/95 backdrop-blur-sm rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/30 shadow-xl text-lg font-medium transition-all"
                            placeholder="What service are you looking for today?">
                        <button type="submit"
                            class="absolute right-3 top-3 bottom-3 bg-indigo-600 hover:bg-indigo-700 text-white px-6 rounded-xl font-semibold transition-colors shadow-lg">
                            Search
                        </button>
                    </form>
                </div>

                <div class="flex flex-wrap justify-center gap-3 text-sm">
                    <span class="text-slate-400 mr-2 py-1.5">Popular:</span>
                    @foreach($dashboardUi['popular_searches'] as $popularSearch)
                    <a href="{{ route('services.index', ['q' => $popularSearch['query']]) }}"
                        class="px-4 py-1.5 rounded-full bg-slate-800/60 border border-slate-700 text-slate-300 hover:bg-indigo-600 hover:text-white hover:border-indigo-500 transition-all cursor-pointer backdrop-blur-sm">{{ $popularSearch['label'] }}</a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-12  border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Explore Categories</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5">
                    @foreach ($categories as $category)
                        <a href="{{ route('services.index', ['category_id' => $category->hc_id]) }}"
                            class="group p-5 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 text-center flex flex-col items-center justify-center h-full"
                            style="background-color: {{ $category->hc_color }};">

                            <div
                                class="w-14 h-14 mb-4 rounded-full flex items-center justify-center bg-white shadow-sm transition-transform group-hover:scale-110">


                                <i class="{{ $category->hc_icon ?? 'fa-solid fa-folder' }} text-2xl"
                                    style="color: {{ $category->hc_color }};">
                                </i>

                            </div>

                            <span class="block text-sm font-bold text-white transition-colors group-hover:opacity-90">
                                {{ $category->hc_name }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Quick Access Points Section for Community Users --}}
        @auth
        @if(Auth::user()->isCommunity())
        <section class="py-12 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-lg border border-purple-100 p-8">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="flex items-center space-x-6 mb-6 md:mb-0">
                            <div class="bg-gradient-to-br from-purple-500 to-pink-500 w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-coins text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Your Points Dashboard</h3>
                                <p class="text-gray-600">Track your purchase rewards and redeem exclusive benefits!</p>
                                <div class="flex items-center space-x-4 mt-3">
                                    <div class="bg-purple-100 px-3 py-1 rounded-full">
                                        <span class="text-sm font-medium text-purple-700">
                                            {{ Auth::user()->getTotalBuyerPoints() }} Points Earned
                                        </span>
                                    </div>
                                    @if(App\Models\Reward::active()->where('hr_points_cost', '<=', Auth::user()->getAccessibleTotalPoints())->count() > 0)
                                    <div class="bg-pink-100 px-3 py-1 rounded-full">
                                        <span class="text-sm font-medium text-pink-700">
                                            {{ App\Models\Reward::active()->where('hr_points_cost', '<=', Auth::user()->getAccessibleTotalPoints())->count() }} Rewards Available
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('points.dashboard') }}" 
                               class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-purple-600 hover:to-pink-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                View Dashboard
                            </a>
                            @if(Auth::user()->getTotalBuyerPoints() > 0)
                            <a href="{{ route('points.dashboard') }}#points-store-section" 
                               class="bg-white text-purple-600 px-6 py-3 rounded-xl font-semibold border-2 border-purple-200 hover:bg-purple-50 transition-all duration-200">
                                <i class="fas fa-gift mr-2"></i>
                                Shop Rewards
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        @endauth

        <section class="py-16 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900">Services you might like</h2>
                        <p class="text-slate-500 mt-2">Recommended based on popular demand.</p>
                    </div>
                    <a href="{{ route('services.index') }}"
                        class="text-indigo-600 font-semibold hover:text-indigo-700 flex items-center gap-1 group">
                        View all services <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($serviceCards as $serviceCard)
                        <div
                            class="group bg-white rounded-2xl border border-slate-200 hover:border-indigo-100 hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden relative">

                           <a href="{{ $serviceCard['details_url'] }}" class="relative h-56 bg-slate-200 overflow-hidden block">
    <img src="{{ $serviceCard['image_url'] }}" 
         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
         alt="{{ $serviceCard['title'] ?? 'Service' }}">

    @if ($serviceCard['category_name'])
        <span class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                            style="color: {{ $serviceCard['category_color'] }}">
                        {{ $serviceCard['category_name'] }}
        </span>
    @endif
</a>
                            <div class="p-5 flex flex-col flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <img src="{{ $serviceCard['seller_avatar_url'] }}"
                                        class="w-8 h-8 rounded-full object-cover border border-slate-100">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-900 flex items-center gap-1">
                                            {{ $serviceCard['seller_name_short'] }}
                                            @if ($serviceCard['seller_has_trust_badge'])
                                                <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-slate-500">Student seller</span>
                                    </div>

                                    <div class="ml-auto flex items-center gap-1 bg-slate-50 px-2 py-1 rounded text-xs">
    <div class="flex text-yellow-400 text-[10px]">
        {{-- Loop to generate 5 stars --}}
        @for ($i = 1; $i <= 5; $i++)
            <i class="{{ $i <= $serviceCard['rating_stars_filled'] ? 'fas' : 'far' }} fa-star"></i>
        @endfor
    </div>
    
    <span class="font-bold text-slate-700 ml-1">
        {{ $serviceCard['rating_display'] }}
    </span>

    <span class="text-slate-400 text-[10px]">
        ({{ $serviceCard['reviews_count_display'] }})
    </span>
</div>                                </div>

                                <a href="{{ $serviceCard['details_url'] }}" class="block mb-2">
                                    <h3
                                        class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2 leading-tight">
                                        {{ $serviceCard['title'] }}
                                    </h3>
                                </a>

                                <div class="rich-text text-sm text-slate-500 line-clamp-2 mb-4">
                                    {{ $serviceCard['description_preview'] }}
                                </div>
                                <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium uppercase">Starting at</span>
                                        <div class="text-lg font-bold text-slate-900">
                                            RM{{ $serviceCard['price_display'] }}</div>
                                    </div>
                                    <a href="{{ $serviceCard['details_url'] }}"
                                        class="px-4 py-2 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-md">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        {{-- 🎨 REDESIGNED TOP STUDENTS SECTION (COLORFUL BUTTON) --}}
        <section class="py-16 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <h2 class="text-2xl font-bold text-slate-900">Sellers Available Now</h2>
                    </div>
                    <p class="text-slate-500">Available right now to take your requests.</p>
                </div>

                <div class="flex gap-6 overflow-x-auto pb-8 hide-scroll snap-x snap-mandatory">
                    @foreach ($availableHelpers as $student)
                        <div class="snap-center shrink-0 w-64 group relative">
                            <div
                                class="bg-white rounded-2xl border border-slate-200 p-6 text-center hover:border-indigo-200 hover:shadow-xl transition-all duration-300 relative z-10 h-full flex flex-col items-center">

                                <div class="relative mb-4">
                                    <div
                                        class="w-20 h-20 rounded-full overflow-hidden border-2 border-white shadow-md group-hover:scale-105 transition-transform">
                                        @if ($student['avatar_url'])
                                            <img src="{{ $student['avatar_url'] }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-bold">
                                                {{ $student['initial'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute bottom-0 right-0 w-5 h-5 bg-green-500 border-2 border-white rounded-full"
                                        title="Online"></div>
                                </div>

                                <h3 class="text-lg font-bold text-slate-900 truncate w-full mb-1">{{ $student['name'] }}
                                </h3>
                                <p class="text-xs text-slate-500 mb-3">{{ $student['faculty_display'] }}</p>

                                <div
                                    class="flex items-center justify-center gap-2 mb-4 bg-slate-50 px-3 py-1.5 rounded-full text-xs font-semibold text-slate-700">
                                    <i class="fas fa-star text-yellow-400"></i>
                                    {{ $student['rating_display'] }}
                                    <span class="text-slate-300">|</span>
                                    {{ $student['reviews_count'] }} reviews
                                </div>

                                {{-- Key Change: COLORFUL BUTTON --}}
                                <a href="{{ $student['profile_url'] }}"
                                    class="w-full py-2.5 rounded-xl bg-indigo-600 border border-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 hover:border-indigo-700 transition-all mt-auto shadow-md hover:shadow-lg">
                                    View Profile
                                </a>
                            </div>

                            <div
                                class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl transform rotate-3 scale-[0.98] -z-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        @include('layouts.footer')

    <div id="dashboardConfig" data-search-query="@json($dashboardUi['search_query'])"></div>
    <script src="{{ asset('js/nonadmin-dashboard.js') }}"></script>
</body>

</html>
