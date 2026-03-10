@extends('layouts.helper')

@section('title', 'Buyer Leaderboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="bg-purple-500 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-star text-white text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Buyer Leaderboard</h1>
                        <p class="text-gray-600 mt-1 text-sm sm:text-base">Top-ranked service requesters</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <a href="{{ route('points.leaderboard') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>

        {{-- Your Rank Card --}}
        @if($userRank)
        <div class="bg-purple-500 text-white rounded-xl p-4 sm:p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">Your Rank</h2>
                    <p class="text-purple-100">Current position in buyer leaderboard</p>
                </div>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 backdrop-blur rounded-lg p-4">
                        <p class="text-2xl font-bold">#{{ $userRank }}</p>
                        <p class="text-purple-100 text-sm">of {{ $buyerLeaderboard->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Leaderboard --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Top Buyers</h2>
                <p class="text-sm text-gray-600">Users who have earned points by requesting services</p>
            </div>

            @if($buyerLeaderboard->count() > 0)
                @if($buyerLeaderboard->count() >= 3)
                    <div class="bg-gradient-to-b from-indigo-500 to-blue-600 p-5 text-white">
                        <div class="grid grid-cols-3 gap-4 items-end text-center">
                            <div>
                                <img src="{{ $buyerLeaderboard->get(1)?->hu_profile_photo_path ? asset($buyerLeaderboard->get(1)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($buyerLeaderboard->get(1)?->hu_name ?? 'User')) }}"
                                     alt="{{ $buyerLeaderboard->get(1)?->hu_name }}"
                                     class="w-14 h-14 rounded-full mx-auto mb-2 border-2 border-white/70 object-cover shadow" />
                                <p class="text-sm font-semibold truncate">{{ $buyerLeaderboard->get(1)?->hu_name }}</p>
                                <p class="text-xs text-indigo-100">2nd • {{ $buyerLeaderboard->get(1)?->buyer_points_sum_hbp_points_earned ?? 0 }} pts</p>
                                <div class="mt-2 h-16 rounded-t-lg bg-cyan-300/90 border border-white/25 border-b-0 flex items-center justify-center text-2xl font-bold">2</div>
                            </div>
                            <div>
                                <img src="{{ $buyerLeaderboard->get(0)?->hu_profile_photo_path ? asset($buyerLeaderboard->get(0)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($buyerLeaderboard->get(0)?->hu_name ?? 'User')) }}"
                                     alt="{{ $buyerLeaderboard->get(0)?->hu_name }}"
                                     class="w-16 h-16 rounded-full mx-auto mb-2 border-2 border-white object-cover shadow" />
                                <p class="text-base font-semibold truncate">{{ $buyerLeaderboard->get(0)?->hu_name }}</p>
                                <p class="text-xs text-indigo-100">1st • {{ $buyerLeaderboard->get(0)?->buyer_points_sum_hbp_points_earned ?? 0 }} pts</p>
                                <div class="mt-2 h-24 rounded-t-lg bg-amber-300/90 border border-white/25 border-b-0 flex items-center justify-center text-3xl font-bold">1</div>
                            </div>
                            <div>
                                <img src="{{ $buyerLeaderboard->get(2)?->hu_profile_photo_path ? asset($buyerLeaderboard->get(2)?->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($buyerLeaderboard->get(2)?->hu_name ?? 'User')) }}"
                                     alt="{{ $buyerLeaderboard->get(2)?->hu_name }}"
                                     class="w-14 h-14 rounded-full mx-auto mb-2 border-2 border-white/70 object-cover shadow" />
                                <p class="text-sm font-semibold truncate">{{ $buyerLeaderboard->get(2)?->hu_name }}</p>
                                <p class="text-xs text-indigo-100">3rd • {{ $buyerLeaderboard->get(2)?->buyer_points_sum_hbp_points_earned ?? 0 }} pts</p>
                                <div class="mt-2 h-12 rounded-t-lg bg-sky-200/90 border border-white/25 border-b-0 flex items-center justify-center text-2xl font-bold">3</div>
                            </div>
                        </div>
                        <div class="h-3 rounded-b-lg bg-white/25 border border-white/20 border-t-0"></div>
                    </div>
                @endif

                <div class="divide-y divide-gray-100">
                    @foreach(($buyerLeaderboard->count() >= 3 ? $buyerLeaderboard->slice(3)->values() : $buyerLeaderboard->values()) as $index => $user)
                        <div class="px-4 sm:px-6 py-4 {{ Auth::id() == $user->hu_id ? 'bg-purple-50 border-l-4 border-purple-500' : 'bg-white' }}">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-7 text-sm text-gray-500">{{ ($buyerLeaderboard->count() >= 3 ? 4 : 1) + $index }}</span>
                                    <img src="{{ $user->hu_profile_photo_path ? asset($user->hu_profile_photo_path) : ('https://ui-avatars.com/api/?name=' . urlencode($user->hu_name ?? 'User')) }}"
                                         alt="{{ $user->hu_name }}"
                                         class="w-8 h-8 rounded-full object-cover border border-gray-200" />
                                    <span class="text-sm font-medium text-gray-900 truncate">{{ $user->hu_name }}</span>
                                    @if(Auth::id() == $user->hu_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">You</span>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $user->buyer_points_sum_hbp_points_earned ?? 0 }} pts</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Buyers Yet</h3>
                    <p class="text-gray-600">Be the first to earn points by requesting services!</p>
                </div>
            @endif
        </div>

        {{-- How to Earn Points --}}
        <div class="mt-8 bg-purple-50 border border-purple-200 rounded-xl p-4 sm:p-6">
            <div class="flex items-start space-x-3">
                <div class="bg-purple-500 p-2 rounded-lg flex-shrink-0">
                    <i class="fas fa-lightbulb text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">How to Earn Buyer Points</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Complete service requests as a requester</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Each completed service earns you 1 buyer point</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500"></i>
                            <span>Redeem points for discounts, credits, and rewards</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Available Rewards Preview --}}
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Available Rewards</h3>
                <a href="{{ route('points.buyer.dashboard') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                    View Rewards Store →
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($availableRewards as $reward)
                    <div class="{{ $reward->ui_card_classes }} rounded-lg p-4 text-center">
                        <i class="{{ $reward->ui_icon }} {{ $reward->ui_icon_classes }} text-2xl mb-2"></i>
                        <p class="font-semibold text-gray-900">{{ $reward->hr_title }}</p>
                        <p class="text-sm text-gray-600">{{ $reward->hr_points_cost }} points</p>
                    </div>
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 text-sm text-gray-500 text-center py-4">
                        No active rewards available right now.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection