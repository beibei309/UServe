@extends('layouts.helper')

@section('title', 'Buyer Rewards Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Coming Soon Modal Overlay --}}
<div id="coming-soon-overlay"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); background: rgba(17, 10, 50, 0.5);">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full text-center border border-purple-100">

        {{-- Icon --}}
        <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-purple-50 flex items-center justify-center">
            <i class="fas fa-rocket text-purple-600 text-2xl"></i>
        </div>

        {{-- Badge --}}
        <span class="inline-flex items-center gap-1 text-xs font-medium bg-purple-100 text-purple-800 px-3 py-1 rounded-full mb-3">
            <i class="fas fa-clock text-xs"></i> Coming soon
        </span>

        {{-- Heading --}}
        <h2 class="text-lg font-semibold text-purple-900 mb-2">Rewards are almost here</h2>

        {{-- Body --}}
        <p class="text-sm text-gray-500 leading-relaxed mb-6">
            We're finalizing the Buyer Rewards system. You can already earn points —
            redemptions will be unlocked once the system goes live.
        </p>

        {{-- Actions --}}
        <button onclick="dismissOverlay()"
                class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors mb-2">
            Got it, I'll check back later
        </button>
        <button onclick="dismissOverlay()"
                class="w-full py-2 px-4 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 rounded-lg transition-colors">
            Browse anyway
        </button>
    </div>
</div>

<script>
    function dismissOverlay() {
        document.getElementById('coming-soon-overlay').style.display = 'none';
    }
</script>
        {{-- Header Section --}}
        <div class="upsi-card p-4 sm:p-5 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-gift text-white text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Buyer Rewards Dashboard</h1>
                        <p class="text-gray-600 mt-1 text-sm sm:text-base">Earn points and redeem amazing rewards</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <a href="{{ route('points.buyer.history') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center">
                        <i class="fas fa-history mr-2"></i>
                        View History
                    </a>
                    <a href="{{ route('points.leaderboard') }}"
                       class="inline-flex items-center px-4 py-2 border border-purple-300 rounded-lg text-sm font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 transition-colors w-full sm:w-auto justify-center">
                        <i class="fas fa-trophy mr-2"></i>
                        Leaderboard
                    </a>
                </div>
            </div>
        </div>
        
        {{-- Warning Notification --}}
        
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 font-medium">
                        The Reedem rewards are still not finalized and cannot be used at the moment.
                    </p>
                </div>
            </div>
        </div>

        {{-- Points Overview Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 mb-6">
            {{-- Total Buyer Points Card --}}
            <div class="upsi-card p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-coins text-purple-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Available Points</p>
                        <p class="text-2xl sm:text-3xl font-bold text-purple-600">{{ $buyerPoints }}</p>
                    </div>
                </div>
            </div>

            {{-- Points Earned Card --}}
            <div class="upsi-card p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-chart-line text-green-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Earned</p>
                        <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ $totalEarnedPoints }}</p>
                    </div>
                </div>
            </div>

            {{-- Rewards Redeemed Card --}}
            <div class="upsi-card p-4 sm:p-5 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center">
                    <div class="bg-pink-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-trophy text-pink-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Rewards Redeemed</p>
                        <p class="text-2xl sm:text-3xl font-bold text-pink-600">{{ $rewardRedemptions->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Available Rewards Section --}}
        <div class="upsi-card p-4 sm:p-5 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-2 sm:space-y-0">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Available Rewards</h3>
                <p class="text-sm text-gray-600">Choose your rewards with {{ $buyerPoints }} points</p>
            </div>

            @if($availableRewards->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    @foreach($availableRewards as $reward)
                        <div class="{{ $reward->ui_card_classes }}">
                            
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base">{{ $reward->hr_title }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $reward->hr_description }}</p>
                                </div>
                                <div class="ml-2 text-right">
                                    <span class="{{ $reward->ui_badge_classes }}">
                                        {{ $reward->hr_points_cost }} pts
                                    </span>
                                </div>
                            </div>

                            @if($reward->hr_value > 0)
                                <div class="{{ $reward->ui_price_classes }}">
                                    @if($reward->hr_type === 'discount')
                                        {{ $reward->hr_value }}% OFF
                                    @else
                                        RM {{ number_format($reward->hr_value, 2) }}
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                @if($reward->ui_can_redeem)
                                    <form action="{{ route('points.rewards.redeem') }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="reward_id" value="{{ $reward->hr_id }}">
                                        <button type="submit" class="{{ $reward->ui_button_classes }}">
                                            Redeem Now
                                        </button>
                                    </form>
                                @else
                                    <span class="w-full text-center text-xs text-gray-500 py-2">
                                        @if($buyerPoints < $reward->hr_points_cost)
                                            Need {{ $reward->hr_points_cost - $buyerPoints }} more points
                                        @else
                                            Limit reached
                                        @endif
                                    </span>
                                @endif
                            </div>

                            @if($reward->hr_user_limit > 1)
                                <div class="mt-2 text-xs text-gray-500 text-center">
                                    Used {{ $reward->ui_user_redemptions_count }}/{{ $reward->hr_user_limit }} times
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-gift text-4xl mb-4 text-gray-300"></i>
                    <p>No rewards available at the moment</p>
                </div>
            @endif
        </div>

        {{-- Recent Points & Redemptions Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
            {{-- Recent Points Earned --}}
            <div class="upsi-card p-4 sm:p-5">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">Recent Points Earned</h3>
                @if($recentBuyerPoints->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentBuyerPoints as $point)
                            <div class="flex items-start justify-between gap-3 py-2 border-b border-gray-100 last:border-0">
                                <div class="flex min-w-0 flex-1 items-start space-x-3">
                                    <div class="bg-green-100 p-1.5 rounded-full flex-shrink-0">
                                        <i class="fas fa-plus text-green-600 text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium leading-snug text-gray-900 break-words">{{ $point->hbp_description }}</p>
                                        <p class="text-xs text-gray-500">{{ $point->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <span class="flex-shrink-0 text-sm font-medium text-green-600">+{{ $point->hbp_points_earned }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm text-center py-4">No points earned yet</p>
                @endif
            </div>

            {{-- Recent Redemptions --}}
            <div class="upsi-card p-4 sm:p-5">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">My Reward Redemptions</h3>
                @if($rewardRedemptions->count() > 0)
                    <div class="space-y-3">
                        @foreach($rewardRedemptions as $redemption)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $redemption->reward->hr_title }}</h4>
                                    <span class="{{ $redemption->ui_status_classes }}">
                                        {{ ucfirst($redemption->hrr_status) }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <p><strong>Code:</strong> {{ $redemption->hrr_redemption_code }}</p>
                                    <p><strong>Redeemed:</strong> {{ $redemption->hrr_redeemed_at->format('M d, Y') }}</p>
                                    @if($redemption->hrr_expires_at)
                                        <p><strong>Expires:</strong> {{ $redemption->hrr_expires_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm text-center py-4">No rewards redeemed yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
