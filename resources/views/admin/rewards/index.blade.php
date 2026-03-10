@extends('admin.layout')

@section('title', 'Rewards Management')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Rewards Management</h1>
            <p class="mt-1 transition-colors duration-300" style="color: var(--text-secondary);">Manage rewards, redemptions, and analytics</p>
        </div>
        <a href="{{ route('admin.rewards.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
            <i class="fas fa-plus mr-2"></i>Create Reward
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="rounded-lg shadow p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-gift text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Total Rewards</p>
                    <p class="text-2xl font-semibold transition-colors duration-300" style="color: var(--text-primary);">{{ $totalRewards }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Active Rewards</p>
                    <p class="text-2xl font-semibold transition-colors duration-300" style="color: var(--text-primary);">{{ $activeRewards }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Total Redemptions</p>
                    <p class="text-2xl font-semibold transition-colors duration-300" style="color: var(--text-primary);">{{ $totalRedemptions }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Pending Redemptions</p>
                    <p class="text-2xl font-semibold transition-colors duration-300" style="color: var(--text-primary);">{{ $pendingRedemptions }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('admin.rewards.list') }}" 
              class="block rounded-lg shadow p-6 hover:shadow-lg transition-all duration-200 surface-hover" 
              style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <i class="fas fa-list text-blue-500 text-2xl mr-4"></i>
                <div>
                    <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Manage Rewards</h3>
                    <p class="transition-colors duration-300" style="color: var(--text-secondary);">View and edit all rewards</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.rewards.redemptions') }}" 
              class="block rounded-lg shadow p-6 hover:shadow-lg transition-all duration-200 surface-hover" 
              style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <i class="fas fa-history text-purple-500 text-2xl mr-4"></i>
                <div>
                    <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Redemptions</h3>
                    <p class="transition-colors duration-300" style="color: var(--text-secondary);">Track all redemptions</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.rewards.analytics') }}" 
              class="block rounded-lg shadow p-6 hover:shadow-lg transition-all duration-200 surface-hover" 
              style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <i class="fas fa-chart-bar text-green-500 text-2xl mr-4"></i>
                <div>
                    <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Analytics</h3>
                    <p class="transition-colors duration-300" style="color: var(--text-secondary);">View detailed analytics</p>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Redemptions -->
        <div class="rounded-lg shadow transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
            <div class="px-6 py-4 transition-colors duration-300" style="border-bottom: 1px solid var(--border-color);">
                <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Recent Redemptions</h3>
            </div>
            <div class="p-6">
                @if($recentRedemptions->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentRedemptions as $redemption)
                        <div class="flex items-center justify-between py-2">
                            <div class="flex-1">
                                <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $redemption->user->hu_name ?? 'Unknown User' }}</p>
                                <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $redemption->reward->hr_title ?? 'Deleted Reward' }}</p>
                                <p class="text-xs transition-colors duration-300" style="color: var(--text-muted);">{{ $redemption->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    @if($redemption->hrr_status === 'approved') bg-green-100 text-green-800
                                    @elseif($redemption->hrr_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($redemption->hrr_status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($redemption->hrr_status) }}
                                </span>
                                <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-secondary);">{{ $redemption->hrr_points_used }} pts</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.rewards.redemptions') }}" 
                           class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
                    </div>
                @else
                    <p class="text-center py-4 transition-colors duration-300" style="color: var(--text-secondary);">No redemptions yet</p>
                @endif
            </div>
        </div>

        <!-- Popular Rewards & Expiring Soon -->
        <div class="space-y-6">
            <!-- Popular Rewards -->
            <div class="rounded-lg shadow transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 transition-colors duration-300" style="border-bottom: 1px solid var(--border-color);">
                    <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Popular Rewards</h3>
                </div>
                <div class="p-6">
                    @if($popularRewards->count() > 0)
                        <div class="space-y-3">
                            @foreach($popularRewards as $reward)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $reward->hr_title }}</p>
                                    <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ $reward->hr_points_cost }} points</p>
                                </div>
                                <span class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">
                                    {{ $reward->redemptions_count }} redemptions
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center py-4 transition-colors duration-300" style="color: var(--text-secondary);">No redemptions yet</p>
                    @endif
                </div>
            </div>

            <!-- Expiring Soon -->
            @if($expiringSoon->count() > 0)
            <div class="rounded-lg shadow transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 transition-colors duration-300" style="border-bottom: 1px solid var(--border-color);">
                    <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Expiring Soon</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($expiringSoon as $reward)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $reward->hr_title }}</p>
                                <p class="text-sm text-red-500">
                                    Expires: {{ $reward->hr_expires_at->format('M d, Y') }}
                                </p>
                            </div>
                            <a href="{{ route('admin.rewards.edit', $reward) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <div id="adminModuleRewardsIndexConfig"
        data-success-message="{{ session('success') }}"></div>
    <script src="{{ asset('js/admin-rewards-index.js') }}"></script>
@endsection