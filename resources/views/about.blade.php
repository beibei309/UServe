<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>U-Serve | Upsi Service Circle</title>
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

<body class="antialiased bg-white">
    <div x-data="{
        activeTab: 'seekers'
    }">

        <!-- Navigation -->
        @include('layouts.navbar')

        <!-- Hero Section -->
        <section class="py-24 bg-white relative overflow-hidden">
            <div
                class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50">
            </div>
            <div
                class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-96 h-96 bg-purple-50 rounded-full blur-3xl opacity-50">
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-20">

                    <div class="order-2 lg:order-1">
                        <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-4 block">Our
                            Mission</span>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 leading-tight">
                            Empowering the <span
                                class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">UPSI
                                Community</span> through U-Serve
                        </h1>
                        <p class="text-lg text-slate-600 leading-relaxed mb-8">
                            U-Serve (UPSI Service Circle) is more than just a marketplace. It is a dedicated ecosystem
                            designed specifically for UPSI students to bridge the gap between talent and needs. Whether
                            you're looking for expert tutoring, creative design, or technical coding help, your peers
                            are here to deliver.
                        </p>

                        <div class="flex flex-wrap gap-4">
                            @auth
                                <a href="{{ route('services.index') }}"
                                    class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-blue-600 transition-all duration-300 shadow-xl shadow-slate-200 flex items-center gap-2">
                                    Find Your Next Service
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition-all duration-300 shadow-xl shadow-blue-200">
                                    Join U-Serve Today!
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="order-1 lg:order-2 relative">
                        <div
                            class="relative z-10 rounded-3xl overflow-hidden shadow-2xl transform lg:rotate-3 hover:rotate-0 transition-transform duration-500 border-8 border-white">
                            <div
                                class="aspect-video bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center group">
                                <img src="{{ asset('images/about.jpg') }}" alt="Students Collaborating"
                                    class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span
                                        class="bg-white/90 backdrop-blur px-4 py-2 rounded-full text-slate-900 font-bold text-sm shadow-lg">UPSI
                                        Talent in Action</span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-purple-100 rounded-2xl -z-10 rotate-12">
                        </div>
                        <div class="absolute -top-6 -right-6 w-32 h-32 bg-blue-100 rounded-2xl -z-10 -rotate-12"></div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-[2.5rem] p-8 md:p-12 border border-slate-100 shadow-inner">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">

                        <div class="relative group">
                            <div class="flex flex-col items-center">
                                <span
                                    class="text-4xl md:text-5xl font-black text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">{{ number_format($totalUsers ?? 0) }}+</span>
                                <div
                                    class="w-12 h-1 bg-blue-500 rounded-full mb-4 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                                </div>
                                <p class="text-slate-500 font-semibold uppercase tracking-widest text-xs">Community
                                    Served</p>
                            </div>
                        </div>

                        <div class="relative group border-y md:border-y-0 md:border-x border-slate-200 py-8 md:py-0">
                            <div class="flex flex-col items-center">
                                <span
                                    class="text-4xl md:text-5xl font-black text-slate-900 mb-2 group-hover:text-purple-600 transition-colors">{{ number_format($totalServices ?? 0) }}</span>
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
                                    class="text-4xl md:text-5xl font-black text-slate-900 mb-2 group-hover:text-emerald-500 transition-colors">{{ number_format($totalSellers ?? 0) }}</span>
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
        <section class="py-24 bg-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-40 pointer-events-none">
                <div class="absolute top-1/4 -left-20 w-72 h-72 bg-blue-100 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-1/4 -right-20 w-72 h-72 bg-purple-100 rounded-full blur-[100px]"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

                    <div class="relative">
                        <div
                            class="inline-block px-4 py-1.5 mb-6 text-sm font-bold tracking-widest text-blue-600 uppercase bg-blue-50 rounded-full">
                            Our Origin
                        </div>

                        <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-8 leading-tight">
                            Built by Students, <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                For the Community.
                            </span>
                        </h2>

                        <div class="space-y-8 relative">
                            <div
                                class="absolute left-0 top-2 w-px h-[90%] bg-gradient-to-b from-blue-200 via-purple-200 to-transparent ml-[-20px] hidden md:block">
                            </div>

                            <div class="relative group">
                                <p
                                    class="text-lg text-slate-600 leading-relaxed italic border-l-4 border-blue-500 pl-6 md:border-none md:pl-0">
                                    "U-Serve was developed in 2025 out of a simple need: a trusted, friendly, and more effective
                                    way for UPSI students to help one another."
                                </p>
                            </div>

                            <div class="text-slate-600 leading-relaxed space-y-6">
                                <p>
                                    Founded by a group of students who experienced the frustration of searching for
                                    reliable academic help and creative services. Tired of
                                    <span class="font-semibold text-slate-900">unreliable providers and cluttered
                                        listings</span>, they decided to build the solution the UPSI community deserved.
                                </p>

                                <div class="bg-slate-50 p-6 rounded-2xl border-l-4 border-purple-500 shadow-sm">
                                    <p class="text-slate-700 font-medium">
                                        What started as a small project has now become a movement, transforming how we
                                        connect and support each other's financial and academic growth.
                                    </p>
                                </div>

                                <p>
                                    Today, U-Serve stands as a leader in student-led services at UPSI, continuously growing
                                    as more students turn to us for verified, peer-to-peer excellence.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="relative mt-12 lg:mt-0">
                        <div class="relative z-20">
                            <img src="{{ !empty($imagePath) ? asset('images/' . $imagePath) : asset('images/about2.jpg') }}"
                                alt="Students Collaborating"
                                class="w-full h-[500px] object-cover rounded-[2rem] shadow-2xl border-8 border-white">

                            <div
                                class="absolute -bottom-6 -left-6 md:-left-12 bg-white p-6 rounded-3xl shadow-xl z-30 flex items-center gap-4 border border-slate-100">
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

                        <div class="absolute top-12 -right-6 w-full h-full bg-slate-100 rounded-[2rem] -z-10 rotate-3">
                        </div>
                        <div class="absolute -top-6 -right-6 text-slate-200">
                            <svg width="100" height="100" fill="currentColor" viewBox="0 0 100 100">
                                <circle cx="2" cy="2" r="2" />
                                <circle cx="22" cy="2" r="2" />
                                <circle cx="42" cy="2" r="2" />
                                <circle cx="62" cy="2" r="2" />
                                <circle cx="82" cy="2" r="2" />
                                <circle cx="2" cy="22" r="2" />
                                <circle cx="22" cy="22" r="2" />
                                <circle cx="42" cy="22" r="2" />
                                <circle cx="62" cy="22" r="2" />
                                <circle cx="82" cy="22" r="2" />
                                <circle cx="2" cy="42" r="2" />
                                <circle cx="22" cy="42" r="2" />
                                <circle cx="42" cy="42" r="2" />
                                <circle cx="62" cy="42" r="2" />
                                <circle cx="82" cy="42" r="2" />
                                <circle cx="2" cy="62" r="2" />
                                <circle cx="22" cy="62" r="2" />
                                <circle cx="42" cy="62" r="2" />
                                <circle cx="62" cy="62" r="2" />
                                <circle cx="82" cy="62" r="2" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Step-by-Step Guide -->
       <section class="py-24 bg-slate-50 relative overflow-hidden" x-data="{ activeTab: 'seekers' }">
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100/50 rounded-full blur-[100px] -mr-48 -mt-48"></div>
    
    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3">Process</h2>
            <h3 class="text-4xl font-extrabold text-slate-900 mb-6">Simple Steps to Get Started</h3>
            
            <div class="inline-flex p-1.5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <button @click="activeTab = 'seekers'" 
                    :class="activeTab === 'seekers' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-600 hover:bg-slate-50'"
                    class="px-6 py-2.5 rounded-xl font-bold transition-all duration-300 text-sm">
                    For Buyers
                </button>
                <button @click="activeTab = 'providers'" 
                    :class="activeTab === 'providers' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-600 hover:bg-slate-50'"
                    class="px-6 py-2.5 rounded-xl font-bold transition-all duration-300 text-sm">
                    For Sellers
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'seekers'" x-transition:enter="transition ease-out duration-500 transform" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="relative">
                <div class="hidden md:block absolute top-10 left-0 w-full h-0.5 border-t-2 border-dashed border-slate-300 z-0"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative z-10">
                    <div class="group flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-white text-blue-600 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-blue-100 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300 border-2 border-blue-50">1</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Search & Browse</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Use smart filters to find tutoring, design, or coding help from your peers.</p>
                    </div>
                    <div class="group flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-white text-purple-600 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-purple-100 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 border-2 border-purple-50">2</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Book & Chat</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Book your requested date, Wait for approval, discuss needs, pricing or other details directly with student sellers through Whatsapp.</p>
                    </div>
                    <div class="group flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-white text-emerald-500 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-emerald-100 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300 border-2 border-emerald-50">3</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Leave a Review</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Leave a review to help build a trustworthy community.</p>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'providers'" x-transition:enter="transition ease-out duration-500 transform" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <div class="relative">
                <div class="hidden md:block absolute top-10 left-0 w-full h-0.5 border-t-2 border-dashed border-orange-200 z-0"></div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative z-10">
                    <div class="group flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-white text-orange-500 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-orange-100 group-hover:scale-110 transition-all border-2 border-orange-50">1</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Create Profile</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Showcase your skills, portfolio, and set your own availability status.</p>
                    </div>
                    <div class="group flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-white text-pink-500 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-pink-100 group-hover:scale-110 transition-all border-2 border-pink-50">2</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Receive Requests</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Get instant notifications when students are interested in your services.</p>
                    </div>
                    <div class="group flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-white text-teal-500 rounded-3xl flex items-center justify-center text-3xl font-black mb-8 shadow-xl shadow-teal-100 group-hover:scale-110 transition-all border-2 border-teal-50">3</div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Earn & Grow</h4>
                        <p class="text-slate-600 leading-relaxed max-w-xs">Build your reputation, earn money, and help your fellow students and community succeed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</section>

<section class="py-24 bg-slate-950 relative overflow-hidden text-center">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full opacity-30 pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-4xl mx-auto px-6 relative z-10">
        <h2 class="text-4xl md:text-6xl font-black text-white mb-8 tracking-tight leading-tight">
            Over <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">{{ number_format($totalUsers ?? 1250) }}+</span> Students Have Used U-Serve
        </h2>
        <p class="text-slate-400 text-lg md:text-xl mb-12 max-w-2xl mx-auto">
            Find a better way to learn and earn. Join the UPSI student movement transforming the campus economy.
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <a href="{{ route('services.index') }}" 
               class="group px-10 py-4 bg-white text-slate-950 rounded-2xl font-bold hover:scale-105 transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.2)] flex items-center gap-2">
                <span>Find Your Service!</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
            
            @guest
            <a href="{{ route('login') }}" class="px-10 py-4 text-white border border-slate-700 rounded-2xl font-bold hover:bg-slate-900 transition-all">
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
