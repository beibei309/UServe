<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | UServe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            /* Light theme colors */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --sidebar-bg: #ffffff;
            --sidebar-border: #e2e8f0;
            --hover-bg: #f1f5f9;
            --active-bg: #e0f2fe;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] {
            /* Dark theme colors */
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f8fafc;
            --text-secondary: #e2e8f0;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --sidebar-bg: #1e293b;
            --sidebar-border: #334155;
            --hover-bg: #334155;
            --active-bg: #0f172a;
            --shadow: rgba(0, 0, 0, 0.3);
        }
        /* Smooth transition for sidebar width and transforms */
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        
        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Theme-aware Standardized Admin Button Styles */
        .btn-blue {
            background: #2563eb;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        [data-theme="dark"] .btn-blue {
            background: #1e3a8a;
            color: #dbeafe;
        }

        .btn-green {
            background: #16a34a;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        [data-theme="dark"] .btn-green {
            background: #14532d;
            color: #bbf7d0;
        }

        .btn-red {
            background: #dc2626;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        [data-theme="dark"] .btn-red {
            background: #7f1d1d;
            color: #fecaca;
        }

        .btn-orange {
            background: #ea580c;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        [data-theme="dark"] .btn-orange {
            background: #9a3412;
            color: #fed7aa;
        }

        .btn-gray {
            background: #6b7280;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        [data-theme="dark"] .btn-gray {
            background: #374151;
            color: #e5e7eb;
        }

        .btn-blue:hover,
        .btn-green:hover,
        .btn-red:hover,
        .btn-orange:hover,
        .btn-gray:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }

        /* Theme-aware Status Badge Styles */
        .badge-green {
            background: #10b981;
            color: white;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        [data-theme="dark"] .badge-green {
            background: #065f46;
            color: #d1fae5;
        }

        .badge-red {
            background: #ef4444;
            color: white;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        [data-theme="dark"] .badge-red {
            background: #7f1d1d;
            color: #fecaca;
        }

        .badge-yellow {
            background: #f59e0b;
            color: white;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        [data-theme="dark"] .badge-yellow {
            background: #78350f;
            color: #fde68a;
        }

        .badge-dark {
            background: #6b7280;
            color: white;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        [data-theme="dark"] .badge-dark {
            background: #374151;
            color: #e5e7eb;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: relative;
            width: 50px;
            height: 25px;
            background: var(--bg-tertiary);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            outline: none;
            padding: 0;
        }

        .theme-toggle::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 19px;
            height: 19px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        [data-theme="dark"] .theme-toggle::after {
            transform: translateX(23px);
            background: #fbbf24;
        }

        /* Smooth content transitions */
        #main-content {
            transition: opacity 0.15s ease-in-out;
        }

        /* Ensure smooth transitions don't interfere with other elements */
        #main-content.transitioning {
            pointer-events: none;
        }

        /* Loading styles */
        #loading-indicator {
            backdrop-filter: blur(2px);
        }

        nav[role="navigation"] {
            color: var(--text-secondary);
        }

        nav[role="navigation"] p.text-sm {
            color: var(--text-muted) !important;
        }

        nav[role="navigation"] .relative.inline-flex.items-center {
            background-color: var(--bg-secondary) !important;
            border-color: var(--border-color) !important;
            color: var(--text-secondary) !important;
        }

        nav[role="navigation"] a.relative.inline-flex.items-center:hover {
            background-color: var(--hover-bg) !important;
            color: var(--text-primary) !important;
        }

        nav[role="navigation"] span[aria-current="page"] .relative.inline-flex.items-center {
            background: linear-gradient(90deg, #06b6d4, #2563eb) !important;
            border-color: transparent !important;
            color: #ffffff !important;
        }

        nav[role="navigation"] svg {
            color: inherit !important;
            fill: currentColor !important;
        }

        .nav-hover:hover {
            background-color: var(--hover-bg) !important;
            color: var(--text-primary) !important;
        }

        .submenu-hover:hover {
            background-color: var(--hover-bg) !important;
            color: #06b6d4 !important;
        }

        .danger-nav-hover:hover {
            background-color: rgba(239, 68, 68, 0.2) !important;
            color: #f87171 !important;
        }

        .surface-hover:hover {
            background-color: var(--hover-bg) !important;
        }
    </style>
</head>

<body class="font-sans antialiased" data-theme="dark">

    <div class="flex min-h-screen lg:h-screen overflow-hidden">

        <aside id="sidebar"
            class="sidebar-transition fixed inset-y-0 left-0 z-30 w-64 shadow-2xl transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 flex flex-col border-r transition-colors duration-300"
            style="background-color: var(--sidebar-bg); border-color: var(--sidebar-border);">

            <div class="flex items-center justify-between p-6 h-16 border-b transition-colors duration-300"
                 style="border-color: var(--sidebar-border); background-color: var(--sidebar-bg);">
                <div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-blue-600 bg-clip-text text-transparent tracking-wider">UServe</h1>
                    <p class="text-xs uppercase tracking-widest mt-1 transition-colors duration-300" style="color: var(--text-muted);">Admin</p>
                </div>
                <button type="button" data-sidebar-toggle class="lg:hidden hover:text-red-500 focus:outline-none transition-colors duration-300" style="color: var(--text-muted);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-3">

                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
                           {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold' : '' }}"
                           @if(!request()->routeIs('admin.dashboard'))
                           style="color: var(--text-secondary);"
                           @endif>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <button type="button" data-submenu-toggle data-menu-id="pageMenu" data-arrow-id="pageArrow"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
                                {{ request()->routeIs('admin.pages.*') ? 'text-white font-semibold' : '' }}"
                            @if(request()->routeIs('admin.pages.*'))
                            style="background-color: var(--hover-bg);"
                            @else
                            style="color: var(--text-secondary);"
                            @endif>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                    </path>
                                </svg>
                                <span>Page Management</span>
                            </div>
                            <svg id="pageArrow"
                                class="w-4 h-4 transition-transform {{ request()->routeIs('admin.pages.*') ? 'rotate-90' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>

                        <ul id="pageMenu"
                            class="pl-11 mt-1 space-y-1 {{ request()->routeIs('admin.pages.*') ? '' : 'hidden' }}">
                            <li>
                                <a href="{{ route('admin.faqs.index') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors duration-300 submenu-hover"
                                    style="color: var(--text-secondary);">Help
                                    page</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <button type="button" data-submenu-toggle data-menu-id="studentSubMenu" data-arrow-id="studentMenuArrow"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
                                {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.student_status.*') ? 'text-white font-semibold' : '' }}"
                            @if(request()->routeIs('admin.students.*') || request()->routeIs('admin.student_status.*'))
                            style="background-color: var(--hover-bg);"
                            @else
                            style="color: var(--text-secondary);"
                            @endif>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                                <span>Students</span>
                            </div>
                            <svg id="studentMenuArrow"
                                class="w-4 h-4 transition-transform {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.student_status.*') ? 'rotate-90' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>

                        <ul id="studentSubMenu"
                            class="pl-11 mt-1 space-y-1 {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.student_status.*') ? '' : 'hidden' }}">
                            <li>
                                <a href="{{ route('admin.students.index') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors duration-300 submenu-hover
                                    {{ request()->routeIs('admin.students.index') ? 'text-cyan-400 font-semibold' : '' }}"
                                    @if(!request()->routeIs('admin.students.index'))
                                    style="color: var(--text-secondary);"
                                    @endif>View
                                    Students</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.student_status.index') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors duration-300 submenu-hover
                                    {{ request()->routeIs('admin.student_status.index') ? 'text-cyan-400 font-semibold' : '' }}"
                                    @if(!request()->routeIs('admin.student_status.index'))
                                    style="color: var(--text-secondary);"
                                    @endif>Student
                                    Status</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('admin.community.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
                           {{ request()->routeIs('admin.community.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold' : '' }}"
                           @if(!request()->routeIs('admin.community.*'))
                           style="color: var(--text-secondary);"
                           @endif>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Manage Community
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
                           {{ request()->routeIs('admin.categories.index') ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold' : '' }}"
                           @if(!request()->routeIs('admin.categories.index'))
                           style="color: var(--text-secondary);"
                           @endif>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            Manage Categories
                        </a>
                    </li>
                    <li>
                        <button type="button" data-submenu-toggle data-menu-id="serviceMenu" data-arrow-id="serviceArrow"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
            {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.services.*') ? 'text-white font-semibold' : '' }}"
                            @if(request()->routeIs('admin.services.*'))
                            style="background-color: var(--hover-bg);"
                            @else
                            style="color: var(--text-secondary);"
                            @endif>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>Manage Services</span>
                            </div>
                            <svg id="serviceArrow"
                                class="w-4 h-4 transition-transform {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.services.*') ? 'rotate-90' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>

                        <ul id="serviceMenu"
                            class="pl-11 mt-1 space-y-1 {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.services.*') ? '' : 'hidden' }}">                      
                            <li>
                                <a href="{{ route('admin.services.index') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors duration-300 submenu-hover
                                    {{ request()->routeIs('admin.services.*') ? 'text-cyan-400 font-semibold' : '' }}"
                                    @if(!request()->routeIs('admin.services.*'))
                                    style="color: var(--text-secondary);"
                                    @endif>View
                                    Services</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('admin.requests.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
                           {{ request()->routeIs('admin.requests.index') ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold' : '' }}"
                           @if(!request()->routeIs('admin.requests.index'))
                           style="color: var(--text-secondary);"
                           @endif>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            Manage Service Requests
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.feedback.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
                           {{ request()->routeIs('admin.feedback.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold' : '' }}"
                           @if(!request()->routeIs('admin.feedback.*'))
                           style="color: var(--text-secondary);"
                           @endif>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 4v-4z">
                                </path>
                            </svg>
                            Manage Feedback
                        </a>
                    </li>

                    <li>
                        <button type="button" data-submenu-toggle data-menu-id="rewardMenu" data-arrow-id="rewardArrow"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors duration-300 nav-hover
            {{ request()->routeIs('admin.rewards.*') ? 'text-white font-semibold' : '' }}"
                            @if(request()->routeIs('admin.rewards.*'))
                            style="background-color: var(--hover-bg);"
                            @else
                            style="color: var(--text-secondary);"
                            @endif>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <span>Rewards System</span>
                            </div>
                            <svg id="rewardArrow"
                                class="w-4 h-4 transition-transform {{ request()->routeIs('admin.rewards.*') ? 'rotate-90' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>

                        <ul id="rewardMenu"
                            class="pl-11 mt-1 space-y-1 {{ request()->routeIs('admin.rewards.*') ? '' : 'hidden' }}">                      
                            <li>
                                <a href="{{ route('admin.rewards.index') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors duration-300 submenu-hover
                                    {{ request()->routeIs('admin.rewards.index') ? 'text-cyan-400 font-semibold' : '' }}"
                                    @if(!request()->routeIs('admin.rewards.index'))
                                    style="color: var(--text-secondary);"
                                    @endif>Dashboard</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.rewards.list') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors duration-300 submenu-hover
                                    {{ request()->routeIs('admin.rewards.list', 'admin.rewards.create', 'admin.rewards.edit') ? 'text-cyan-400 font-semibold' : '' }}"
                                    @if(!request()->routeIs('admin.rewards.list', 'admin.rewards.create', 'admin.rewards.edit'))
                                    style="color: var(--text-secondary);"
                                    @endif>Manage Rewards</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.rewards.redemptions') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors duration-300 submenu-hover
                                    {{ request()->routeIs('admin.rewards.redemptions') ? 'text-cyan-400 font-semibold' : '' }}"
                                    @if(!request()->routeIs('admin.rewards.redemptions'))
                                    style="color: var(--text-secondary);"
                                    @endif>Redemptions</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.rewards.analytics') }}"
                                    class="block px-4 py-2 text-sm rounded-lg transition-colors duration-300 submenu-hover
                                    {{ request()->routeIs('admin.rewards.analytics') ? 'text-cyan-400 font-semibold' : '' }}"
                                    @if(!request()->routeIs('admin.rewards.analytics'))
                                    style="color: var(--text-secondary);"
                                    @endif>Analytics</a>
                            </li>
                        </ul>
                    </li>


                </ul>

                @if (auth('admin')->user()->ha_role === 'superadmin')
                    <div class="px-6 py-4 mt-2">
                        <p class="text-xs font-semibold uppercase tracking-wider transition-colors duration-300" style="color: var(--text-muted);">Super Admin</p>
                    </div>
                    <ul class="space-y-1 px-3">
                        <li>
                            <a href="{{ route('admin.super.admins.index') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-300 danger-nav-hover"
                                style="color: var(--text-secondary);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                Admin Accounts
                            </a>
                        </li>
                    </ul>
                @endif
            </nav>

            <div class="p-4 transition-colors duration-300" style="border-top: 1px solid var(--border-color);">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(auth('admin')->user()->ha_name[0]) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate transition-colors duration-300" style="color: var(--text-primary);">{{ auth('admin')->user()->ha_name }}</p>
                        <p class="text-xs truncate transition-colors duration-300" style="color: var(--text-muted);">{{ auth('admin')->user()->ha_role }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="p-2 rounded-lg transition-colors duration-300 hover:text-red-500 surface-hover"
                                style="color: var(--text-muted);"
                            title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <header class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 flex items-center justify-between shadow-md transition-colors duration-300"
                    style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                <div class="flex items-center gap-4">
                    <button type="button" data-sidebar-toggle
                        class="p-2 rounded-md focus:outline-none transition-colors duration-300 hover:text-cyan-400 surface-hover"
                        style="color: var(--text-muted); background-color: transparent;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-base sm:text-lg font-semibold transition-colors duration-300 truncate" style="color: var(--text-primary);">Admin Dashboard</h2>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm hidden sm:block transition-colors duration-300" style="color: var(--text-muted);">{{ now()->format('D, M d Y') }}</span>
                    
                    <!-- Theme Toggle -->
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" style="color: var(--text-muted);" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                        </svg>
                        <button type="button" class="theme-toggle" data-theme-toggle title="Toggle light/dark mode"></button>
                        <svg class="w-4 h-4" style="color: var(--text-muted);" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                        </svg>
                    </div>
                </div>
            </header>

            <main id="main-content" class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-colors duration-300" style="background-color: var(--bg-primary);">
                @yield('content')
            </main>
        </div>

        <div id="mobileOverlay" data-sidebar-toggle
            class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden transition-opacity"></div>

    </div>

    <script src="{{ asset('js/admin-module.js') }}"></script>
    <div id="page-script-content">
        @yield('scripts')
    </div>
    <script src="{{ asset('js/admin-layout.js') }}"></script>
    <script src="{{ asset('js/admin-confirm.js') }}"></script>
</body>

</html>
