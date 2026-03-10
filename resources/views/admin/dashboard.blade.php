@extends('admin.layout')

@section('content')
    <div class="px-4 md:px-0">

        <!-- Title -->
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Dashboard</h1>
        <p class="mt-1 font-medium transition-colors duration-300" style="color: var(--text-secondary);">Monitor platform activity and analytics.</p>

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-8 sm:mt-10">

            <!-- CARD: Total Students -->
            <a href="{{ route('admin.students.index') }}"
                class="group relative p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-cyan-500/20 hover:scale-[1.02] transition-all duration-300 block overflow-hidden border"
                style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%); border-color: var(--border-color);">
                <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <p class="font-medium text-sm transition-colors duration-300" style="color: var(--text-secondary);">Total Students</p>
                    <p class="text-3xl sm:text-4xl lg:text-5xl font-bold text-cyan-400 mt-4">{{ $totalStudents }}</p>
                </div>
            </a>

            <!-- CARD: Total Community Users -->
            <a href="{{ route('admin.community.index') }}"
                class="group relative p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-purple-500/20 hover:scale-[1.02] transition-all duration-300 block overflow-hidden border"
                style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%); border-color: var(--border-color);">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <p class="font-medium text-sm transition-colors duration-300" style="color: var(--text-secondary);">Total Community Users</p>
                    <p class="text-3xl sm:text-4xl lg:text-5xl font-bold text-purple-400 mt-4">{{ $totalCommunityUsers }}</p>
                </div>
            </a>

            <!-- CARD: Total Services -->
            <a href="{{ route('admin.services.index') }}"
                class="group relative p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-pink-500/20 hover:scale-[1.02] transition-all duration-300 block overflow-hidden border"
                style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%); border-color: var(--border-color);">
                <div class="absolute inset-0 bg-gradient-to-r from-pink-500 to-orange-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <p class="font-medium text-sm transition-colors duration-300" style="color: var(--text-secondary);">Total Services</p>
                    <p class="text-3xl sm:text-4xl lg:text-5xl font-bold text-pink-400 mt-4">{{ $totalServices }}</p>
                </div>
            </a>

            <!-- CARD: Pending Services -->
            <a href="{{ route('admin.services.index', ['status' => 'pending']) }}"
                class="group relative p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-yellow-500/20 hover:scale-[1.02] transition-all duration-300 block overflow-hidden border"
                style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%); border-color: var(--border-color);">
                <div class="absolute inset-0 bg-gradient-to-r from-yellow-500 to-orange-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <p class="font-medium text-sm transition-colors duration-300" style="color: var(--text-secondary);">Pending Services</p>
                    <p class="text-3xl sm:text-4xl lg:text-5xl font-bold text-yellow-400 mt-4">{{ $pendingServices }}</p>
                </div>
            </a>

        </div>

        @if ($pendingStudents > 0)
            <div
                class="mt-6 px-4 sm:px-6 py-4 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border transition-all duration-300"
                style="background-color: rgba(239, 68, 68, 0.1); border-color: #ef4444; color: #f87171;">
                <div>
                    <strong>⚠ Action Required</strong><br>
                    {{ $pendingStudents }} student(s) are waiting for approval.
                </div>

                <a href="{{ route('admin.students.index', ['verification_status' => 'pending']) }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-300">
                    Review Now
                </a>
            </div>
        @endif

        @if ($pendingHelpers > 0)
            <div
                class="mt-6 px-4 sm:px-6 py-4 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border transition-all duration-300"
                style="background-color: rgba(245, 158, 11, 0.1); border-color: #f59e0b; color: #fbbf24;">
                <div>
                    <strong>⚠ Action Required</strong><br>
                    {{ $pendingHelpers }} helper(s) are waiting for verification.
                </div>

                <a href="{{ route('admin.verifications.index') }}"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-300">
                    Review Helpers
                </a>
            </div>
        @endif

        @if ($studentsWithoutStatus > 0)
            <div
                class="mt-6 px-4 sm:px-6 py-4 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border transition-all duration-300"
                style="background-color: rgba(234, 88, 12, 0.1); border-color: #ea580c; color: #fb923c;">
                <div>
                    <strong>⚠ Action Required</strong><br>
                    {{ $studentsWithoutStatus }} student/helper account(s) do not have an academic status assigned.
                </div>

                <a href="{{ route('admin.student_status.index') }}"
                    class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors duration-300">
                    Assign Status
                </a>
            </div>
        @endif

        <!-- CHARTS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-10 sm:mt-12">

            <!-- LINE CHART -->
            <div class="p-4 sm:p-6 lg:p-8 rounded-2xl shadow-xl border transition-all duration-300"
                 style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%); border-color: var(--border-color);">
                <h2 class="text-lg font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Monthly Student Registrations</h2>
                <canvas id="studentChart" height="120"></canvas>
            </div>

            <!-- BAR CHART -->
            <div class="p-4 sm:p-6 lg:p-8 rounded-2xl shadow-xl border transition-all duration-300"
                 style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%); border-color: var(--border-color);">
                <h2 class="text-lg font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Services Created Per Month</h2>
                <canvas id="serviceChart" height="120"></canvas>
            </div>

        </div>

    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div id="adminDashboardConfig"
        data-students-per-month='@json(array_values($studentsPerMonth))'
        data-services-per-month='@json(array_values($servicesPerMonth))'></div>
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endsection
