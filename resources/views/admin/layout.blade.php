<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | S2U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Smooth transition for sidebar width and transforms */
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased text-gray-900">

    <div class="flex h-screen overflow-hidden">

        <aside id="sidebar"
            class="sidebar-transition fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 flex flex-col border-r border-gray-200">

            <div class="flex items-center justify-between p-6 h-16 border-b border-gray-200 bg-white">
                <div>
                    <h1 class="text-2xl font-bold text-blue-600 tracking-wider">S2U</h1>
                    <p class="text-xs text-gray-500 uppercase tracking-widest">Admin</p>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-red-500 focus:outline-none">
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
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                           {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <button onclick="toggleSubMenu('pageMenu', 'pageArrow')"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors
                                {{ request()->routeIs('admin.pages.*') ? 'text-blue-700 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' }}">
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
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-600">Help
                                    page</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <button onclick="toggleSubMenu('studentSubMenu', 'studentMenuArrow')"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors
                                {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.student_status.*') ? 'text-blue-700 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' }}">
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
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('admin.students.index') ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">View
                                    Students</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.student_status.index') }}"
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('admin.student_status.index') ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">Student
                                    Status</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('admin.community.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                           {{ request()->routeIs('admin.community.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' }}">
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
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                           {{ request()->routeIs('admin.categories.index') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            Manage Categories
                        </a>
                    </li>
                    <li>
                        <button onclick="toggleSubMenu('serviceMenu', 'serviceArrow')"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors
            {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.services.*') ? 'text-blue-700 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' }}">
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
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('admin.services.*') ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">View
                                    Services</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('admin.requests.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors
                           {{ request()->routeIs('admin.requests.index') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            Manage Service Requests
                        </a>
                    </li>


                </ul>

                @if (auth('admin')->user()->role === 'superadmin')
                    <div class="px-6 py-4 mt-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Super Admin</p>
                    </div>
                    <ul class="space-y-1 px-3">
                        <li>
                            <a href="{{ route('admin.super.admins.index') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors">
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

            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                        {{ strtoupper(auth('admin')->user()->name[0]) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth('admin')->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth('admin')->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100"
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

            <header
                class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()"
                        class="text-gray-500 hover:text-blue-600 focus:outline-none focus:bg-gray-100 p-2 rounded-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800">Admin Dashboard</h2>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 hidden sm:block">{{ now()->format('D, M d Y') }}</span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-8 bg-gray-50">
                @yield('content')
            </main>
        </div>

        <div id="mobileOverlay" onclick="toggleSidebar()"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 hidden lg:hidden transition-opacity"></div>

    </div>

    @yield('scripts')

    <script>
        // Submenu Toggle (for Student/Page menus)
        function toggleSubMenu(menuId, arrowId) {
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                arrow.classList.add('rotate-90');
            } else {
                menu.classList.add('hidden');
                arrow.classList.remove('rotate-90');
            }
        }

        // Main Sidebar Toggle Logic
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            const isDesktop = window.innerWidth >= 1024; // Tailwind 'lg' breakpoint is 1024px

            if (isDesktop) {
                // DESKTOP LOGIC: Toggle the 'hidden' class to collapse the layout
                // We use 'hidden' to make the sidebar disappear and let main content expand
                sidebar.classList.toggle('hidden');
            } else {
                // MOBILE LOGIC: Toggle the translate transform to slide in/out
                if (sidebar.classList.contains('-translate-x-full')) {
                    // Open Sidebar
                    sidebar.classList.remove('-translate-x-full');
                    if (overlay) overlay.classList.remove('hidden');
                } else {
                    // Close Sidebar
                    sidebar.classList.add('-translate-x-full');
                    if (overlay) overlay.classList.add('hidden');
                }
            }
        }
    </script>
</body>

</html>
