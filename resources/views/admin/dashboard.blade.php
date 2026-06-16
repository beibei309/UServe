@extends('admin.layout')

@section('content')
    @php
        $actionQueue = [
            [
                'label' => 'Pending services',
                'description' => 'Services waiting for admin review.',
                'value' => $pendingServices,
                'href' => route('admin.services.index', ['status' => 'pending']),
                'color' => '#f59e0b',
                'icon' => 'fa-briefcase',
            ],
            [
                'label' => 'Missing student status',
                'description' => 'Student/helper accounts without eligibility status.',
                'value' => $studentsWithoutStatus,
                'href' => route('admin.student_status.index'),
                'color' => '#f97316',
                'icon' => 'fa-id-card',
            ],
            [
                'label' => 'Open reports',
                'description' => 'Complaints that still need a decision.',
                'value' => $openReports,
                'href' => route('admin.reports.page'),
                'color' => '#ef4444',
                'icon' => 'fa-flag',
            ],
            [
                'label' => 'Community verification',
                'description' => 'Community identity checks waiting for review.',
                'value' => $pendingCommunityVerifications,
                'href' => route('admin.verifications.page'),
                'color' => '#06b6d4',
                'icon' => 'fa-user-check',
            ],
            [
                'label' => 'Payment proof checks',
                'description' => 'Orders with proof waiting for payment verification.',
                'value' => $pendingPaymentProofs,
                'href' => route('admin.requests.index'),
                'color' => '#10b981',
                'icon' => 'fa-receipt',
            ],
        ];
        $totalActionItems = collect($actionQueue)->sum('value');
    @endphp

    <div class="admin-list-page">
        <div class="admin-list-header">
            <div>
                <h1 class="admin-list-title">Admin Dashboard</h1>
                <p class="admin-list-subtitle">See what needs action today and jump straight to the right admin page.</p>
            </div>
            <div class="admin-list-actions">
                <a href="{{ route('admin.students.export', ['format' => 'csv']) }}" class="admin-export-action">
                    <i class="fa-solid fa-download text-xs"></i> Students CSV
                </a>
                <a href="{{ route('admin.requests.export') }}" class="admin-secondary-action">
                    <i class="fa-solid fa-file-export text-xs"></i> Requests CSV
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <a href="{{ route('admin.students.index') }}" class="rounded-xl border p-5 transition-all duration-300 hover:-translate-y-0.5"
                style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <p class="text-sm font-semibold" style="color: var(--text-secondary);">Student Accounts</p>
                <p class="mt-3 text-3xl font-bold text-cyan-500">{{ $totalStudentAccounts }}</p>
                <p class="mt-2 text-xs" style="color: var(--text-muted);">{{ $totalStudents }} students, {{ $totalHelpers }} helpers</p>
            </a>

            <a href="{{ route('admin.community.index') }}" class="rounded-xl border p-5 transition-all duration-300 hover:-translate-y-0.5"
                style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <p class="text-sm font-semibold" style="color: var(--text-secondary);">Community Users</p>
                <p class="mt-3 text-3xl font-bold text-violet-500">{{ $totalCommunityUsers }}</p>
                <p class="mt-2 text-xs" style="color: var(--text-muted);">{{ $pendingCommunityVerifications }} waiting checks</p>
            </a>

            <a href="{{ route('admin.services.index') }}" class="rounded-xl border p-5 transition-all duration-300 hover:-translate-y-0.5"
                style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <p class="text-sm font-semibold" style="color: var(--text-secondary);">Services</p>
                <p class="mt-3 text-3xl font-bold text-emerald-500">{{ $totalServices }}</p>
                <p class="mt-2 text-xs" style="color: var(--text-muted);">{{ $pendingServices }} awaiting review</p>
            </a>

            <a href="{{ route('admin.requests.index') }}" class="rounded-xl border p-5 transition-all duration-300 hover:-translate-y-0.5"
                style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <p class="text-sm font-semibold" style="color: var(--text-secondary);">Service Requests</p>
                <p class="mt-3 text-3xl font-bold text-sky-500">{{ $totalRequests }}</p>
                <p class="mt-2 text-xs" style="color: var(--text-muted);">{{ $disputedRequests }} dispute(s)</p>
            </a>
        </div>

        <section class="mt-6 rounded-xl border p-5"
            style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-lg font-bold" style="color: var(--text-primary);">Action Queue</h2>
                    <p class="text-sm" style="color: var(--text-secondary);">Clear these items first before checking detailed reports.</p>
                </div>
                <span class="inline-flex items-center justify-center text-sm font-bold px-3 py-1.5 rounded-full"
                    style="background-color: var(--bg-tertiary); color: var(--text-primary);">
                    {{ $totalActionItems }} item(s)
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                @foreach ($actionQueue as $item)
                    <a href="{{ $item['href'] }}" class="rounded-lg border p-4 transition-all duration-200 hover:-translate-y-0.5"
                        style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center justify-center h-9 w-9 rounded-lg"
                                style="background-color: {{ $item['color'] }}22; color: {{ $item['color'] }};">
                                <i class="fa-solid {{ $item['icon'] }}"></i>
                            </span>
                            <span class="text-2xl font-bold" style="color: {{ $item['color'] }};">{{ $item['value'] }}</span>
                        </div>
                        <h3 class="mt-3 text-sm font-bold" style="color: var(--text-primary);">{{ $item['label'] }}</h3>
                        <p class="mt-1 text-xs leading-5" style="color: var(--text-secondary);">{{ $item['description'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="mt-6 rounded-xl border p-5"
            style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold" style="color: var(--text-primary);">Reporting Shortcuts</h2>
                    <p class="text-sm" style="color: var(--text-secondary);">Detailed reports stay in their list pages, where admin can filter and export.</p>
                </div>
                <div class="admin-list-actions">
                    <a href="{{ route('admin.services.index') }}" class="admin-secondary-action">Services</a>
                    <a href="{{ route('admin.requests.index') }}" class="admin-secondary-action">Requests</a>
                    <a href="{{ route('admin.feedback.index') }}" class="admin-secondary-action">Moderation</a>
                    <a href="{{ route('admin.rewards.redemptions') }}" class="admin-secondary-action">Rewards</a>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <section class="rounded-xl border p-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <h2 class="text-lg font-bold mb-4" style="color: var(--text-primary);">Monthly Student Registrations</h2>
                <canvas id="studentChart" height="120"></canvas>
            </section>

            <section class="rounded-xl border p-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <h2 class="text-lg font-bold mb-4" style="color: var(--text-primary);">Services Created Per Month</h2>
                <canvas id="serviceChart" height="120"></canvas>
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/chart-tools.js'])
    <div id="adminDashboardConfig"
        data-students-per-month='@json(array_values($studentsPerMonth))'
        data-services-per-month='@json(array_values($servicesPerMonth))'></div>
    <script defer src="{{ asset('js/admin-dashboard.js') }}?v={{ filemtime(public_path('js/admin-dashboard.js')) }}"></script>
@endsection
