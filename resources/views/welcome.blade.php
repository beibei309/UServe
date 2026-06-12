<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UPSI2u | UPSI Service Circle</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

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
    @php
        $heroVideo = \App\Models\PageContent::get('welcome.hero_video', 'videos/herobanner.mp4');
        $heroTitleLegacy = \App\Models\PageContent::get('welcome.hero_title', "UPSI Student to Community\nWe've Got You.");
        $heroTitleLegacyLines = preg_split('/\r\n|\r|\n/', (string) $heroTitleLegacy, 2);

        $heroTitlePrimary = trim((string) \App\Models\PageContent::get('welcome.hero_title_line_1', trim($heroTitleLegacyLines[0] ?? 'UPSI Student to Community')));
        $heroTitleAccent = trim((string) \App\Models\PageContent::get('welcome.hero_title_highlight', trim($heroTitleLegacyLines[1] ?? "We've Got You.")));
        $heroTitleAccentColor = trim((string) \App\Models\PageContent::get('welcome.hero_title_highlight_color', '#818cf8'));
        $heroTitleLine2 = trim((string) \App\Models\PageContent::get('welcome.hero_title_line_2', ''));

        if (!preg_match('/^#[0-9A-Fa-f]{3}(?:[0-9A-Fa-f]{3})?$/', $heroTitleAccentColor)) {
            $heroTitleAccentColor = '#818cf8';
        }
        $heroSubtitle = \App\Models\PageContent::get('welcome.hero_subtitle', 'Connect with talented students for services ranging from academic help to creative tasks. Secure, reliable, and community-driven.');
        $featuresBadge = \App\Models\PageContent::get('welcome.features_badge', 'Advantages');
        $featuresTitle = \App\Models\PageContent::get('welcome.features_title', 'Why choose UPSI2u');
        $featuresSubtitle = \App\Models\PageContent::get('welcome.features_subtitle', 'We create a safe, reliable environment for students to connect, earn, and collaborate within the UPSI ecosystem.');
        $feature1Title = \App\Models\PageContent::get('welcome.feature_1_title', 'Verified Students');
        $feature1Desc = \App\Models\PageContent::get('welcome.feature_1_desc', 'Safety first. Every service provider is a verified UPSI student.');
        $feature2Title = \App\Models\PageContent::get('welcome.feature_2_title', 'Transparent Pricing');
        $feature2Desc = \App\Models\PageContent::get('welcome.feature_2_desc', 'What you see is what you pay. No hidden fees or commissions.');
        $feature3Title = \App\Models\PageContent::get('welcome.feature_3_title', 'Community Growth');
        $feature3Desc = \App\Models\PageContent::get('welcome.feature_3_desc', 'Directly empower your peers to develop skills and gain independence.');
        $ctaBadge = \App\Models\PageContent::get('welcome.cta_badge', 'Become part of the community');
        $ctaTitle = \App\Models\PageContent::get('welcome.cta_title', 'Ready to get started?');
        $ctaSubtitle = \App\Models\PageContent::get('welcome.cta_subtitle', 'Join hundreds of UPSI students who are already connecting, learning, and earning on UPSI2u today.');
        $ctaFootnote = \App\Models\PageContent::get('welcome.cta_footnote', 'Exclusively for UPSI Students and local Tg.Malim');
    @endphp
    <div x-data="{
        mobileMenuOpen: false,
        activeTab: 'seekers'
    }">

        {{-- Navigation bar --}}
        @include('layouts.navbar')

        <section class="relative min-h-[72vh] md:min-h-[78vh] flex items-center justify-start overflow-hidden">
            <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
                <source src="{{ asset($heroVideo) }}" type="video/mp4">
            </video>

            <div class="absolute inset-0 z-10 hero-overlay"></div>

            <div class="relative z-20 w-full max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 pt-8 md:pt-12 pb-8">
                <div class="max-w-3xl animate-fade-in-up">
                    <h1 class="text-3xl sm:text-4xl md:text-6xl font-bold leading-tight mb-5">
                        <span class="block text-white">{{ $heroTitlePrimary }}</span>
                        @if (!empty($heroTitleAccent))
                            <span class="block" style="color: {{ $heroTitleAccentColor }};">{{ $heroTitleAccent }}</span>
                        @endif
                        @if (!empty($heroTitleLine2))
                            <span class="block text-white">{{ $heroTitleLine2 }}</span>
                        @endif
                    </h1>
                    <p class="text-base sm:text-lg text-gray-200 mb-6 max-w-2xl font-light">
                        {{ $heroSubtitle }}
                    </p>

                    <div class="bg-white p-2 rounded-2xl shadow-lg max-w-2xl mb-4">
                        <form action="{{ route('services.index') }}" method="GET" class="w-full flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="hidden sm:block pl-4 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="q" placeholder="What service are you looking for?"
                                class="w-full py-3 px-3 sm:px-4 text-gray-700 bg-transparent border-none focus:ring-0 focus:outline-none placeholder-gray-400 text-base sm:text-lg" />
                            <button type="submit"
                                class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                                Search
                            </button>
                        </form>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-sm text-white/90">
                        <span class="py-1 mr-1">Popular:</span>
                        @foreach (($popularSearches ?? []) as $popularSearch)
                            <a href="{{ route('services.index', ['q' => $popularSearch['query']]) }}"
                                class="px-3 py-1 rounded-full border border-white/30 hover:bg-white/10 transition backdrop-blur-sm cursor-pointer">{{ $popularSearch['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="py-10 sm:py-14 bg-indigo-100 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10">
                <div class="flex justify-between items-end gap-4 mb-6 sm:mb-8">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Explore Categories</h2>
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
                    class="flex gap-4 sm:gap-6 overflow-x-auto hide-scroll-bar pb-10 sm:pb-16 snap-x snap-mandatory">
                    @forelse ($categories ?? [] as $category)
                        @php($categoryColor = $category->hc_color ?: '#4f46e5')
                        <a href="{{ route('services.index', ['category_id' => $category->hc_id]) }}"
                            class="snap-center shrink-0 w-56 sm:w-64 p-5 sm:p-6 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center group cursor-pointer"
                            style="background-color: {{ $categoryColor }}; border: 1px solid {{ $categoryColor }};">

                            <div
                                class="w-16 h-16 rounded-full flex items-center justify-center mb-4 transition-transform group-hover:scale-110 bg-white">
                                <i class="{{ $category->hc_icon ?? 'fa fa-folder' }} text-3xl"
                                    style="color: {{ $categoryColor }};"></i>
                            </div>

                            <h3 class="text-lg font-bold text-white mb-2">{{ $category->hc_name }}</h3>
                            <p class="text-sm text-gray-200 line-clamp-2">{{ $category->hc_description }}</p>
                        </a>
                    @empty
                        <div class="w-full bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-600">
                            No categories available yet.
                        </div>
                    @endforelse
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

        <section class="upsi-section bg-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
                    <h2 class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3">{{ $featuresBadge }}</h2>
                    <h3 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-5">
                        {{ $featuresTitle }}
                    </h3>
                    <p class="text-lg text-slate-600 leading-relaxed">
                        {{ $featuresSubtitle }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-8">
                    <div
                        class="upsi-card upsi-card-hover p-6 sm:p-8 flex flex-col items-center text-center">
                        <div
                            class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white mb-5 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-4">{{ $feature1Title }}</h4>
                        <p class="text-slate-600 leading-relaxed">{{ $feature1Desc }}</p>
                    </div>

                    <div
                        class="upsi-card upsi-card-hover p-6 sm:p-8 flex flex-col items-center text-center">
                        <div
                            class="w-14 h-14 bg-purple-600 rounded-2xl flex items-center justify-center text-white mb-5 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-4">{{ $feature2Title }}</h4>
                        <p class="text-slate-600 leading-relaxed">{{ $feature2Desc }}</p>
                    </div>

                    <div
                        class="upsi-card upsi-card-hover p-6 sm:p-8 flex flex-col items-center text-center">
                        <div
                            class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center text-white mb-5 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-4">{{ $feature3Title }}</h4>
                        <p class="text-slate-600 leading-relaxed">{{ $feature3Desc }}</p>
                    </div>
                </div>
            </div>

        </section>

        <section class="py-12 sm:py-16 bg-indigo-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full overflow-hidden leading-[0]">
                <svg class="relative block w-[calc(100%+1.3px)] h-[60px]" viewBox="0 0 1200 120"
                    preserveAspectRatio="none">
                    <path
                        d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                        fill="#f8fafc"></path>
                </svg>
            </div>

            <div class="max-w-7xl mx-auto px-6 relative z-10 mt-8 pb-12 sm:pb-20">
                <div class="text-center mb-8 sm:mb-12">
                    <h2 class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3">Process</h2>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-5">How it works</h3>

                    <div
                        class="inline-flex w-full sm:w-auto p-1.5 bg-white/80 border border-slate-200 rounded-2xl shadow-sm">
                        <button @click="activeTab = 'seekers'"
                            :class="activeTab === 'seekers' ? 'bg-slate-900 text-white shadow-lg' :
                                'text-slate-600 hover:bg-white/80'"
                            class="flex-1 sm:flex-none px-4 sm:px-8 py-2.5 rounded-xl font-bold transition-all duration-300 text-sm">
                            For Buyers
                        </button>
                        <button @click="activeTab = 'providers'"
                            :class="activeTab === 'providers' ? 'bg-slate-900 text-white shadow-lg' :
                                'text-slate-600 hover:bg-white/80'"
                            class="flex-1 sm:flex-none px-4 sm:px-8 py-2.5 rounded-xl font-bold transition-all duration-300 text-sm">
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 relative z-10">
                            <div class="upsi-card p-5 sm:p-6 flex flex-col items-center">
                                <div
                                    class="w-14 h-14 bg-white text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-blue-50">
                                    1
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">Search & Browse</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Filter by category, price, or rating to find the perfect match for your needs.
                                </p>
                            </div>

                            <div class="upsi-card p-5 sm:p-6 flex flex-col items-center">
                                <div
                                    class="w-14 h-14 bg-white text-purple-600 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-purple-50">
                                    2
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">Connect Directly</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Chat with the seller to discuss details, deadlines, and requirements securely.
                                </p>
                            </div>

                            <div class="upsi-card p-5 sm:p-6 flex flex-col items-center">
                                <div
                                    class="w-14 h-14 bg-white text-emerald-600 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-emerald-50">
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 relative z-10">
                            <div class="upsi-card p-5 sm:p-6 flex flex-col items-center">
                                <div
                                    class="w-14 h-14 bg-white text-orange-500 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-orange-50">
                                    1
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">Create Profile</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Sign up with your student ID, complete your bio, and verify your status.
                                </p>
                            </div>

                            <div class="upsi-card p-5 sm:p-6 flex flex-col items-center">
                                <div
                                    class="w-14 h-14 bg-white text-pink-500 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-pink-50">
                                    2
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-3">List Services</h3>
                                <p class="text-slate-600 leading-relaxed max-w-xs">
                                    Post your services with clear descriptions, pricing, and attractive images.
                                </p>
                            </div>

                            <div class="upsi-card p-5 sm:p-6 flex flex-col items-center">
                                <div
                                    class="w-14 h-14 bg-white text-teal-500 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-teal-50">
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

        <section class="py-12 sm:py-16 bg-slate-950 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 pointer-events-none"
                style="background-image: radial-gradient(#475569 0.5px, transparent 0.5px); background-size: 24px 24px;">
            </div>

            <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">
                <span class="text-blue-400 font-bold tracking-[0.2em] uppercase text-xs mb-4 block">
                    {{ $ctaBadge }}
                </span>

                <h2 class="text-3xl md:text-5xl font-black text-white mb-5 tracking-tight">
                    {{ $ctaTitle }}
                </h2>

                <p class="text-slate-400 text-base md:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                    {{ $ctaSubtitle }}
                </p>

                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    @auth
                        <a href="{{ route('services.index') }}"
                            class="group relative w-full sm:w-auto px-6 py-3 bg-white text-slate-950 rounded-xl font-bold hover:bg-indigo-50 transition-all duration-300">
                            Find Services
                            <span class="inline-block ml-2 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="group relative w-full sm:w-auto px-6 py-3 bg-white text-slate-950 rounded-xl font-bold hover:bg-indigo-50 transition-all duration-300">
                            Join Now - It's Free
                            <span class="inline-block ml-2 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-white border border-slate-700 hover:bg-slate-800 transition-all duration-300">
                            Log In
                        </a>
                    @endauth
                </div>

                <div class="mt-8 flex items-center justify-center gap-2 text-slate-500 text-sm">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ $ctaFootnote }}
                </div>
            </div>
        </section>

        @include('layouts.footer')

    </div>

    <div id="welcomeScrollConfig" data-step="300"></div>
    <script src="{{ asset('js/welcome.js') }}"></script>
</body>

</html>
