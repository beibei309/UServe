@extends('layouts.helper')

@section('title', 'Points History')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-purple-400 to-blue-500 p-3 rounded-xl">
                        <i class="fas fa-history text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Points History</h1>
                        <p class="text-gray-600 mt-1">Complete history of all your points transactions</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('points.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Points History List --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">All Transactions</h3>
            </div>

            @if ($pointsHistory->count() > 0)
                <div class="overflow-hidden">
                    @foreach ($pointsHistory as $point)
                        <div class="border-b border-gray-100 last:border-b-0">
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if ($point->hsp_points_earned > 0)
                                                <div class="bg-green-100 p-3 rounded-full">
                                                    <i class="fas fa-plus text-green-600"></i>
                                                </div>
                                            @else
                                                <div class="bg-red-100 p-3 rounded-full">
                                                    <i class="fas fa-minus text-red-600"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $point->hsp_description }}
                                            </p>
                                            <div class="flex items-center space-x-4 mt-1 text-sm text-gray-600">
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    {{ $point->created_at->format('M j, Y \a\t g:i A') }}
                                                </span>
                                                @if ($point->serviceRequest)
                                                    <span class="flex items-center">
                                                        <i class="fas fa-briefcase mr-1"></i>
                                                        {{ $point->serviceRequest->studentService->hss_title ?? 'Service' }}
                                                    </span>
                                                @endif
                                                <span class="flex items-center">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    Status: {{ ucfirst($point->hsp_status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            @if ($point->hsp_points_earned > 0)
                                                <div class="flex items-center text-green-600">
                                                    <span class="text-xl font-bold">+{{ $point->hsp_points_earned }}</span>
                                                    <i class="fas fa-coins ml-2"></i>
                                                </div>
                                                <p class="text-xs text-green-500 mt-1">Points Earned</p>
                                            @else
                                                <div class="flex items-center text-red-600">
                                                    <span class="text-xl font-bold">{{ $point->hsp_points_earned }}</span>
                                                    <i class="fas fa-coins ml-2"></i>
                                                </div>
                                                <p class="text-xs text-red-500 mt-1">Points Used</p>
                                            @endif
                                        </div>
                                        @if ($point->serviceRequest)
                                            <div class="hidden sm:block">
                                                <a href="{{ route('service-requests.show', $point->serviceRequest) }}" 
                                                   class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                                    View Order
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $pointsHistory->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Points History</h3>
                    <p class="text-gray-600 mb-6">You haven't earned or used any points yet.</p>
                    <a href="{{ route('services.manage') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your First Service
                    </a>
                </div>
            @endif
        </div>

        {{-- Summary Statistics --}}
        @if ($pointsHistory->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-xl">
                            <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Earned</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ $pointsHistory->where('hsp_points_earned', '>', 0)->sum('hsp_points_earned') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-xl">
                            <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Used</p>
                            <p class="text-2xl font-bold text-red-600">
                                {{ abs($pointsHistory->where('hsp_points_earned', '<', 0)->sum('hsp_points_earned')) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-xl">
                            <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Current Balance</p>
                            <p class="text-2xl font-bold text-blue-600">
                                {{ $pointsHistory->sum('hsp_points_earned') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Points Styling Component --}}
@push('styles')
<link href="{{ asset('css/points-dashboard.css') }}" rel="stylesheet">
@endpush
@endsection