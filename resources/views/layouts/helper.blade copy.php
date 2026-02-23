<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="upsi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('S2U', 'S2U - Student to Community') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
        
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>



<body class="font-sans antialiased bg-white">
    <div x-data="{
        sidebarOpen: false,
        isMobile: window.innerWidth < 1024,
        init() {
            this.checkScreenSize();
            window.addEventListener('resize', () => this.checkScreenSize());
        },
        checkScreenSize() {
            this.isMobile = window.innerWidth < 1024;
            if (!this.isMobile) {
                this.sidebarOpen = false;
            }
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        }
    }" class="min-h-screen bg-white">

    @include('layouts.navbar')
        <!-- Mobile Navigation Bar -->
        <div class="lg:hidden bg-upsi-blue shadow-lg text-white sticky top-0 z-50">
            <div class="flex items-center justify-between px-4 py-4">
                <button @click="toggleSidebar()" class="p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-upsi-gold rounded-lg flex items-center justify-center">
                        <span class="text-upsi-blue font-bold text-sm">U</span>
                    </div>
                    <h1 class="text-lg font-bold">UpsiConnect</h1>
                </div>

                <!-- Mobile User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors">
                        <img class="h-8 w-8 rounded-full border-2 border-upsi-gold"
                            src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=ffffff&background=C41E3A"
                            alt="{{ Auth::user()->name }}">
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl ring-1 ring-black/5 divide-y divide-gray-100">

                        <div class="px-4 py-3">
                            <p class="text-sm text-upsi-text-primary/60">Signed in as</p>
                            <p class="text-sm font-semibold text-upsi-text-primary truncate">{{ Auth::user()->name }}
                            </p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-upsi-text-primary hover:bg-upsi-light-gray transition-colors">
                                Profile Settings
                            </a>
                        </div>

                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-upsi-red hover:bg-red-50 transition-colors">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen && isMobile" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"></div>

        <div class="flex">
            <!-- Desktop Sidebar -->
            <div
                class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-white border-r border-gray-100 shadow-sm">
                <!-- Desktop Header -->
                <div class="flex items-center justify-center h-16 px-6 bg-upsi-blue shadow-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-upsi-gold rounded-lg flex items-center justify-center">
                            <span class="text-upsi-blue font-bold text-sm">U</span>
                        </div>
                        <a href="{{ route('dashboard') }}"
                            class="text-xl font-bold text-white hover:text-upsi-gold transition-colors">
                            UpsiConnect
                        </a>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto bg-white">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Student-specific menu items -->
                    @if (auth()->user()->role === 'helper')
                        <a href="{{ route('services.manage') }}"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('services.manage') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Service Management
                        </a>

                        <a href="{{ route('service-requests.index') }}"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('service-requests.*') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Service Requests
                        </a>

                        <a href="{{ route('services.applications.index') }}"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('services.applications.*') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Open Applications
                        </a>
                    @endif

                    <!-- Community-specific menu items -->
                    @if (auth()->user()->isCommunity())

                        <a href="{{ route('service-requests.index') }}"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('service-requests.*') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            My Service Requests
                        </a>

                        <a href="{{ route('services.applications.index') }}"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('services.applications.*') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            My Applications
                        </a>

                        <a href="{{ route('services.apply') }}"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('services.apply') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Request Custom Service
                        </a>
                    @endif

                    <!-- Common menu items -->
                    <a href="{{ route('chat.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('chat.*') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Messages
                        <span id="messageNotificationBadge"
                            class="ml-auto bg-upsi-red text-white text-xs font-semibold px-2.5 py-1 rounded-full shadow-sm hidden">0</span>
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-4"></div>

                    <!-- Settings -->
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-upsi-blue text-white shadow-md' : 'text-upsi-text-primary hover:bg-upsi-light-gray hover:text-upsi-blue' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                </nav>

    
            </div>

            <!-- Mobile Sidebar -->
            <div x-show="sidebarOpen && isMobile" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg lg:hidden">

                <!-- Mobile Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-6 bg-upsi-blue">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-white">
                        UpsiConnect
                    </a>
                    <button @click="sidebarOpen = false" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Student-specific menu items -->
                    @if (auth()->user()->role === 'helper')
                        <a href="{{ route('services.manage') }}" @click="sidebarOpen = false"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('services.manage') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Service Management
                        </a>

                        <a href="{{ route('service-requests.index') }}" @click="sidebarOpen = false"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('service-requests.*') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Service Requests
                        </a>

                        <a href="{{ route('services.applications.index') }}" @click="sidebarOpen = false"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('services.applications.*') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Open Applications
                        </a>
                    @endif

                    <!-- Community-specific menu items -->
                    @if (auth()->user()->isCommunity())
                        <a href="{{ route('search.index') }}" @click="sidebarOpen = false"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('search.*') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Browse Services
                        </a>

                        <a href="{{ route('favorites.index') }}" @click="sidebarOpen = false"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('favorites.*') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            My Favorites
                        </a>

                        <a href="{{ route('service-requests.index') }}" @click="sidebarOpen = false"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('service-requests.*') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            My Service Requests
                        </a>

                        <a href="{{ route('services.applications.index') }}" @click="sidebarOpen = false"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('services.applications.*') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            My Applications
                        </a>

                        <a href="{{ route('services.apply') }}" @click="sidebarOpen = false"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('services.apply') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Request Custom Service
                        </a>
                    @endif

                    <!-- Common menu items -->
                    <a href="{{ route('chat.index') }}" @click="sidebarOpen = false"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-upsi-light hover:text-upsi-blue">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        Messages
                        <span id="mobileMessageNotificationBadge"
                            class="ml-auto bg-upsi-red text-white text-xs font-medium px-2 py-1 rounded-full hidden">0</span>
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-4"></div>

                    <!-- Settings -->
                    <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('profile.*') ? 'bg-upsi-blue text-white' : 'text-gray-700 hover:bg-upsi-light hover:text-upsi-blue' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>

                    <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-upsi-light hover:text-upsi-blue">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Settings
                    </a>
                </nav>

               
            </div>
           
                <!-- Main Content Area -->
                <div class="flex-1 lg:ml-64 bg-white">
                    @yield('content')
                </div>

            </div>
        </div>
    </div>



    <script>
        // Initialize notification system
        let unreadCount = 0;

        // Function to update notification badges
        function updateNotificationBadges(count) {
            const desktopBadge = document.getElementById('messageNotificationBadge');
            const mobileBadge = document.getElementById('mobileMessageNotificationBadge');

            if (count > 0) {
                if (desktopBadge) {
                    desktopBadge.textContent = count;
                    desktopBadge.classList.remove('hidden');
                }
                if (mobileBadge) {
                    mobileBadge.textContent = count;
                    mobileBadge.classList.remove('hidden');
                }
            } else {
                if (desktopBadge) {
                    desktopBadge.classList.add('hidden');
                }
                if (mobileBadge) {
                    mobileBadge.classList.add('hidden');
                }
            }
        }

        // Listen for new message notifications if user is authenticated
        @auth
        if (window.Echo) {
            window.Echo.private('user.{{ auth()->id() }}')
                .listen('NewMessageNotification', (e) => {
                    // Only increment if not on the current conversation page
                    const currentPath = window.location.pathname;
                    const conversationPath = `/chat/${e.conversation_id}`;

                    if (currentPath !== conversationPath) {
                        unreadCount++;
                        updateNotificationBadges(unreadCount);

                        // Show browser notification if permission granted
                        if (Notification.permission === 'granted') {
                            new Notification(`New message from ${e.sender_name}`, {
                                body: e.message_content.substring(0, 100) + (e.message_content.length > 100 ?
                                    '...' : ''),
                                icon: '/favicon.ico'
                            });
                        }
                    }
                });
        }

        // Request notification permission on page load
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // Reset unread count when visiting chat pages
        if (window.location.pathname.startsWith('/chat')) {
            unreadCount = 0;
            updateNotificationBadges(0);
        }
        @endauth
    </script>
    @stack('scripts')

</body>

</html>
