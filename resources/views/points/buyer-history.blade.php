@extends('layouts.helper')

@section('title', 'Buyer Points History')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-history text-white text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Buyer Points History</h1>
                        <p class="text-gray-600 mt-1 text-sm sm:text-base">Complete transaction history for your buyer points</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('points.buyer.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
            {{-- Total Points Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-coins text-purple-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Current Balance</p>
                        <p class="text-2xl sm:text-3xl font-bold text-purple-600">{{ auth()->user()->getTotalBuyerPoints() }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Earned Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-plus text-green-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Earned</p>
                        <p class="text-2xl sm:text-3xl font-bold text-green-600">
                            {{ auth()->user()->buyerPoints()->where('hbp_status', 'earned')->where('hbp_points_earned', '>', 0)->sum('hbp_points_earned') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Total Spent Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-minus text-red-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Spent</p>
                        <p class="text-2xl sm:text-3xl font-bold text-red-600">
                            {{ abs(auth()->user()->buyerPoints()->where('hbp_status', 'earned')->where('hbp_points_earned', '<', 0)->sum('hbp_points_earned')) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction History --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Transaction History</h3>
                <p class="text-gray-600 mt-1 text-sm">Showing {{ $buyerPointsHistory->count() }} of {{ $buyerPointsHistory->total() }} transactions</p>
            </div>

            @if($buyerPointsHistory->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($buyerPointsHistory as $point)
                        <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                                <div class="flex items-start space-x-3 sm:space-x-4">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($point->hbp_points_earned > 0)
                                            <div class="bg-green-100 p-1.5 rounded-full">
                                                <i class="fas fa-plus text-green-600 text-sm"></i>
                                            </div>
                                        @else
                                            <div class="bg-red-100 p-1.5 rounded-full">
                                                <i class="fas fa-minus text-red-600 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 mb-1">
                                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $point->hbp_description }}</h4>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @if($point->hbp_status === 'earned') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($point->hbp_status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="text-xs text-gray-500 space-y-1">
                                            <p><i class="fas fa-calendar-alt mr-1"></i>{{ $point->created_at->format('M d, Y \a\t g:i A') }}</p>
                                            @if($point->serviceRequest && $point->serviceRequest->studentService)
                                                <p><i class="fas fa-link mr-1"></i>
                                                    <a href="{{ route('service-requests.show', $point->serviceRequest->hsr_id) }}" 
                                                       class="text-blue-600 hover:text-blue-800 hover:underline">
                                                        Related to: {{ $point->serviceRequest->studentService->hss_title }}
                                                    </a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <span class="text-lg font-bold 
                                        @if($point->hbp_points_earned > 0) text-green-600
                                        @else text-red-600
                                        @endif">
                                        @if($point->hbp_points_earned > 0)
                                            +{{ $point->hbp_points_earned }}
                                        @else
                                            {{ $point->hbp_points_earned }}
                                        @endif
                                    </span>
                                    <p class="text-xs text-gray-500">points</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="p-4 sm:p-6 border-t border-gray-200">
                    {{ $buyerPointsHistory->links() }}
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-history text-4xl mb-4 text-gray-300"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No transaction history yet</h3>
                    <p class="text-sm">Start requesting services to earn your first buyer points!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection