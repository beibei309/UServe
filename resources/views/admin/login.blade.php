<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S2U Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">

    <div class="bg-white p-10 rounded-2xl shadow-2xl w-full max-w-md">

        <!-- LOGO / TITLE -->
        <div class="text-center mb-6">
            <h1 class="text-4xl font-extrabold text-blue-700 tracking-wide">S2U</h1>
            <h2 class="text-xl font-semibold text-blue-600 mt-1">Admin Portal</h2>
            <p class="text-gray-500 mt-2">Sign in to continue</p>
        </div>



        <!-- ERROR MESSAGE -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- LOGIN FORM -->
        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <label class="block mb-2 font-medium text-gray-700">Email</label>
            <input type="email" name="email"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 outline-none"
                placeholder="Enter your admin email" required>

            <label class="block mt-4 mb-2 font-medium text-gray-700">Password</label>
            <input type="password" name="password"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 outline-none"
                placeholder="Enter your password" required>

            <button type="submit"
                class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-lg transition">
                Sign In
            </button>
        </form>

        <!-- FOOTER -->
        <p class="text-center text-gray-500 text-sm mt-6">
            © {{ date('Y') }} S2U System. All rights reserved.
        </p>

    </div>

</body>
</html>
