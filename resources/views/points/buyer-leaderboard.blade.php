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
                <div class="divide-y divide-gray-100">
                    @foreach($buyerLeaderboard as $index => $user)
                    <div class="p-4 sm:p-6 {{ $index < 3 ? 'bg-purple-50' : '' }} 
                              {{ Auth::id() == $user->hu_id ? 'ring-2 ring-purple-300 bg-purple-25' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                {{-- Rank Badge --}}
                                <div class="flex-shrink-0">
                                    @if($index === 0)
                                        <div class="w-12 h-12 bg-yellow-500 text-white rounded-full flex items-center justify-center">
                                            <i class="fas fa-crown text-lg"></i>
                                        </div>
                                    @elseif($index === 1)
                                        <div class="w-12 h-12 bg-gray-400 text-white rounded-full flex items-center justify-center">
                                            <i class="fas fa-medal text-lg"></i>
                                        </div>
                                    @elseif($index === 2)
                                        <div class="w-12 h-12 bg-orange-500 text-white rounded-full flex items-center justify-center">
                                            <i class="fas fa-award text-lg"></i>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center">
                                            <span class="text-lg font-bold">{{ $index + 1 }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- User Info --}}
                                <div class="min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $user->hu_name }}
                                        </p>
                                        @if(Auth::id() == $user->hu_id)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                You
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ ucfirst($user->hu_role) }} • Service Requester</p>
                                    
                                    {{-- Additional stats if available --}}
                                    @if($user->relationLoaded('buyerPoints'))
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $user->buyerPoints->where('hbp_status', 'earned')->where('hbp_points_earned', '>', 0)->count() }} completed requests
                                    </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Points Display --}}
                            <div class="text-right">
                                <div class="flex items-center space-x-2">
                                    @if($index < 3)
                                        <div class="bg-purple-500 text-white px-4 py-2 rounded-lg">
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-star text-sm"></i>
                                                <span class="font-bold text-lg">{{ $user->buyer_points_sum_hbp_points_earned ?? 0 }}</span>
                                            </div>
                                            <p class="text-xs text-purple-100">points</p>
                                        </div>
                                    @else
                                        <div class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg">
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-star text-sm"></i>
                                                <span class="font-bold text-lg">{{ $user->buyer_points_sum_hbp_points_earned ?? 0 }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500">points</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
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
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <i class="fas fa-percentage text-green-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-900">10% Discount</p>
                    <p class="text-sm text-gray-600">2 points</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <i class="fas fa-money-bill-wave text-blue-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-900">RM 5 Credit</p>
                    <p class="text-sm text-gray-600">3 points</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <i class="fas fa-star text-yellow-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-900">Priority Support</p>
                    <p class="text-sm text-gray-600">5 points</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection