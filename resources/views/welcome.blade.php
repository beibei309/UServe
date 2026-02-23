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
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

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
            font-family: 'Poppins', sans-serif;
        }

        /* Custom Scrollbar for horizontal scrolling */
        .hide-scroll-bar::-webkit-scrollbar {
            display: none;
        }

        .hide-scroll-bar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hero-overlay {
            background: linear-gradient(to right, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0.1) 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="antialiased text-slate-800">
    <div x-data="{
        mobileMenuOpen: false,
        activeTab: 'seekers'
    }">

        {{-- Navigation bar --}}
        @include('layouts.navbar')

        <section class="relative min-h-[85vh] flex items-center justify-start overflow-hidden">
            <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
                <source src="{{ asset('videos/herobanner.mp4') }}" type="video/mp4">
            </video>

            <div class="absolute inset-0 z-10 hero-overlay"></div>

            <div class="relative z-20 w-full max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 pt-20">
                <div class="max-w-3xl animate-fade-in-up">
                    <h1 class="text-5xl md:text-6xl font-bold text-white leading-tight mb-6">
                        UPSI Student to Community<br>
                        <span class="text-indigo-500">We've Got You.</span>
                    </h1>
                    <p class="text-lg text-gray-200 mb-8 max-w-2xl font-light">
                        Connect with talented students for services ranging from academic help to creative tasks.
                        Secure, reliable, and community-driven.
                    </p>

                    <div class="bg-white p-2 rounded-2xl shadow-2xl max-w-2xl mb-6 flex items-center">
                        <form action="{{ route('services.index') }}" method="GET" class="w-full flex items-center">
                            <div class="pl-4 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="q" placeholder="What service are you looking for?"
                                class="w-full py-3 px-4 text-gray-700 bg-transparent border-none focus:ring-0 focus:outline-none placeholder-gray-400 text-lg" />
                            <button type="submit"
                                class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-semibold transition-all">
                                Search
                            </button>
                        </form>
                    </div>

                    <div class="flex flex-wrap gap-3 text-sm text-white/90">
                        <span class="py-1">Popular:</span>
                        <a href="{{ route('services.index', ['q' => 'iron baju']) }}"
                            class="px-3 py-1 rounded-full border border-white/30 hover:bg-white/10 transition backdrop-blur-sm cursor-pointer">Iron
                            Baju</a>
                        <a href="{{ route('services.index', ['q' => 'video editing']) }}"
                            class="px-3 py-1 rounded-full border border-white/30 hover:bg-white/10 transition backdrop-blur-sm cursor-pointer">Video
                            Editing</a>
                        <a href="{{ route('services.index', ['q' => 'poster design']) }}"
                            class="px-3 py-1 rounded-full border border-white/30 hover:bg-white/10 transition backdrop-blur-sm cursor-pointer">Poster
                            Design</a>
                        <a href="{{ route('services.index', ['q' => 'pickup']) }}"
                            class="px-3 py-1 rounded-full border border-white/30 hover:bg-white/10 transition backdrop-blur-sm cursor-pointer">Pickup
                            Parcel</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 bg-indigo-200 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10">
                <div class="flex justify-between items-end mb-10">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Explore Categories</h2>
                        <p class="text-gray-500 mt-2">Find exactly what you need.</p>
                    </div>

                    <div class="flex gap-2">
                        <button id="scrollLeft"
                            class="p-2 rounded-full border bg-white hover:bg-gray-50 transition text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button id="scrollRight"
                            class="p-2 rounded-full border bg-white hover:bg-gray-50 transition text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="scrollContainer"
                    class="flex gap-6 overflow-x-auto hide-scroll-bar pb-20 snap-x snap-mandatory">
                    @foreach ($categories ?? [] as $category)
                        <a href="{{ route('services.index', ['category_id' => $category->id]) }}"
                            class="snap-center shrink-0 w-64 p-6 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center group cursor-pointer"
                            style="background-color: {{ $category->color }}; border: 1px solid {{ $category->color }};">

                            <div
                                class="w-16 h-16 rounded-full flex items-center justify-center mb-4 transition-transform group-hover:scale-110 bg-white">
                                <i class="{{ $category->icon ?? 'fa fa-folder' }} text-3xl"
                                    style="color: {{ $category->color }};"></i>
                            </div>

                            <h3 class="text-lg font-bold text-white mb-2">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-200 line-clamp-2">{{ $category->description }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-[0] transform rotate-180">
                <svg class="relative block w-[calc(100%+1.3px)] h-[80px]" data-name="Layer 1"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path
                        d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                        fill="#FFFFFF"></path>
                </svg>
            </div>
        </section>

        <section class="py-24 bg-white relative overflow-hidden">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full opacity-40 pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-50 blur-[120px]"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-purple-50 blur-[120px]">
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-20">
                    <h2 class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3">Advantages</h2>
                    <h3 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-6">
                        Why choose <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">U-Serve</span>
                    </h3>
                    <p class="text-lg text-slate-600 leading-relaxed">
                        We create a safe, reliable environment for students to connect, earn, and collaborate within the
                        UPSI ecosystem.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        class="group bg-white p-10 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col items-center text-center">
                        <div
                            class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mb-8 shadow-lg shadow-blue-200 group-hover:rotate-6 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-4">Verified Students</h4>
                        <p class="text-slate-600 leading-relaxed">Safety first. Every service provider is a verified
                            UPSI student.</p>
                    </div>

                    <div
                        class="group bg-white p-10 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col items-center text-center">
                        <div
                            class="w-16 h-16 bg-purple-600 rounded-2xl flex items-center justify-center text-white mb-8 shadow-lg shadow-purple-200 group-hover:rotate-6 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-4">Transparent Pricing</h4>
                        <p class="text-slate-600 leading-relaxed">What you see is what you pay. No hidden fees or
                            commissions.</p>
                    </div>

                    <div
                        class="group bg-white p-10 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col items-center text-center">
                        <div
                            class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center text-white mb-8 shadow-lg shadow-emerald-200 group-hover:rotate-6 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-4">Community Growth</h4>
                        <p class="text-slate-600 leading-relaxed">Directly empower your peers to develop skills and
                            gain independence.</p>
                    </div>
                </div>
            </div>

        </section>

        <section class="py-20 bg-indigo-200 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full overflow-hidden leading-[0]">
                <svg class="relative block w-[calc(100%+1.3px)] h-[60px]" viewBox="0 0 1200 120"
                    preserveAspectRatio="none">
                    <path
                        d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                        fill="#f8fafc"></path>
                </svg>
            </div>

            <div class="max-w-7xl mx-auto px-6 relative z-10 mt-10 pb-24">
                <div class="text-center mb-16">
                    <h2 class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3">Process</h2>
                    <h3 class="text-4xl font-extrabold text-slate-900 mb-6">How it works</h3>

                    <div
                        class="inline-flex p-1.5 bg-white/50 backdrop-blur-sm border border-slate-200 rounded-2xl shadow-inner">
                        <button @click="activeTab = 'seekers'"
                            :class="activeTab === 'seekers' ? 'bg-slate-900 text-white shadow-lg' :
                                'text-slate-600 hover:bg-white/80'"
                            class="px-8 py-2.5 rounded-xl font-bold transition-all duration-300 text-sm">
                            For Buyers
                        </button>
                        <button @click="activeTab = 'providers'"
                            :class="activeTab === 'providers' ? 'bg-slate-900 text-white shadow-lg' :
                                'text-slate-600 hover:bg-white/80'"
                            class="px-8 py-2.5 rounded-xl font-bold transition-all duration-300 text-sm">
                            For Sellers
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'seekers'" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-8"
                    x-transition:enter-end="opacity-100 translate-y-0" class="text-center">

                    <div class="relative">
                        <div
                            class="hidden md:block absolute top-10 left-0 w-full h-0.5 border-t-2 border-dashed border-slate-300 z-0">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative z-10">
                            <div class="group flex flex-col items-center">
                                <div
                                    class="w-20 h-20 bg-white text-blue-600 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-blue-100 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300 border-2 border-blue-50">
                                    1
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">Search & Browse</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Filter by category, price, or rating to find the perfect match for your needs.
                                </p>
                            </div>

                            <div class="group flex flex-col items-center">
                                <div
                                    class="w-20 h-20 bg-white text-purple-600 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-purple-100 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 border-2 border-purple-50">
                                    2
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">Connect Directly</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Chat with the seller to discuss details, deadlines, and requirements securely.
                                </p>
                            </div>

                            <div class="group flex flex-col items-center">
                                <div
                                    class="w-20 h-20 bg-white text-emerald-600 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-emerald-100 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300 border-2 border-emerald-50">
                                    3
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">Get it Done</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Receive your service and leave a review to help the UPSI community grow.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'providers'" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-8"
                    x-transition:enter-end="opacity-100 translate-y-0" class="text-center">
                    <div class="relative">
                        <div
                            class="hidden md:block absolute top-10 left-0 w-full h-0.5 border-t-2 border-dashed border-orange-200 z-0">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative z-10">
                            <div class="group flex flex-col items-center">
                                <div
                                    class="w-20 h-20 bg-white text-orange-500 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-orange-100 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 border-2 border-orange-50">
                                    1
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">Create Profile</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Sign up with your student ID, complete your bio, and verify your status.
                                </p>
                            </div>

                            <div class="group flex flex-col items-center">
                                <div
                                    class="w-20 h-20 bg-white text-pink-500 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-pink-100 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300 border-2 border-pink-50">
                                    2
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">List Services</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Post your services with clear descriptions, pricing, and attractive images.
                                </p>
                            </div>

                            <div class="group flex flex-col items-center">
                                <div
                                    class="w-20 h-20 bg-white text-teal-500 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-teal-100 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 border-2 border-teal-50">
                                    3
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">Start Earning</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Accept requests, deliver quality work, and get paid directly by your peers.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section class="py-24 bg-slate-950 relative overflow-hidden">
            <div
                class="absolute top-0 left-1/4 w-64 h-64 bg-blue-600/20 rounded-full blur-[100px] pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 right-1/4 w-64 h-64 bg-purple-600/20 rounded-full blur-[100px] pointer-events-none">
            </div>

            <div class="absolute inset-0 opacity-10 pointer-events-none"
                style="background-image: radial-gradient(#475569 0.5px, transparent 0.5px); background-size: 24px 24px;">
            </div>

            <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">
                <span class="text-blue-400 font-bold tracking-[0.2em] uppercase text-xs mb-4 block">
                    Become part of the community
                </span>

                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-8 tracking-tight">
                    Ready to get <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">started?</span>
                </h2>

                <p class="text-slate-400 text-lg md:text-xl mb-12 max-w-2xl mx-auto leading-relaxed">
                    Join hundreds of UPSI students who are already connecting, learning, and earning on U-Serve today.
                </p>

                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    @auth
                        <a href="{{ route('services.index') }}"
                            class="group relative px-10 py-4 bg-white text-slate-950 rounded-2xl font-bold hover:scale-105 transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                            Find Services
                            <span class="inline-block ml-2 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="group relative px-10 py-4 bg-white text-slate-950 rounded-2xl font-bold hover:scale-105 transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                            Join Now - It's Free
                            <span class="inline-block ml-2 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                        <a href="{{ route('login') }}"
                            class="px-10 py-4 rounded-2xl font-bold text-white border border-slate-700 hover:bg-slate-800 transition-all duration-300">
                            Log In
                        </a>
                    @endauth
                </div>

                <div class="mt-12 flex items-center justify-center gap-2 text-slate-500 text-sm">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Exclusively for UPSI Students and local Tg.Malim
                </div>
            </div>
        </section>

        @include('layouts.footer')

    </div>

    <script>
        const scrollContainer = document.getElementById('scrollContainer');
        const scrollLeftButton = document.getElementById('scrollLeft');
        const scrollRightButton = document.getElementById('scrollRight');

        scrollLeftButton.addEventListener('click', () => {
            scrollContainer.scrollBy({
                left: -300,
                behavior: 'smooth'
            });
        });

        scrollRightButton.addEventListener('click', () => {
            scrollContainer.scrollBy({
                left: 300,
                behavior: 'smooth'
            });
        });
    </script>
</body>

</html>
