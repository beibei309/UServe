<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="upsi">

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
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
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

<body class="bg-gray-50 text-gray-900 antialiased" <!-- Navigation (same as welcome page) -->
    @include('layouts.navbar')

    <main class="min-h-screen pt-24 pb-16">
        <div class="max-w-5xl mx-auto px-6">
            {{ $slot }}
        </div>
    </main>


    <!-- Footer -->
    @include('layouts.footer')

    @stack('scripts')
</body>

</html>
