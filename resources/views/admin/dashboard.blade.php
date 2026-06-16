@extends('admin.layout')

@section('content')
    @php
        $statusLabel = fn ($value) => ucwords(str_replace('_', ' ', (string) ($value ?: 'not set')));
        $serviceStatusRows = [
            ['label' => 'Pending', 'value' => $serviceStatusCounts['pending'] ?? 0, 'class' => 'text-amber-500', 'href' => route('admin.services.index', ['status' => 'pending'])],
            ['label' => 'Approved', 'value' => $serviceStatusCounts['approved'] ?? 0, 'class' => 'text-emerald-500', 'href' => route('admin.services.index', ['status' => 'approved'])],
            ['label' => 'Rejected', 'value' => $serviceStatusCounts['rejected'] ?? 0, 'class' => 'text-red-500', 'href' => route('admin.services.index', ['status' => 'rejected'])],
            ['label' => 'Suspended', 'value' => $serviceStatusCounts['suspended'] ?? 0, 'class' => 'text-slate-500', 'href' => route('admin.services.index', ['status' => 'suspended'])],
        ];
        $requestStatusRows = collect($requestStatusCounts)->sortKeys();
        $paymentStatusRows = collect($paymentStatusCounts)->sortKeys();
        $actionQueue = [
            ['label' => 'Pending services', 'value' => $pendingServices, 'href' => route('admin.services.index', ['status' => 'pending']), 'color' => '#f59e0b'],
            ['label' => 'Missing student status', 'value' => $studentsWithoutStatus, 'href' => route('admin.student_status.index'), 'color' => '#f97316'],
            ['label' => 'Open reports', 'value' => $openReports, 'href' => route('admin.reports.page'), 'color' => '#ef4444'],
            ['label' => 'Pending community verification', 'value' => $pendingCommunityVerifications, 'href' => route('admin.verifications.page'), 'color' => '#06b6d4'],
            ['label' => 'Payment proof checks', 'value' => $pendingPaymentProofs, 'href' => route('admin.requests.index'), 'color' => '#10b981'],
        ];
    @endphp

    <div class="admin-list-page">
        <div class="admin-list-header">
            <div>
                <h1 class="admin-list-title">Admin Dashboard</h1>
                <p class="admin-list-subtitle">Monitor platform health, action queues, service activity, and reporting shortcuts.</p>
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
                <p class="mt-2 text-xs" style="color: var(--text-muted);">{{ $pendingCommunityVerifications }} pending verification</p>
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
                <p class="mt-2 text-xs" style="color: var(--text-muted);">{{ $disputedRequests }} disputes, {{ $totalReviews }} reviews</p>
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 xl:grid-cols-5 gap-4">
            <section class="xl:col-span-2 rounded-xl border p-5"
                style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-lg font-bold" style="color: var(--text-primary);">Action Queue</h2>
                        <p class="text-sm" style="color: var(--text-secondary);">Items admin should clear before launch.</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background-color: var(--bg-tertiary); color: var(--text-secondary);">
                        {{ collect($actionQueue)->sum('value') }} total
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach ($actionQueue as $item)
                        <a href="{{ $item['href'] }}" class="flex items-center justify-between gap-3 rounded-lg border px-4 py-3 transition-colors duration-200 hover:opacity-90"
                            style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                            <span class="text-sm font-semibold" style="color: var(--text-primary);">{{ $item['label'] }}</span>
                            <span class="text-lg font-bold" style="color: {{ $item['color'] }};">{{ $item['value'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="xl:col-span-3 rounded-xl border p-5"
                style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-lg font-bold" style="color: var(--text-primary);">Platform Health</h2>
                        <p class="text-sm" style="color: var(--text-secondary);">Read-only moderation and reward snapshot.</p>
                    </div>
                    <a href="{{ route('admin.feedback.index') }}" class="admin-secondary-action">Moderation</a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="rounded-lg p-4" style="background-color: var(--bg-tertiary);">
                        <p class="text-xs font-bold uppercase" style="color: var(--text-muted);">Open Reports</p>
                        <p class="mt-2 text-2xl font-bold text-red-500">{{ $openReports }}</p>
                    </div>
                    <div class="rounded-lg p-4" style="background-color: var(--bg-tertiary);">
                        <p class="text-xs font-bold uppercase" style="color: var(--text-muted);">Warned Users</p>
                        <p class="mt-2 text-2xl font-bold text-amber-500">{{ $moderationCounts['warned'] }}</p>
                    </div>
                    <div class="rounded-lg p-4" style="background-color: var(--bg-tertiary);">
                        <p class="text-xs font-bold uppercase" style="color: var(--text-muted);">Restricted</p>
                        <p class="mt-2 text-2xl font-bold text-slate-500">{{ $moderationCounts['blocked'] + $moderationCounts['suspended'] + $moderationCounts['blacklisted'] }}</p>
                    </div>
                    <div class="rounded-lg p-4" style="background-color: var(--bg-tertiary);">
                        <p class="text-xs font-bold uppercase" style="color: var(--text-muted);">Reward Claims</p>
                        <p class="mt-2 text-2xl font-bold text-emerald-500">{{ $rewardSnapshot['redemptions'] }}</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
            <section class="rounded-xl border p-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="text-lg font-bold" style="color: var(--text-primary);">Service Status</h2>
                    <a href="{{ route('admin.services.export') }}" class="text-sm font-semibold text-emerald-500">Export</a>
                </div>
                <div class="space-y-3">
                    @foreach ($serviceStatusRows as $row)
                        <a href="{{ $row['href'] }}" class="flex items-center justify-between gap-3">
                            <span class="text-sm" style="color: var(--text-secondary);">{{ $row['label'] }}</span>
                            <span class="font-bold {{ $row['class'] }}">{{ $row['value'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl border p-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="text-lg font-bold" style="color: var(--text-primary);">Request Status</h2>
                    <a href="{{ route('admin.requests.export') }}" class="text-sm font-semibold text-emerald-500">Export</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse ($requestStatusRows as $status => $count)
                        <div class="flex items-center justify-between gap-3 rounded-lg px-3 py-2" style="background-color: var(--bg-tertiary);">
                            <span class="text-sm" style="color: var(--text-secondary);">{{ $statusLabel($status) }}</span>
                            <span class="font-bold" style="color: var(--text-primary);">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="text-sm" style="color: var(--text-muted);">No service requests yet.</p>
                    @endforelse
                </div>
                @if ($paymentStatusRows->isNotEmpty())
                    <div class="mt-4 pt-4 border-t" style="border-color: var(--border-color);">
                        <p class="text-xs font-bold uppercase mb-2" style="color: var(--text-muted);">Payment Status</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($paymentStatusRows as $status => $count)
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color: var(--bg-tertiary); color: var(--text-secondary);">
                                    {{ $statusLabel($status) }}: {{ $count }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mt-6">
            <section class="rounded-xl border p-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <h2 class="text-lg font-bold mb-4" style="color: var(--text-primary);">Top Categories</h2>
                <div class="space-y-3">
                    @forelse ($topCategories as $category)
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="h-3 w-3 rounded-full flex-shrink-0" style="background-color: {{ $category->hc_color ?? '#64748b' }};"></span>
                                <span class="text-sm font-semibold truncate" style="color: var(--text-primary);">{{ $category->hc_name }}</span>
                            </div>
                            <span class="text-sm font-bold" style="color: var(--text-secondary);">{{ $category->services_count }}</span>
                        </div>
                    @empty
                        <p class="text-sm" style="color: var(--text-muted);">No categories yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="xl:col-span-2 rounded-xl border p-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="text-lg font-bold" style="color: var(--text-primary);">Top Services</h2>
                    <a href="{{ route('admin.services.index') }}" class="text-sm font-semibold text-cyan-500">View all</a>
                </div>
                <div class="admin-table-scroll">
                    <table class="admin-table" style="min-width: 620px;">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Provider</th>
                                <th>Rating</th>
                                <th>Reviews</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topServices as $service)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.services.show', $service->hss_id) }}" class="font-semibold text-cyan-500">
                                            {{ Str::limit($service->hss_title, 36) }}
                                        </a>
                                        <div class="text-xs" style="color: var(--text-muted);">{{ $service->category->hc_name ?? 'No category' }}</div>
                                    </td>
                                    <td>{{ $service->user->hu_name ?? 'Unknown' }}</td>
                                    <td>{{ number_format((float) ($service->reviews_avg_rating ?? 0), 1) }}</td>
                                    <td>{{ $service->reviews_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="admin-empty-row">No reviewed services yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mt-6">
            <section class="rounded-xl border p-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <h2 class="text-lg font-bold mb-4" style="color: var(--text-primary);">Recent Requests</h2>
                <div class="space-y-3">
                    @forelse ($recentRequests as $request)
                        <a href="{{ route('admin.requests.index', ['search' => $request->hsr_id]) }}" class="block rounded-lg border p-3 transition-colors duration-200 hover:opacity-90"
                            style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold truncate" style="color: var(--text-primary);">
                                        {{ $request->studentService->hss_title ?? 'Deleted service' }}
                                    </p>
                                    <p class="text-xs" style="color: var(--text-muted);">
                                        {{ $request->requester->hu_name ?? 'Unknown requester' }} to {{ $request->provider->hu_name ?? 'Unknown provider' }}
                                    </p>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded-full" style="background-color: var(--bg-secondary); color: var(--text-secondary);">
                                    {{ $statusLabel($request->hsr_status) }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm" style="color: var(--text-muted);">No requests yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border p-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="text-lg font-bold" style="color: var(--text-primary);">Warning Watchlist</h2>
                    <a href="{{ route('admin.feedback.index') }}" class="text-sm font-semibold text-cyan-500">Review moderation</a>
                </div>
                <div class="space-y-3">
                    @forelse ($highWarningServices as $service)
                        <a href="{{ route('admin.services.show', $service->hss_id) }}" class="flex items-center justify-between gap-3 rounded-lg border p-3"
                            style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate" style="color: var(--text-primary);">{{ $service->hss_title }}</p>
                                <p class="text-xs" style="color: var(--text-muted);">{{ $service->user->hu_name ?? 'Unknown provider' }}</p>
                            </div>
                            <span class="text-sm font-bold text-red-500">{{ $service->hss_warning_count }} warning(s)</span>
                        </a>
                    @empty
                        <p class="text-sm" style="color: var(--text-muted);">No services with warnings.</p>
                    @endforelse
                </div>
            </section>
        </div>

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
