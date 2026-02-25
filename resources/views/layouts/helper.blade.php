<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="upsi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>U-Serve | Upsi Service Circle</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        nav {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f3f4f6;
        }

        /* Navigation Link Styles */
        .nav-link {
            @apply px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 cursor-pointer;
            color: #4b5563;
        }

        .nav-link:hover {
            color: #0d9488 !important;
            background-color: #f0fdfa;
        }

        .nav-link.active {
            color: #0f766e !important;
            background-color: #f0fdfa;
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

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">

        @include('layouts.navbar')

        <main class="pt-0">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
