<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    /* Custom utility for active link indication */
    .nav-link-active {
        color: #4f46e5 !important;
        /* Indigo-600 */
        background-color: #f3f4f6;
        /* Gray-100 */
    }
</style>

{{-- INITIALIZE LOGIC --}}
@php
    $user = auth()->user();
    $isLoggedIn = auth()->check();
    $isHelper = $isLoggedIn && $user->role === 'helper';

    // Determine View Mode: 'seller' or 'buyer' (Default)
    // If user is not logged in, default to buyer.
    $viewMode = session('view_mode', 'buyer');

    // Safety check: If role is student, force buyer mode
    if ($isLoggedIn && $user->role === 'student') {
        $viewMode = 'buyer';
    }
@endphp

{{-- TOP UTILITY BAR --}}
<div class="w-full bg-white border-b border-slate-100 font-sans sticky top-0 z-50">
    <div class="max-w-7xl mx-auto h-16 md:h-20 flex items-center justify-between px-6 relative">

        <div class="hidden md:flex items-center gap-4 text-slate-400 text-[11px] font-bold uppercase tracking-widest">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-calendar-day text-indigo-500"></i>
                {{ now()->format('D, d M Y') }}
            </span>
        </div>

        {{-- CENTER LOGO: Dengan Kesan Hover --}}
        <div class="absolute left-1/2 transform -translate-x-1/2 z-20">
            <a href="https://www.upsi.edu.my/" class="block transition-transform duration-300 hover:scale-105 active:scale-95">
                <img src="{{ asset('images/upsilogo.png') }}" class="h-12 md:h-16 w-auto object-contain"
                    alt="UPSI Logo">
            </a>
        </div>

        {{-- SOCIAL ICONS RIGHT: Gaya Card-based --}}
        <div class="ml-auto flex items-center gap-2.5">

            <a href="https://www.facebook.com/UPSIMalaysia/" target="_blank"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-[#1877F2] hover:text-white hover:shadow-lg hover:shadow-blue-200 transition-all duration-300"
                title="Facebook">
                <i class="fa-brands fa-facebook-f text-sm"></i>
            </a>

            <a href="https://x.com/UPSI_Malaysia" target="_blank"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white hover:shadow-lg hover:shadow-slate-300 transition-all duration-300"
                title="X (Twitter)">
                <i class="fa-brands fa-x-twitter text-sm"></i>
            </a>

            <a href="https://www.instagram.com/upsi_malaysia" target="_blank"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:text-white hover:shadow-lg hover:shadow-pink-200 transition-all duration-300"
                title="Instagram">
                <i class="fa-brands fa-instagram text-sm"></i>
            </a>

        </div>
    </div>
</div>


<nav x-data="{ mobileMenuOpen: false, userOpen: false }" class="bg-white shadow-sm sticky top-0 w-full z-50 border-b border-gray-100">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 md:h-20">

            <div class="flex-shrink-0 flex items-center cursor-pointer gap-2"
                onclick="window.location.href='{{ $isLoggedIn ? route('dashboard') : route('home') }}'">

                <img src="{{ asset('images/logo-svg.png') }}" alt="U-Serve Logo" class="h-20 w-auto object-contain">

                @if ($viewMode === 'seller')
                    <span
                        class="ml-2 px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 uppercase tracking-wide">
                        Seller Mode
                    </span>
                @endif
            </div>

            <div class="hidden md:flex items-center space-x-1 lg:space-x-4">

                @if ($viewMode === 'seller')
                    {{-- LINKS FOR SELLER / HELPER DASHBOARD --}}
                    <a href="{{ route('students.index') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 transition-colors duration-200 {{ request()->routeIs('students.index') ? 'nav-link-active' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('services.manage') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 transition-colors duration-200 {{ request()->routeIs('services.manage') ? 'nav-link-active' : '' }}">
                        My Services
                    </a>
                    {{-- Points to same index route, but Controller shows Sales data --}}
                    <a href="{{ route('service-requests.index') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 transition-colors duration-200 {{ request()->routeIs('service-requests.index') ? 'nav-link-active' : '' }}">
                        Incoming Orders
                    </a>
                @else
                    {{-- LINKS FOR BUYER / STUDENT --}}
                    <a href="{{ $isLoggedIn ? route('dashboard') : route('home') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 transition-colors duration-200">
                        Home
                    </a>
                    <a href="{{ route('services.index') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 transition-colors duration-200 {{ request()->routeIs('services.index') ? 'nav-link-active' : '' }}">
                        Find Services
                    </a>
                    <a href="{{ route('about') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 transition-colors duration-200">
                        About Us
                    </a>
                    <a href="{{ route('help') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 transition-colors duration-200">
                        Help
                    </a>
                @endif
            </div>

            <div class="hidden md:flex items-center space-x-3 lg:space-x-4">
                @auth
                    <div class="flex items-center space-x-2 border-r border-gray-200 pr-4 mr-2">
                        <a href="{{ route('notifications.index') }}"
                            class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-full transition focus:outline-none">
                            <span class="sr-only">View notifications</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 7 8.388 7 11v3.159c0 .538-.214 1.055-.595 1.436L5 17h10z" />
                            </svg>
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                </span>
                            @endif
                        </a>

                        {{-- <a href="{{ route('chat.index') }}" class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-full transition">
                            <span class="sr-only">Messages</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </a> --}}

                        {{-- BUYER-ONLY ICONS --}}
                        @if ($viewMode === 'buyer')
                            <a href="{{ route('favorites.index') }}"
                                class="relative p-2 text-gray-500 hover:text-red-500 hover:bg-gray-100 rounded-full transition">
                                <span class="sr-only">Favorites</span>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </a>

                            {{-- Points to same index route, but Controller shows Purchase data --}}
                            <a href="{{ route('service-requests.index') }}"
                                class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition px-2">
                                Orders
                            </a>
                        @endif
                    </div>

                    {{-- SWITCH MODE BUTTONS --}}
                    @if ($user->role === 'student')
                        <a href="{{ route('onboarding.students') }}"
                            class="hidden lg:inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Become a Seller
                        </a>
                    @elseif ($isHelper)
                        <form action="{{ route('switch.mode') }}" method="POST" class="hidden lg:inline-flex">
                            @csrf
                            <button type="submit"
                                class="items-center px-4 py-2 border text-sm font-medium rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors
                                {{ $viewMode === 'seller'
                                    ? 'border-indigo-600 text-indigo-600 bg-white hover:bg-indigo-50 focus:ring-indigo-500'
                                    : 'border-green-600 text-green-600 bg-white hover:bg-green-50 focus:ring-green-500' }}">
                                {{ $viewMode === 'seller' ? 'Switch to Buying' : 'Switch to Selling' }}
                            </button>
                        </form>
                    @endif

                    <div class="relative ml-3" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen" type="button"
                            class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 relative"
                            id="user-menu-button">
                            <span class="sr-only">Open user menu</span>
                            <img class="h-9 w-9 rounded-full object-cover border border-gray-200"
                                src="{{ $user->profile_photo_path ? asset($user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                alt="{{ $user->name }}">
                            @if ($user->verification_status === 'approved')
                                <span
                                    class="absolute -bottom-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-blue-500 ring-2 ring-white"
                                    title="Verified">
                                    <svg class="h-2.5 w-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @endif
                        </button>

                        <div x-show="userOpen" @click.away="userOpen = false"
                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                            style="display: none;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm text-gray-900 font-semibold truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600">Your
                                Profile</a>



                            <div class="border-t border-gray-100 mt-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">Sign
                                    out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">Log in</a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Sign up
                        </a>
                    </div>
                @endauth
            </div>

            <div class="-mr-2 flex md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                    class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="sr-only">Open main menu</span>
                    <svg :class="{ 'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" class="block h-6 w-6"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" class="hidden h-6 w-6"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen"
        class="md:hidden absolute top-16 inset-x-0 z-50 bg-white border-b border-gray-200 shadow-lg" id="mobile-menu"
        style="display: none;">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @if ($viewMode === 'seller')
                {{-- MOBILE SELLER LINKS --}}
                <a href="{{ route('students.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Dashboard</a>
                <a href="{{ route('services.manage') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">My
                    Services</a>
                <a href="{{ route('service-requests.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Incoming
                    Orders</a>
            @else
                {{-- MOBILE BUYER LINKS --}}
                <a href="{{ $isLoggedIn ? route('dashboard') : route('home') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Home</a>
                <a href="{{ route('services.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Find
                    Services</a>
                <a href="{{ route('about') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">About
                    Us</a>
                <a href="{{ route('help') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Help</a>
            @endif
        </div>

        @auth
            <div class="pt-4 pb-4 border-t border-gray-200">
                <div class="flex items-center px-5">
                    <div class="flex-shrink-0 relative">
                        <img class="h-10 w-10 rounded-full border border-gray-200"
                            src="{{ $user->profile_photo_path ? asset($user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                            alt="">
                        @if ($user->verification_status === 'approved')
                            <span
                                class="absolute -bottom-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-blue-500 ring-2 ring-white"
                                title="Verified">
                                <svg class="h-2.5 w-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium text-gray-800">{{ $user->name }}</div>
                        <div class="text-sm font-medium text-gray-500">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a href="{{ route('profile.edit') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Your
                        Profile</a>
                    {{-- <a href="{{ route('chat.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Messages</a> --}}

                    @if ($viewMode === 'buyer')
                        <a href="{{ route('favorites.index') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Favorites</a>
                        <a href="{{ route('service-requests.index') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50">Orders</a>
                    @endif

                    @if ($user->role === 'student')
                        <a href="{{ route('onboarding.students') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-indigo-600 hover:bg-indigo-50">Become
                            a Seller</a>
                    @elseif ($isHelper)
                        {{-- MOBILE SWITCH BUTTON --}}
                        <form action="{{ route('switch.mode') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full text-left block px-3 py-2 rounded-md text-base font-medium {{ $viewMode === 'seller' ? 'text-indigo-600 hover:bg-indigo-50' : 'text-green-600 hover:bg-green-50' }}">
                                {{ $viewMode === 'seller' ? 'Switch to Buying' : 'Switch to Selling' }}
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50 hover:text-red-700">Sign
                            out</button>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-4 border-t border-gray-200">
                <div class="flex items-center justify-around px-5">
                    <a href="{{ route('login') }}" class="text-base font-medium text-gray-600 hover:text-indigo-600">Log
                        in</a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Sign
                        up</a>
                </div>
            </div>
        @endauth
    </div>
</nav>
