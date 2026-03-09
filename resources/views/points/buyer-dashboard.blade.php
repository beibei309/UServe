@extends('layouts.helper')

@section('title', 'Buyer Rewards Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
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
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('points.buyer.history') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center">
                        <i class="fas fa-history mr-2"></i>
                        View History
                    </a>
                </div>
            </div>
        </div>

        {{-- Points Overview Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
            {{-- Total Buyer Points Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 sm:col-span-2 lg:col-span-1">
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-2 sm:space-y-0">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Available Rewards</h3>
                <p class="text-sm text-gray-600">Choose your rewards with {{ $buyerPoints }} points</p>
            </div>

            @if($availableRewards->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($availableRewards as $reward)
                        @php
                            $cardClasses = 'border rounded-lg p-4 sm:p-6 hover:shadow-md transition-shadow';
                            $badgeClasses = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium';
                            $priceClasses = 'text-lg font-bold mb-3';
                            $buttonClasses = 'w-full px-3 py-2 rounded-lg text-sm font-medium text-white transition-colors';
                            
                            if($reward->hr_type === 'discount') {
                                $cardClasses .= ' bg-purple-50 border-purple-200';
                                $badgeClasses .= ' bg-purple-100 text-purple-800';
                                $priceClasses .= ' text-purple-600';
                                $buttonClasses .= ' bg-purple-600 hover:bg-purple-700';
                            } elseif($reward->hr_type === 'service_credit') {
                                $cardClasses .= ' bg-blue-50 border-blue-200';
                                $badgeClasses .= ' bg-blue-100 text-blue-800';
                                $priceClasses .= ' text-blue-600';
                                $buttonClasses .= ' bg-blue-600 hover:bg-blue-700';
                            } else {
                                $cardClasses .= ' bg-green-50 border-green-200';
                                $badgeClasses .= ' bg-green-100 text-green-800';
                                $priceClasses .= ' text-green-600';
                                $buttonClasses .= ' bg-green-600 hover:bg-green-700';
                            }
                        @endphp
                        
                        <div class="{{ $cardClasses }}">
                            
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base">{{ $reward->hr_title }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $reward->hr_description }}</p>
                                </div>
                                <div class="ml-2 text-right">
                                    <span class="{{ $badgeClasses }}">
                                        {{ $reward->hr_points_cost }} pts
                                    </span>
                                </div>
                            </div>

                            @if($reward->hr_value > 0)
                                <div class="{{ $priceClasses }}">
                                    @if($reward->hr_type === 'discount')
                                        {{ $reward->hr_value }}% OFF
                                    @else
                                        RM {{ number_format($reward->hr_value, 2) }}
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                @if($reward->canUserRedeem(auth()->user()))
                                    <form action="{{ route('points.rewards.redeem') }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="reward_id" value="{{ $reward->hr_id }}">
                                        <button type="submit" class="{{ $buttonClasses }}">
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
                                @php
                                    $userRedemptions = $reward->redemptions()->where('hrr_user_id', auth()->user()->hu_id)->whereIn('hrr_status', ['active', 'used'])->count();
                                @endphp
                                <div class="mt-2 text-xs text-gray-500 text-center">
                                    Used {{ $userRedemptions }}/{{ $reward->hr_user_limit }} times
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            {{-- Recent Points Earned --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">Recent Points Earned</h3>
                @if($recentBuyerPoints->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentBuyerPoints as $point)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-1.5 rounded-full flex-shrink-0">
                                        <i class="fas fa-plus text-green-600 text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $point->hbp_description }}</p>
                                        <p class="text-xs text-gray-500">{{ $point->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-green-600">+{{ $point->hbp_points_earned }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm text-center py-4">No points earned yet</p>
                @endif
            </div>

            {{-- Recent Redemptions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">My Reward Redemptions</h3>
                @if($rewardRedemptions->count() > 0)
                    <div class="space-y-3">
                        @foreach($rewardRedemptions as $redemption)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $redemption->reward->hr_title }}</h4>
                                    @php
                                        $statusClasses = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium';
                                        if($redemption->hrr_status === 'active') {
                                            $statusClasses .= ' bg-green-100 text-green-800';
                                        } elseif($redemption->hrr_status === 'used') {
                                            $statusClasses .= ' bg-blue-100 text-blue-800';
                                        } elseif($redemption->hrr_status === 'expired') {
                                            $statusClasses .= ' bg-red-100 text-red-800';
                                        } else {
                                            $statusClasses .= ' bg-gray-100 text-gray-800';
                                        }
                                    @endphp
                                    <span class="{{ $statusClasses }}">
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