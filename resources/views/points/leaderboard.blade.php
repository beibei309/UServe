@extends('layouts.helper')

@section('title', 'Points Leaderboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="bg-gradient-to-br from-amber-400 to-orange-500 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-trophy text-white text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Points Leaderboard</h1>
                        <p class="text-gray-600 mt-1 text-sm sm:text-base">Shared rankings across Seller and Buyer tracks.</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <a href="{{ auth()->user()?->canAccessSellerFeatures() ? route('points.dashboard') : route('points.buyer.dashboard') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 lg:col-span-2">
                <h3 class="text-lg font-bold text-gray-900 mb-3">How ranking works</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li>Seller leaderboard ranks helpers by seller points earned from completed services.</li>
                    <li>Buyer leaderboard ranks all users by buyer points earned from completed requests.</li>
                    <li>Both tracks are visible to all users for transparency.</li>
                </ul>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Your standing</h3>
                <div class="space-y-3 text-sm">
                    @if($canViewSellerStanding)
                        <div class="flex items-center justify-between rounded-lg bg-orange-50 border border-orange-100 px-3 py-2">
                            <span class="text-gray-700">Seller Rank</span>
                            <span class="font-semibold text-orange-700">{{ $userSellerRank ? ('#' . $userSellerRank) : 'Not ranked yet' }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between rounded-lg bg-purple-50 border border-purple-100 px-3 py-2">
                        <span class="text-gray-700">Buyer Rank</span>
                        <span class="font-semibold text-purple-700">{{ $userBuyerRank ? ('#' . $userBuyerRank) : 'Not ranked yet' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Stats</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="bg-orange-100 p-3 rounded-lg mx-auto w-fit mb-2">
                        <i class="fas fa-user-tie text-orange-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-orange-600">{{ $sellerRankedCount }}</p>
                    <p class="text-sm text-gray-600">Ranked Sellers</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-purple-100 p-3 rounded-lg mx-auto w-fit mb-2">
                        <i class="fas fa-user-friends text-purple-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-purple-600">{{ $buyerRankedCount }}</p>
                    <p class="text-sm text-gray-600">Ranked Buyers</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 p-3 rounded-lg mx-auto w-fit mb-2">
                        <i class="fas fa-coins text-green-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-green-600">{{ $sellerLeaderboard->first()?->seller_points_sum_hsp_points_earned ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Top Seller Points</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-blue-100 p-3 rounded-lg mx-auto w-fit mb-2">
                        <i class="fas fa-gift text-blue-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-blue-600">{{ $buyerLeaderboard->first()?->buyer_points_sum_hbp_points_earned ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Top Buyer Points</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Seller Leaderboard --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-yellow-400 to-orange-500 p-2 rounded-lg">
                            <i class="fas fa-crown text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Top Sellers</h2>
                            <p class="text-sm text-gray-600">Students providing services</p>
                        </div>
                    </div>
                    <a href="{{ route('points.leaderboard.seller') }}" 
                       class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                        View All
                    </a>
                </div>

                @if($sellerLeaderboard && $sellerLeaderboard->count() > 0)
                    @if($sellerLeaderboard->count() >= 3)
                        <div class="rounded-2xl bg-gradient-to-b from-indigo-500 to-blue-600 p-5 mb-4 shadow-sm">
                            <div class="grid grid-cols-3 gap-3 items-end text-center text-white">
                                <div>
                                    <img src="{{ $sellerLeaderboard->get(1)?->hu_profile_photo_path ? asset($sellerLeaderboard->get(1)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($sellerLeaderboard->get(1)?->hu_name ?? 'User')) }}"
                                         alt="{{ $sellerLeaderboard->get(1)?->hu_name }}"
                                         class="w-12 h-12 rounded-full mx-auto mb-2 border-2 border-white/70 object-cover shadow" />
                                    <p class="text-xs font-semibold truncate">{{ $sellerLeaderboard->get(1)?->hu_name }}</p>
                                    <p class="text-[11px] text-indigo-100">2nd</p>
                                    <div class="mt-2 h-14 rounded-t-lg bg-cyan-300/90 border border-white/25 border-b-0 flex items-center justify-center text-xl font-bold">2</div>
                                </div>
                                <div>
                                    <img src="{{ $sellerLeaderboard->get(0)?->hu_profile_photo_path ? asset($sellerLeaderboard->get(0)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($sellerLeaderboard->get(0)?->hu_name ?? 'User')) }}"
                                         alt="{{ $sellerLeaderboard->get(0)?->hu_name }}"
                                         class="w-14 h-14 rounded-full mx-auto mb-2 border-2 border-white object-cover shadow" />
                                    <p class="text-sm font-semibold truncate">{{ $sellerLeaderboard->get(0)?->hu_name }}</p>
                                    <p class="text-[11px] text-indigo-100">1st • {{ $sellerLeaderboard->get(0)?->seller_points_sum_hsp_points_earned ?? 0 }} pts</p>
                                    <div class="mt-2 h-20 rounded-t-lg bg-amber-300/90 border border-white/25 border-b-0 flex items-center justify-center text-2xl font-bold">1</div>
                                </div>
                                <div>
                                    <img src="{{ $sellerLeaderboard->get(2)?->hu_profile_photo_path ? asset($sellerLeaderboard->get(2)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($sellerLeaderboard->get(2)?->hu_name ?? 'User')) }}"
                                         alt="{{ $sellerLeaderboard->get(2)?->hu_name }}"
                                         class="w-12 h-12 rounded-full mx-auto mb-2 border-2 border-white/70 object-cover shadow" />
                                    <p class="text-xs font-semibold truncate">{{ $sellerLeaderboard->get(2)?->hu_name }}</p>
                                    <p class="text-[11px] text-indigo-100">3rd</p>
                                    <div class="mt-2 h-10 rounded-t-lg bg-sky-200/90 border border-white/25 border-b-0 flex items-center justify-center text-xl font-bold">3</div>
                                </div>
                            </div>
                            <div class="h-3 rounded-b-lg bg-white/25 border border-white/20 border-t-0"></div>
                        </div>

                        <div class="divide-y divide-gray-100 rounded-xl border border-gray-100 overflow-hidden">
                            @foreach($sellerLeaderboard->slice(3)->values() as $index => $user)
                                <div class="flex items-center justify-between px-3 py-3 bg-white hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-6 text-sm text-gray-500">{{ $index + 4 }}</span>
                                        <img src="{{ $user->hu_profile_photo_path ? asset($user->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($user->hu_name ?? 'User')) }}"
                                             alt="{{ $user->hu_name }}"
                                             class="w-7 h-7 rounded-full object-cover border border-gray-200" />
                                        <span class="text-sm text-gray-900 truncate">{{ $user->hu_name }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">{{ $user->seller_points_sum_hsp_points_earned ?? 0 }} pts</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($sellerLeaderboard as $index => $user)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</div>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $user->hu_name }}</p>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">{{ $user->seller_points_sum_hsp_points_earned ?? 0 }} pts</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-400 text-3xl mb-4"></i>
                        <p class="text-gray-600">No sellers with points yet!</p>
                    </div>
                @endif
            </div>

            {{-- Buyer Leaderboard --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-2 rounded-lg">
                            <i class="fas fa-star text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Top Buyers</h2>
                            <p class="text-sm text-gray-600">Active service requesters</p>
                        </div>
                    </div>
                    <a href="{{ route('points.leaderboard.buyer') }}" 
                       class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                        View All
                    </a>
                </div>

                @if($buyerLeaderboard->count() > 0)
                    @if($buyerLeaderboard->count() >= 3)
                        <div class="rounded-2xl bg-gradient-to-b from-indigo-500 to-blue-600 p-5 mb-4 shadow-sm">
                            <div class="grid grid-cols-3 gap-3 items-end text-center text-white">
                                <div>
                                    <img src="{{ $buyerLeaderboard->get(1)?->hu_profile_photo_path ? asset($buyerLeaderboard->get(1)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($buyerLeaderboard->get(1)?->hu_name ?? 'User')) }}"
                                         alt="{{ $buyerLeaderboard->get(1)?->hu_name }}"
                                         class="w-12 h-12 rounded-full mx-auto mb-2 border-2 border-white/70 object-cover shadow" />
                                    <p class="text-xs font-semibold truncate">{{ $buyerLeaderboard->get(1)?->hu_name }}</p>
                                    <p class="text-[11px] text-indigo-100">2nd</p>
                                    <div class="mt-2 h-14 rounded-t-lg bg-cyan-300/90 border border-white/25 border-b-0 flex items-center justify-center text-xl font-bold">2</div>
                                </div>
                                <div>
                                    <img src="{{ $buyerLeaderboard->get(0)?->hu_profile_photo_path ? asset($buyerLeaderboard->get(0)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($buyerLeaderboard->get(0)?->hu_name ?? 'User')) }}"
                                         alt="{{ $buyerLeaderboard->get(0)?->hu_name }}"
                                         class="w-14 h-14 rounded-full mx-auto mb-2 border-2 border-white object-cover shadow" />
                                    <p class="text-sm font-semibold truncate">{{ $buyerLeaderboard->get(0)?->hu_name }}</p>
                                    <p class="text-[11px] text-indigo-100">1st • {{ $buyerLeaderboard->get(0)?->buyer_points_sum_hbp_points_earned ?? 0 }} pts</p>
                                    <div class="mt-2 h-20 rounded-t-lg bg-amber-300/90 border border-white/25 border-b-0 flex items-center justify-center text-2xl font-bold">1</div>
                                </div>
                                <div>
                                    <img src="{{ $buyerLeaderboard->get(2)?->hu_profile_photo_path ? asset($buyerLeaderboard->get(2)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($buyerLeaderboard->get(2)?->hu_name ?? 'User')) }}"
                                         alt="{{ $buyerLeaderboard->get(2)?->hu_name }}"
                                         class="w-12 h-12 rounded-full mx-auto mb-2 border-2 border-white/70 object-cover shadow" />
                                    <p class="text-xs font-semibold truncate">{{ $buyerLeaderboard->get(2)?->hu_name }}</p>
                                    <p class="text-[11px] text-indigo-100">3rd</p>
                                    <div class="mt-2 h-10 rounded-t-lg bg-sky-200/90 border border-white/25 border-b-0 flex items-center justify-center text-xl font-bold">3</div>
                                </div>
                            </div>
                            <div class="h-3 rounded-b-lg bg-white/25 border border-white/20 border-t-0"></div>
                        </div>

                        <div class="divide-y divide-gray-100 rounded-xl border border-gray-100 overflow-hidden">
                            @foreach($buyerLeaderboard->slice(3)->values() as $index => $user)
                                <div class="flex items-center justify-between px-3 py-3 bg-white hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-6 text-sm text-gray-500">{{ $index + 4 }}</span>
                                        <img src="{{ $user->hu_profile_photo_path ? asset($user->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($user->hu_name ?? 'User')) }}"
                                             alt="{{ $user->hu_name }}"
                                             class="w-7 h-7 rounded-full object-cover border border-gray-200" />
                                        <span class="text-sm text-gray-900 truncate">{{ $user->hu_name }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">{{ $user->buyer_points_sum_hbp_points_earned ?? 0 }} pts</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($buyerLeaderboard as $index => $user)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</div>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $user->hu_name }}</p>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">{{ $user->buyer_points_sum_hbp_points_earned ?? 0 }} pts</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-400 text-3xl mb-4"></i>
                        <p class="text-gray-600">No buyers with points yet!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection