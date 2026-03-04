<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UServe Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #0ea5e9 100%);
        }
        .password-toggle {
            cursor: pointer;
            user-select: none;
        }
        body {
            background: #0f172a;
        }
    </style>
</head>

<body class="min-h-screen">
    <div class="w-full min-h-screen lg:h-screen flex flex-col lg:flex-row">
        <!-- LEFT SIDE - GRADIENT -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg flex items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-10 left-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-80 h-80 bg-white rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10 text-white text-center">
                <h1 class="text-5xl font-bold mb-4">UServe</h1>
                <p class="text-xl opacity-90">Admin Control Panel</p>
            </div>
        </div>

        <!-- RIGHT SIDE - LOGIN FORM -->
        <div class="w-full lg:w-1/2 bg-slate-900 flex items-center justify-center p-4 sm:p-6 lg:p-12 min-h-screen lg:min-h-0">
            <div class="w-full max-w-md bg-slate-900 rounded-2xl sm:rounded-none border border-slate-800 sm:border-0 p-4 sm:p-0">
                
                <!-- HEADER -->
                <div class="mb-8 sm:mb-12">
                    <div class="flex items-center gap-3 mb-6 sm:mb-8">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-lg"></div>
                        <span class="text-white text-xl sm:text-2xl font-bold">UServe</span>
                    </div>
                    <h2 class="text-white text-2xl sm:text-3xl font-bold mb-2">Welcome Back!</h2>
                    <p class="text-slate-400 text-sm">Sign in to your admin account</p>
                </div>

                <!-- ERROR MESSAGE -->
                @if ($errors->any())
                    <div class="bg-red-500 bg-opacity-20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-500 bg-opacity-20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- LOGIN FORM -->
                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <!-- EMAIL INPUT -->
                    <div class="mb-6">
                        <label class="block text-slate-300 text-sm font-medium mb-3">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 sm:py-3 bg-slate-800 border border-slate-700 text-white rounded-lg placeholder-slate-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition @error('email') border-red-500 @enderror"
                            placeholder="admin@example.com" required autofocus>
                        @error('email')
                            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PASSWORD INPUT -->
                    <div class="mb-8">
                        <label class="block text-slate-300 text-sm font-medium mb-3">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-2.5 sm:py-3 bg-slate-800 border border-slate-700 text-white rounded-lg placeholder-slate-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none transition pr-12 @error('password') border-red-500 @enderror"
                                placeholder="••••••••" required>
                            <button type="button" class="password-toggle absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-300 transition"
                                onclick="togglePasswordVisibility()">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- FORGOT PASSWORD LINK -->
                    <div class="mb-6 sm:mb-8 text-right">
                        <a href="{{ route('password.request') }}" class="text-cyan-400 hover:text-cyan-300 text-sm font-medium transition">
                            Forgot Password?
                        </a>
                    </div>

                    <!-- SIGN IN BUTTON -->
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-semibold py-2.5 sm:py-3 rounded-lg shadow-lg hover:shadow-cyan-500/50 transition duration-300 mb-6">
                        Sign In
                    </button>
                </form>

                <!-- FOOTER LINKS -->
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 text-xs text-slate-400 pt-5 sm:pt-6 border-t border-slate-800">
                    <a href="#" class="hover:text-slate-300 transition">Terms of Use</a>
                    <span>•</span>
                    <a href="#" class="hover:text-slate-300 transition">Privacy Policy</a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                password.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>

</body>
</html>
