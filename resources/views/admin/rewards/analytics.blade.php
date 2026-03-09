@extends('admin.layout')

@section('title', 'Rewards Analytics')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Rewards Analytics</h1>
            <p class="mt-1 transition-colors duration-300" style="color: var(--text-secondary);">Detailed insights into reward redemptions and trends</p>
        </div>
        <a href="{{ route('admin.rewards.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Redemptions by Type -->
        <div class="rounded-lg shadow transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="px-6 py-4 transition-colors duration-300" style="border-bottom: 1px solid var(--border-color);">
                <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Redemptions by Type</h3>
            </div>
            <div class="p-6">
                @if($redemptionsByType->count() > 0)
                    <div class="space-y-4">
                        @foreach($redemptionsByType as $type)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full
                                    @if($type->hr_type === 'discount') bg-blue-100 text-blue-800
                                    @elseif($type->hr_type === 'service_credit') bg-green-100 text-green-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $type->hr_type)) }}
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">{{ $type->total }}</div>
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">redemptions</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No redemptions yet</p>
                @endif
            </div>
        </div>

        <!-- Top Redeemers -->
        <div class="rounded-lg shadow transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="px-6 py-4 transition-colors duration-300" style="border-bottom: 1px solid var(--border-color);">
                <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Top Redeemers</h3>
            </div>
            <div class="p-6">
                @if($topRedeemers->count() > 0)
                    <div class="space-y-4">
                        @foreach($topRedeemers as $index => $redeemer)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 transition-colors duration-300" style="background-color: var(--bg-tertiary);">
                                    <span class="text-sm font-semibold transition-colors duration-300" style="color: var(--text-secondary);">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <div class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $redeemer->user->hu_name ?? 'Unknown User' }}</div>
                                    <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $redeemer->user->email ?? 'No email' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">{{ $redeemer->redemption_count }}</div>
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $redeemer->total_points }} points</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No redemptions yet</p>
                @endif
            </div>
        </div>

        <!-- Monthly Redemptions Chart -->
        <div class="rounded-lg shadow lg:col-span-2 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="px-6 py-4 transition-colors duration-300" style="border-bottom: 1px solid var(--border-color);">
                <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Monthly Redemptions (Last 12 Months)</h3>
            </div>
            <div class="p-6">
                @if($redemptionsByMonth->count() > 0)
                    <div class="space-y-4">
                        @php
                            $maxRedemptions = $redemptionsByMonth->max('total');
                            $months = [
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                            ];
                        @endphp
                        
                        @foreach($redemptionsByMonth as $month)
                        @php
                            $percentage = $maxRedemptions > 0 ? ($month->total / $maxRedemptions) * 100 : 0;
                        @endphp
                        <div class="flex items-center">
                            <div class="w-24 text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">
                                {{ $months[$month->month] ?? 'Month ' . $month->month }}
                            </div>
                            <div class="flex-1 mx-4">
                                <div class="rounded-full h-4 transition-colors duration-300" style="background-color: var(--bg-tertiary);">
                                    <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div class="w-16 text-right text-sm font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                                {{ $month->total }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-8 transition-colors duration-300" style="color: var(--text-secondary);">No redemption data available for the last 12 months</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="rounded-lg shadow p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Total Redemptions</p>
                    <p class="text-2xl font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                        {{ $redemptionsByType->sum('total') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-coins text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Points Redeemed</p>
                    <p class="text-2xl font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                        {{ $topRedeemers->sum('total_points') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Active Redeemers</p>
                    <p class="text-2xl font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                        {{ $topRedeemers->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">This Month</p>
                    <p class="text-2xl font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                        {{ $redemptionsByMonth->where('month', now()->month)->first()->total ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection