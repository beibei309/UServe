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
                        <p class="text-gray-600 mt-1 text-sm sm:text-base">See how you rank against other users</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <a href="{{ route('points.dashboard') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
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
                    <div class="space-y-3">
                        @foreach($sellerLeaderboard as $index => $user)
                        <div class="flex items-center justify-between p-3 rounded-lg 
                                  {{ $index < 3 ? 'bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200' : 'bg-gray-50' }}">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full 
                                          {{ $index < 3 ? 'bg-orange-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                    @if($index === 0)
                                        <i class="fas fa-crown text-xs"></i>
                                    @elseif($index === 1)
                                        <i class="fas fa-medal text-xs"></i>
                                    @elseif($index === 2)
                                        <i class="fas fa-award text-xs"></i>
                                    @else
                                        <span class="text-sm font-bold">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->hu_name }}</p>
                                    <p class="text-xs text-gray-600">Student</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ $user->seller_points_sum_hsp_points_earned ?? 0 }} pts
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
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
                    <div class="space-y-3">
                        @foreach($buyerLeaderboard as $index => $user)
                        <div class="flex items-center justify-between p-3 rounded-lg 
                                  {{ $index < 3 ? 'bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200' : 'bg-gray-50' }}">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full 
                                          {{ $index < 3 ? 'bg-purple-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                    @if($index === 0)
                                        <i class="fas fa-crown text-xs"></i>
                                    @elseif($index === 1)
                                        <i class="fas fa-medal text-xs"></i>
                                    @elseif($index === 2)
                                        <i class="fas fa-award text-xs"></i>
                                    @else
                                        <span class="text-sm font-bold">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->hu_name }}</p>
                                    <p class="text-xs text-gray-600">{{ ucfirst($user->hu_role) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $user->buyer_points_sum_hbp_points_earned ?? 0 }} pts
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-400 text-3xl mb-4"></i>
                        <p class="text-gray-600">No buyers with points yet!</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Stats Section --}}
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Stats</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @if($sellerLeaderboard)
                <div class="text-center">
                    <div class="bg-orange-100 p-3 rounded-lg mx-auto w-fit mb-2">
                        <i class="fas fa-user-tie text-orange-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-orange-600">{{ $sellerLeaderboard->count() }}</p>
                    <p class="text-sm text-gray-600">Active Sellers</p>
                </div>
                @endif
                
                <div class="text-center">
                    <div class="bg-purple-100 p-3 rounded-lg mx-auto w-fit mb-2">
                        <i class="fas fa-user-friends text-purple-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-purple-600">{{ $buyerLeaderboard->count() }}</p>
                    <p class="text-sm text-gray-600">Active Buyers</p>
                </div>

                @if($sellerLeaderboard)
                <div class="text-center">
                    <div class="bg-green-100 p-3 rounded-lg mx-auto w-fit mb-2">
                        <i class="fas fa-coins text-green-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-green-600">{{ $sellerLeaderboard->first()?->seller_points_sum_hsp_points_earned ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Top Seller Points</p>
                </div>
                @endif
                
                <div class="text-center">
                    <div class="bg-blue-100 p-3 rounded-lg mx-auto w-fit mb-2">
                        <i class="fas fa-gift text-blue-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-blue-600">{{ $buyerLeaderboard->first()?->buyer_points_sum_hbp_points_earned ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Top Buyer Points</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection