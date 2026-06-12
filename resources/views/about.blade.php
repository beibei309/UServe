<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UPSI2u | UPSI Service Circle</title>
    <link rel="icon" type="image/png" href="/images/logo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #6b7fd7 0%, #7c8ee0 100%);
        }

        .stats-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 3rem;
            text-align: center;
        }

        .stat-item {
            margin: 1.5rem 0;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #6b7fd7;
            display: block;
            line-height: 1;
        }

        .stat-label {
            font-size: 1rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .feature-box {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-4px);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: #666;
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem;
            line-height: 1.6;
        }

        .btn-primary {
            background: #6b7fd7;
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #5a6ec6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 127, 215, 0.3);
        }

        .step-number {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: #6b7fd7;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .tab-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .tab-btn {
            padding: 0.75rem 2rem;
            border: 2px solid #6b7fd7;
            background: white;
            color: #6b7fd7;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: #6b7fd7;
            color: white;
        }

        .hero-image-placeholder {
            background: #e5e7eb;
            border-radius: 50%;
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 1.25rem;
            font-weight: 600;
            max-width: 400px;
            margin: 0 auto;
        }

        .story-image-placeholder {
            background: #e5e7eb;
            border-radius: 12px;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
</head>

<body class="antialiased bg-white text-slate-800">
    @php
        $aboutHeroBadge = \App\Models\PageContent::get('about.hero_badge', 'Our Mission');
        $aboutHeroTitle = \App\Models\PageContent::get('about.hero_title', 'Empowering the UPSI Community through UPSI2u');
        $aboutHeroDescription = \App\Models\PageContent::get('about.hero_description', 'UPSI2u (UPSI Service Circle) is more than just a marketplace. It is a dedicated ecosystem designed specifically for UPSI students to bridge the gap between talent and needs. Whether you\'re looking for expert tutoring, creative design, or technical coding help, your peers are here to deliver.');
        $aboutStoryTitle = \App\Models\PageContent::get('about.story_title', 'Built by Students, For the Community.');
        $aboutStoryQuote = \App\Models\PageContent::get('about.story_quote', '"UPSI2u was developed in 2025 out of a simple need: a trusted, friendly, and more effective way for UPSI students to help one another."');
        $aboutStoryBody1 = \App\Models\PageContent::get('about.story_body_1', 'Founded by a group of students who experienced the frustration of searching for reliable academic help and creative services. Tired of unreliable providers and cluttered listings, they decided to build the solution the UPSI community deserved.');
        $aboutStoryHighlight = \App\Models\PageContent::get('about.story_highlight', 'What started as a small project has now become a movement, transforming how we connect and support each other\'s financial and academic growth.');
        $aboutStoryBody2 = \App\Models\PageContent::get('about.story_body_2', 'Today, UPSI2u stands as a leader in student-led services at UPSI, continuously growing as more students turn to us for verified, peer-to-peer excellence.');
        $aboutCtaTitle = \App\Models\PageContent::get('about.cta_title', 'Ready to be part of the movement?');
        $aboutCtaSubtitle = \App\Models\PageContent::get('about.cta_subtitle', 'Join UPSI2u and grow together with your campus community.');
        $aboutHeroImage = \App\Models\PageContent::get('about.hero_image', 'images/about.jpg');
        $aboutStoryImage = \App\Models\PageContent::get('about.story_image', 'images/about2.jpg');
    @endphp
    <div x-data="{
        activeTab: 'seekers'
    }" class="min-h-screen">

        <!-- Navigation -->
        @include('layouts.navbar')

        <!-- Hero Section -->
        <section class="upsi-section bg-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14 items-center mb-10 sm:mb-14">

                    <div class="order-2 lg:order-1">
                        <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-4 block">{{ $aboutHeroBadge }}</span>
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 mb-5 leading-tight">
                            {{ $aboutHeroTitle }}
                        </h1>
                        <p class="text-base sm:text-lg text-slate-600 leading-relaxed mb-6">
                            {{ $aboutHeroDescription }}
                        </p>

                        <div class="flex flex-wrap gap-4">
                            @auth
                                <a href="{{ route('services.index') }}"
                                    class="upsi-primary-action gap-2">
                                    Find Your Next Service
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="upsi-primary-action">
                                    Join UPSI2u Today!
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="order-1 lg:order-2 relative">
                        <div
                            class="relative z-10 rounded-2xl overflow-hidden shadow-lg border border-slate-200">
                            <div
                                class="aspect-video bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center group">
                                <img src="{{ asset($aboutHeroImage) }}" alt="Students Collaborating"
                                    class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                <div class="absolute inset-0 flex items-center justify-center p-4">
                                    <span
                                        class="bg-white/90 backdrop-blur px-3 py-2 rounded-full text-slate-900 font-bold text-xs sm:text-sm shadow-sm">UPSI
                                        Talent in Action</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="upsi-card bg-slate-50 p-5 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-10 text-center">

                        <div class="relative group">
                            <div class="flex flex-col items-center">
                                <span
                                    class="text-3xl md:text-4xl font-black text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">{{ number_format($totalUsers ?? 0) }}+</span>
                                <div
                                    class="w-12 h-1 bg-blue-500 rounded-full mb-4 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                                </div>
                                <p class="text-slate-500 font-semibold uppercase tracking-widest text-xs">Community
                                    Served</p>
                            </div>
                        </div>

                        <div class="relative group border-y md:border-y-0 md:border-x border-slate-200 py-6 md:py-0">
                            <div class="flex flex-col items-center">
                                <span
                                    class="text-3xl md:text-4xl font-black text-slate-900 mb-2 group-hover:text-purple-600 transition-colors">{{ number_format($totalServices ?? 0) }}</span>
                                <div
                                    class="w-12 h-1 bg-purple-500 rounded-full mb-4 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                                </div>
                                <p class="text-slate-500 font-semibold uppercase tracking-widest text-xs">Verified
                                    Services</p>
                            </div>
                        </div>

                        <div class="relative group">
                            <div class="flex flex-col items-center">
                                <span
                                    class="text-3xl md:text-4xl font-black text-slate-900 mb-2 group-hover:text-emerald-500 transition-colors">{{ number_format($totalSellers ?? 0) }}</span>
                                <div
                                    class="w-12 h-1 bg-emerald-500 rounded-full mb-4 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                                </div>
                                <p class="text-slate-500 font-semibold uppercase tracking-widest text-xs">Student
                                    Sellers</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>



        <!-- Story Section with Image -->
        <section class="upsi-section bg-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14 items-start">

                    <div class="relative">
                        <div
                            class="inline-block px-4 py-1.5 mb-6 text-sm font-bold tracking-widest text-blue-600 uppercase bg-blue-50 rounded-full">
                            Our Origin
                        </div>

                        <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-6 leading-tight">
                            {{ $aboutStoryTitle }}
                        </h2>

                        <div class="space-y-6 relative">
                            <div
                                class="absolute left-0 top-2 w-px h-[90%] bg-gradient-to-b from-blue-200 via-purple-200 to-transparent ml-[-20px] hidden md:block">
                            </div>

                            <div class="relative group">
                                <p
                                    class="text-base sm:text-lg text-slate-600 leading-relaxed italic border-l-4 border-blue-500 pl-5 md:border-none md:pl-0">
                                    {{ $aboutStoryQuote }}
                                </p>
                            </div>

                            <div class="text-slate-600 leading-relaxed space-y-5">
                                <p>{{ $aboutStoryBody1 }}</p>

                                <div class="bg-slate-50 p-5 rounded-2xl border-l-4 border-purple-500">
                                    <p class="text-slate-700 font-medium">
                                        {{ $aboutStoryHighlight }}
                                    </p>
                                </div>

                                <p>{{ $aboutStoryBody2 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative mt-12 lg:mt-0">
                        <div class="relative z-20">
                            <img src="{{ asset($aboutStoryImage) }}"
                                alt="Students Collaborating"
                                class="w-full h-72 sm:h-96 lg:h-[460px] object-cover rounded-2xl shadow-lg border border-slate-200">

                            <div
                                class="absolute bottom-4 left-4 bg-white/95 p-4 rounded-2xl shadow-lg z-30 flex items-center gap-3 border border-slate-100">
                                <div
                                    class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-bold text-xl">
                                    25
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Founded In</p>
                                    <p
                                        class="text-lg font-black text-slate-900 underline decoration-blue-500 decoration-4">
                                        Year 2025</p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>

        <!-- Step-by-Step Guide -->
    <section class="upsi-section bg-slate-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <h2 class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3">Process</h2>
            <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-5">Simple Steps to Get Started</h3>
            
            <div class="inline-flex w-full sm:w-auto p-1.5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <button @click="activeTab = 'seekers'" 
                    :class="activeTab === 'seekers' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-600 hover:bg-slate-50'"
                    class="flex-1 sm:flex-none px-4 sm:px-6 py-2.5 rounded-xl font-bold transition-all duration-300 text-sm">
                    For Buyers
                </button>
                <button @click="activeTab = 'providers'" 
                    :class="activeTab === 'providers' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-600 hover:bg-slate-50'"
                    class="flex-1 sm:flex-none px-4 sm:px-6 py-2.5 rounded-xl font-bold transition-all duration-300 text-sm">
                    For Sellers
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'seekers'" x-transition:enter="transition ease-out duration-500 transform" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="relative">
                <div class="hidden md:block absolute top-10 left-0 w-full h-0.5 border-t-2 border-dashed border-slate-300 z-0"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 relative z-10">
                    <div class="upsi-card p-5 sm:p-6 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-blue-50">1</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Search & Browse</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Use smart filters to find tutoring, design, or coding help from your peers.</p>
                    </div>
                    <div class="upsi-card p-5 sm:p-6 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white text-purple-600 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-purple-50">2</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Book & Chat</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Book your requested date, Wait for approval, discuss needs, pricing or other details directly with student sellers through Whatsapp.</p>
                    </div>
                    <div class="upsi-card p-5 sm:p-6 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white text-emerald-500 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-emerald-50">3</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Leave a Review</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Leave a review to help build a trustworthy community.</p>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'providers'" x-transition:enter="transition ease-out duration-500 transform" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <div class="relative">
                <div class="hidden md:block absolute top-10 left-0 w-full h-0.5 border-t-2 border-dashed border-orange-200 z-0"></div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 relative z-10">
                    <div class="upsi-card p-5 sm:p-6 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white text-orange-500 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-orange-50">1</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Create Profile</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Showcase your skills, portfolio, and set your own availability status.</p>
                    </div>
                    <div class="upsi-card p-5 sm:p-6 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white text-pink-500 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-pink-50">2</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Receive Requests</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Get instant notifications when students are interested in your services.</p>
                    </div>
                    <div class="upsi-card p-5 sm:p-6 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white text-teal-500 rounded-2xl flex items-center justify-center text-2xl font-black mb-5 shadow-sm border-2 border-teal-50">3</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Earn & Grow</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Build your reputation, earn money, and help your fellow students and community succeed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</section>

<section class="py-12 sm:py-16 bg-slate-950 relative overflow-hidden text-center">
    <div class="max-w-4xl mx-auto px-6 relative z-10">
        <h2 class="text-3xl md:text-5xl font-black text-white mb-5 tracking-tight leading-tight">
            {{ $aboutCtaTitle }}
        </h2>
        <p class="text-slate-400 text-base md:text-lg mb-8 max-w-2xl mx-auto">
            {{ $aboutCtaSubtitle }}
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <a href="{{ route('services.index') }}" 
               class="group w-full sm:w-auto px-6 py-3 bg-white text-slate-950 rounded-xl font-bold hover:bg-indigo-50 transition-all duration-300 flex items-center justify-center gap-2">
                <span>Find Your Service!</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
            
            @guest
            <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 text-white border border-slate-700 rounded-xl font-bold hover:bg-slate-900 transition-all">
                Log In
            </a>
            @endguest
        </div>
    </div>
</section>

        <!-- Footer -->
        @include('layouts.footer')

    </div>
</body>

</html>
