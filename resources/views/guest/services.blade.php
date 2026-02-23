<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>S2U - Student to Community</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FBFBF7;

        }
    </style>
</head>


<body class="antialiased">
    <div x-data="{
        mobileMenuOpen: false,
        activeTab: 'students',
        stats: { students: 1250, services: 340, reviews: 890 },
        animateStats: false
    }" x-init="setTimeout(() => animateStats = true, 1000)">

        {{-- Navigation bar --}}
        @include('layouts.navbar')
        <br><br><br>

        <div class="max-w-7xl mx-auto px-6 py-10">
            {{-- Page Title --}}
            <h1 class="text-2xl font-bold text-gray-800">Find Student Services</h1>
            <p class="text-gray-600 mb-6">Browse services offered by UPSI students. Request and get things done.</p>
            <br>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- ======================= --}}
                {{--       SIDEBAR           --}}
                {{-- ======================= --}}
                <aside class="lg:col-span-1 bg-white border rounded-xl p-5 shadow-sm h-fit sticky top-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Categories</h2>

                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('guest.services') }}"
                                class="block px-3 py-2 rounded-lg text-sm {{ !$category_id ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                All Categories
                            </a>
                        </li>

                        @foreach ($categories as $cat)
                            <li>
                                <a href="?category_id={{ $cat->id }}"
                                    class="block px-3 py-2 rounded-lg text-sm 
                       {{ $category_id == $cat->id ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                </aside>

                {{-- ======================= --}}
                {{--      MAIN CONTENT       --}}
                {{-- ======================= --}}
                <main class="lg:col-span-3">

                    {{-- Top Filters --}}
                    <div class="flex flex-wrap items-center gap-3 mb-5">

                        {{-- Search --}}
                        <form method="GET" class="flex-1">
                            <input type="hidden" name="category_id" value="{{ $category_id }}">

                            <div class="relative">
                                <input type="text" name="q" placeholder="Search services..."
                                    value="{{ request('q') }}"
                                    class="w-full border rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    style="color: rgb(48, 48, 48);">
                                <button class="absolute right-3 top-2.5 text-gray-400">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        {{-- Sort --}}
                        <form method="GET">
                            <input type="hidden" name="category_id" value="{{ $category_id }}">
                            <select name="sort" class="border rounded-lg text-sm px-2 py-2"
                                onchange="this.form.submit()">
                                <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>Price: Low →
                                    High</option>
                                <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>Price: High →
                                    Low</option>
                            </select>
                        </form>

                    </div>

                    @foreach ($services as $service)
                        <div class="bg-white border rounded-xl p-6 shadow-sm hover:shadow-md transition mb-6">

                            <div class="flex gap-5 h-full">

                                {{-- LEFT SIDE IMAGE --}}
                                <div class="w-45 h-40 flex-shrink-0">
                                    <img src="{{ $service->image_path ? asset('images/' . $service->image_path) : asset('default.jpg') }}"
                                        class="w-full h-full object-cover rounded-lg border">
                                </div>



                                {{-- RIGHT SECTION --}}
                                <div class="flex-1 relative flex flex-col justify-between">

                                    {{-- TOP ROW: Left + Right --}}
                                    <div class="flex justify-between">

                                        {{-- LEFT SIDE: Post time --}}
                                        <p class="text-xs text-gray-500">
                                            Posted {{ $service->created_at->diffForHumans() }}
                                        </p>

                                        {{-- RIGHT SIDE: PRICE + ACTION ICONS --}}
                                        <div class="flex items-center gap-3">

                                            {{-- PRICE --}}
                                            @if ($service->suggested_price)
                                                <span class="text-gray-900 mb-2" style="font-size: 17px;">
                                                    From
                                                    <strong>RM{{ number_format($service->suggested_price, 2) }}</strong>
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                    <h2 class="font-bold hover:underline cursor-pointer"
                                        style="font-size: 18px; color:rgb(48, 48, 48);">
                                        <a href="{{ route('guest.detail-service', $service->id) }}">
                                            {{ $service->title }}
                                        </a>
                                    </h2>

                                    {{-- CATEGORY --}}
                                    <div class="flex flex-wrap mt-3 gap-2">
                                        @if ($service->category)
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-medium"
                                                style="background-color:white; border:2px solid {{ $service->category->color }}; color:{{ $service->category->color }}">
                                                {{ $service->category->name }}
                                            </span>
                                        @endif

                                        <!-- Availability Status -->
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium {{ $service->user->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <div
                                                class="w-1 h-1 rounded-full {{ $service->user->is_available ? 'bg-green-400' : 'bg-red-400' }} mr-1">
                                            </div>
                                            {{ $service->user->is_available ? 'Available' : 'Busy' }}
                                        </span>
                                    </div>

                                    <!-- Rating -->
                                    <div class="flex items-center mt-2 gap-2">

                                        <!-- Stars -->
                                        <div class="flex items-center">
                                            @php
                                                $avg = round($service->user->average_rating ?? 0, 1); // Round to 1 decimal
                                            @endphp
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= ceil($avg) ? 'text-yellow-400' : 'text-gray-300' }} transition-colors duration-200"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            @endfor
                                        </div>

                                        <!-- Average Rating -->
                                        <span
                                            class="text-xs md:text-sm text-gray-700 bg-gray-100 px-2 py-0.5 rounded-full font-medium">
                                            {{ number_format($avg, 1) }}
                                        </span>

                                    </div>



                                    {{-- DESCRIPTION --}}
                                    <p class="text-gray-600 text-sm mt-2 leading-relaxed">
                                        {{ Str::limit($service->description, 150) }}
                                    </p>

                                    {{-- BOTTOM ROW: Provider + Request Btn --}}
                                    <div class="flex justify-between items-center mt-5">

                                        {{-- Provider --}}
                                        <div class="flex flex-col gap-1">

                                            <div class="flex items-center gap-3">

                                                {{-- Avatar --}}
                                                <a href="{{ route('students.profile', $service->user) }}"
                                                    class="group/avatar block">
                                                    @if ($service->user->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $service->user->profile_photo_path) }}"
                                                            alt="{{ $service->user->name }}"
                                                            class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-200 group-hover/avatar:ring-indigo-300">
                                                    @else
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                            {{ strtoupper(substr($service->user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </a>

                                                {{-- Name + Badge --}}
                                                <div class="flex items-center space-x-1">
                                                    {{-- Name --}}
                                                    <a href="{{ route('students.profile', $service->user) }}"
                                                        class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors text-sm hover:underline">
                                                        {{ Str::limit($service->user->name, 15) }}
                                                    </a>


                                                    {{-- Trust Badge --}}
                                                    @if ($service->user->trust_badge)
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            <svg class="w-3 h-3 mr-0.5" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Verified
                                                        </span>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>


                                        <!-- Action + Share -->
                                        <div class="mt-3 flex items-center justify-between space-x-2">

                                            <!-- Request Service Button -->
                                            <a href="{{ auth()->check() ? route('chat.request', ['user' => $service->user->id, 'service' => $service->title]) : route('login') }}"
                                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-base font-medium rounded-md shadow transition duration-200">
                                                Request Service
                                            </a>

                                            <!-- Favourite / Heart Button -->
                                            <button type="button"
                                                onclick="handleFavourite({{ $service->id }}, {{ auth()->check() ? 'true' : 'false' }})"
                                                class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full transition"
                                                title="Add to favourites">

                                                <svg id="heart-{{ $service->id }}"
                                                    class="w-6 h-6 transition {{ auth()->check() && $service->is_favourited ? 'text-red-500' : 'text-gray-400' }}"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                                                </svg>

                                            </button>

                                            <!-- Share Button -->
                                            <button type="button"
                                                class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full transition"
                                                title="Share Service" onclick="handleShare(this)"
                                                data-url="{{ route('student-services.show', $service) }}">
                                                <img src="{{ asset('images/share.png') }}" alt="Share"
                                                    class="w-5 h-5">
                                            </button>
                                        </div>

                                        <script>
                                            function handleFavourite(serviceId, isLoggedIn) {
                                                const heart = document.getElementById('heart-' + serviceId);

                                                if (!isLoggedIn) {
                                                    // Redirect guest to login
                                                    window.location.href = "{{ route('login') }}";
                                                    return;
                                                }

                                                // Toggle heart color visually
                                                heart.classList.toggle('text-red-500');
                                                heart.classList.toggle('text-gray-400');

                                                // Optional: Send AJAX request to save favourite in database
                                                fetch('/favourites/toggle/' + serviceId, {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                            'Content-Type': 'application/json'
                                                        },
                                                        body: JSON.stringify({
                                                            service_id: serviceId
                                                        })
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        console.log('Favourite updated', data);
                                                    })
                                                    .catch(err => console.error(err));
                                            }
                                        </script>

                                        <!-- SHARE MODAL -->
                                        <div id="shareModal"
                                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-50">
                                            <div
                                                class="bg-white rounded-xl shadow-xl w-80 md:w-96 p-6 transform scale-95 transition-transform duration-300">
                                                <!-- Close Button -->
                                                <button onclick="closeShareModal()"
                                                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>

                                                <!-- Header -->
                                                <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Share
                                                    This
                                                    Service</h3>

                                                <!-- Input + Copy -->
                                                <div class="flex items-center border rounded-lg overflow-hidden mb-4">
                                                    <input type="text" id="shareLinkInput"
                                                        class="flex-1 px-3 py-2 text-sm text-gray-700 focus:outline-none"
                                                        readonly>
                                                    <button onclick="copyShareLink()"
                                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-medium transition-colors duration-200">
                                                        Copy
                                                    </button>
                                                </div>

                                                <!-- Copy Feedback -->
                                                <p id="copyMessage"
                                                    class="text-sm text-green-600 hidden text-center mb-2">Link copied!
                                                </p>
                                            </div>
                                        </div>


                                        <script>
                                            function handleShare(button) {
                                                const url = button.dataset.url;

                                                const modal = document.getElementById('shareModal');
                                                document.getElementById('shareLinkInput').value = url;

                                                modal.classList.remove('opacity-0', 'pointer-events-none');
                                                modal.querySelector('div').classList.remove('scale-95');
                                                modal.querySelector('div').classList.add('scale-100');
                                            }

                                            function copyShareLink() {
                                                const input = document.getElementById('shareLinkInput');
                                                input.select();
                                                input.setSelectionRange(0, 99999);
                                                document.execCommand("copy");

                                                const msg = document.getElementById('copyMessage');
                                                msg.classList.remove('hidden');
                                                setTimeout(() => msg.classList.add('hidden'), 2000);
                                            }

                                            function closeShareModal() {
                                                const modal = document.getElementById('shareModal');
                                                modal.classList.add('opacity-0', 'pointer-events-none');
                                                modal.querySelector('div').classList.add('scale-95');
                                                modal.querySelector('div').classList.remove('scale-100');
                                            }
                                        </script>


                                    </div>

                                </div>
                            </div>

                        </div>
                    @endforeach



                </main>
            </div>
        </div>
        </section>
