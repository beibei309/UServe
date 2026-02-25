<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>U-Serve | Upsi Service Circle</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #695734;
        }

        #all-tab,
        #pending-tab,
        #approved-tab,
        #rejected-tab {
            position: relative;
            z-index: 10;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        input::placeholder {
            color: rgb(81, 33, 33);
            opacity: 1;
        }

        select option {
            border-radius: 10px;
            color: #695734;
            background-color: #F0F0F0;

        }

        /* --- Rich Text Formatting --- */
        .rich-text ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .rich-text ol {
            list-style-type: decimal;
            padding-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .rich-text p,
        .rich-text li {
            margin-bottom: 0.25rem;
            line-height: 1.5;
        }

        .rich-text strong {
            font-weight: 600;
            color: #111827;
        }

        /* Gray-900 */
        .rich-text em {
            font-style: italic;
        }

        .rich-text h1,
        .rich-text h2,
        .rich-text h3 {
            font-weight: 700;
            margin-top: 0.75rem;
            margin-bottom: 0.25rem;
            color: #111827;
        }

        /* --- Modern Thin Scrollbar --- */
        .modern-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .modern-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .modern-scrollbar::-webkit-scrollbar-thumb {
            background-color: #D1D5DB;
            border-radius: 20px;
        }

        /* Gray-300 */
        .rich-text:hover .modern-scrollbar::-webkit-scrollbar-thumb {
            background-color: #9CA3AF;
        }

        /* Gray-400 on hover */
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

        <!-- Page Content -->
        <main class="bg-white min-h-screen">
            {{ $slot }}
        </main>


        <!-- Footer -->
        @include('layouts.footer')

    </div>
    @stack('scripts')

</body>

</html>
