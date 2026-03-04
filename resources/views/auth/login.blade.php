<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>U-Serve | Upsi Service Circle</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    {{-- SCRIPTS & STYLES --}}
    {{-- Ensure you have Tailwind running. If using Vite: --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- OR if you are not using Vite yet, uncomment this CDN for testing: --}}
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    {{-- SWEETALERT CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased text-gray-900">

    {{-- ======================== --}}
    {{-- 1. VIDEO BACKGROUND AREA --}}
    {{-- ======================== --}}
    <div class="fixed inset-0 w-full h-full overflow-hidden z-0">
        {{-- 
             Replace the 'src' below with your local video path, 
             e.g., src="{{ asset('videos/campus-bg.mp4') }}" 
        --}}
        <video autoplay muted loop playsinline class="absolute min-w-full min-h-full object-cover">
            <source src="{{ asset('videos/background-myupsi-small.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        {{-- Dark Overlay: Makes text readable on top of video --}}
        <div class="absolute inset-0 bg-black/60"></div>
    </div>

    {{-- ======================== --}}
    {{-- 2. MAIN CONTENT AREA     --}}
    {{-- ======================== --}}
    <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 p-4">

        {{-- LOGO SECTION --}}
        <div class="mb-6 mt-16">
            <a href="/" class="flex items-center gap-2 group">
                <img src="{{ asset('images/logo.png') }}" alt=" Logo"
                    class="w-25 h-20 rounded-xl shadow-lg group-hover:opacity-90 transition">
            </a>
        </div>


        {{-- LOGIN CARD --}}
        <div
            class="w-full sm:max-w-md px-8 py-10 bg-white/95 backdrop-blur-sm shadow-2xl rounded-2xl border border-gray-200/50">

            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Welcome back</h2>
                <p class="mt-2 text-sm text-gray-500">Sign in to your U-Serve account.</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <div class="relative">
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            autocomplete="username"
                            class="w-full pl-4 pr-10 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-gray-400 text-gray-900 text-sm"
                            placeholder="you@student.upsi.edu.my">
                        <div
                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-gray-400 text-gray-900 text-sm"
                            placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer transition">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500 hover:underline transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                    Sign in
                </button>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-gray-500 font-medium">New to U-Serve?</span>
                    </div>
                </div>

                <a href="{{ route('register') }}"
                    class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    Create an account
                </a>
            </form>
        </div>

        {{-- Footer Link --}}
        <div class="mt-8 text-center">
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-200 hover:text-white transition-colors group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>
        </div>
    </div>

    {{-- SWEETALERT LOGIC --}}
    <script>
        @if (session('error'))
            const sessionErrorMessage = "{!! addslashes(session('error')) !!}";
            if (
                sessionErrorMessage.toLowerCase().includes('suspended') ||
                sessionErrorMessage.toLowerCase().includes('banned') ||
                sessionErrorMessage.toLowerCase().includes('blocked') ||
                sessionErrorMessage.toLowerCase().includes('blacklisted')
            ) {
                const sessionTitle = sessionErrorMessage.toLowerCase().includes('blacklisted')
                    ? 'Account Blacklisted'
                    : (sessionErrorMessage.toLowerCase().includes('blocked') ? 'Account Blocked' : 'Account Suspended');
                Swal.fire({
                    icon: 'error',
                    title: sessionTitle,
                    html: sessionErrorMessage,
                    confirmButtonColor: '#4F46E5',
                    confirmButtonText: 'Back to Login',
                });
            }
        @endif

        @if ($errors->has('email'))
            const errorMessage = "{!! addslashes($errors->first('email')) !!}";

            if (
                errorMessage.toLowerCase().includes('suspended') ||
                errorMessage.toLowerCase().includes('banned') ||
                errorMessage.toLowerCase().includes('blocked') ||
                errorMessage.toLowerCase().includes('blacklisted')
            ) {
                const title = errorMessage.toLowerCase().includes('blacklisted')
                    ? 'Account Blacklisted'
                    : (errorMessage.toLowerCase().includes('blocked') ? 'Account Blocked' : 'Account Suspended');
                Swal.fire({
                    icon: 'error',
                    title: title,
                    html: errorMessage,
                    confirmButtonColor: '#4F46E5',
                    confirmButtonText: 'Back to Login',

                });
            }
        @endif
    </script>
</body>

</html>
