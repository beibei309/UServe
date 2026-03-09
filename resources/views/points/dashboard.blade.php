@extends('layouts.helper')

@section('title', 'Seller Points Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-coins text-white text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Seller Points Dashboard</h1>
                        <p class="text-gray-600 mt-1 text-sm sm:text-base">Track your points and redeem certificates</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    <a href="{{ route('points.history') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center">
                        <i class="fas fa-history mr-2"></i>
                        View History
                    </a>
                    <a href="{{ route('points.leaderboard') }}"
                       class="inline-flex items-center px-4 py-2 border border-orange-300 rounded-lg text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100 transition-colors w-full sm:w-auto justify-center">
                        <i class="fas fa-trophy mr-2"></i>
                        Leaderboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Points Overview Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
            {{-- Total Points Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-star text-blue-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Points</p>
                        <p class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $totalPoints }}</p>
                    </div>
                </div>
            </div>

            {{-- Points Needed Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-target text-orange-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Points to Certificate</p>
                        <p class="text-2xl sm:text-3xl font-bold text-orange-600">{{ $pointsNeededForCertificate }}</p>
                    </div>
                </div>
            </div>

            {{-- Certificates Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 sm:p-3 rounded-xl flex-shrink-0">
                        <i class="fas fa-certificate text-green-600 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Certificates</p>
                        <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ $certificates->where('hcr_status', 'issued')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Progress to Certificate Achievement</h3>
                <span class="text-sm sm:text-base text-gray-600 font-medium">{{ $totalPoints }}/1 points</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 sm:h-5 mb-4 overflow-hidden">
                @php
                    $progressPercentage = ($totalPoints / 1) * 100;
                    // Ensure minimum 15% width for visibility when points > 0
                    $displayWidth = $totalPoints > 0 ? max(15, $progressPercentage) : 0;
                @endphp
                <div class="h-full rounded-full transition-all duration-500"
                     style="width: {{ $displayWidth }}%; background: linear-gradient(to right, #10b981, #059669);">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                @if ($hasCertificateAchievement)
                    <p class="text-green-600 font-medium text-sm sm:text-base">🏆 Achievement Unlocked! Certificate earned</p>
                @elseif ($canRedeemCertificate)
                    <p class="text-green-600 font-medium text-sm sm:text-base">🎉 Congratulations! You can unlock your certificate achievement</p>
                @else
                    <p class="text-gray-600 text-sm sm:text-base">Complete {{ $pointsNeededForCertificate }} more sale to unlock the certificate achievement!</p>
                @endif

                <div class="w-full sm:w-auto mt-4 sm:mt-0">
                    @if ($hasCertificateAchievement)
                        <div class="w-full sm:w-auto" style="display: block; width: 100%;">
                            <button disabled
                                    style="display: block !important; visibility: visible !important; opacity: 1 !important; background: #10b981 !important; color: white !important; padding: 16px 24px !important; min-height: 50px !important; width: 100% !important; border: none !important; border-radius: 8px !important; font-weight: 500 !important; cursor: default !important;"
                                    class="transition-all duration-200">
                                <i class="fas fa-trophy mr-2"></i>
                                <span>Certificate Achievement Earned! 🏆</span>
                            </button>
                        </div>
                    @elseif ($canRedeemCertificate)
                        <button type="button"
                                onclick="redeemCertificate()"
                                style="display: block !important; visibility: visible !important; opacity: 1 !important; background: linear-gradient(to right, #059669, #047857) !important; color: white !important; padding: 16px 24px !important; min-height: 50px !important; width: 100% !important; border: none !important; border-radius: 8px !important; font-weight: 500 !important;"
                                class="hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-trophy mr-2"></i>
                            <span>Unlock Certificate Achievement ({{ $totalPoints }}/1)</span>
                        </button>
                    @else
                        <div class="w-full sm:w-auto" style="display: block; width: 100%;">
                            <button disabled
                                    style="display: block !important; visibility: visible !important; opacity: 1 !important; background: #d1d5db !important; color: #6b7280 !important; padding: 16px 24px !important; min-height: 50px !important; width: 100% !important; border: none !important; border-radius: 8px !important; font-weight: 500 !important; cursor: not-allowed !important;"
                                    class="transition-all duration-200">
                                <i class="fas fa-lock mr-2"></i>
                                <span>Need {{ $pointsNeededForCertificate }} More Point ({{ $totalPoints }}/1)</span>
                            </button>
                        </div>
                    @endif
                </div>

<script>
function redeemCertificate() {
    Swal.fire({
        title: '� Unlock Certificate Achievement?',
        text: 'Congratulations! You have earned enough points to unlock your certificate achievement.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Unlock Achievement!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Processing...',
                text: 'Unlocking your certificate achievement',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX request
            fetch('{{ route("points.redeem.ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '🏆 Achievement Unlocked!',
                        html: `<p>Congratulations! You have unlocked your certificate achievement!</p>
                               <p><strong>Certificate Number:</strong> ${data.certificate_number}</p>`,
                        icon: 'success',
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'View Certificate'
                    }).then(() => {
                        // Redirect to certificate or refresh page
                        window.location.href = data.certificate_url || window.location.href;
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'An error occurred while unlocking your achievement.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    });
}
</script>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Recent Points Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Points Earned</h3>
                </div>
                <div class="p-6">
                    @if ($recentPoints->count() > 0)
                        <div class="space-y-4">
                            @foreach ($recentPoints as $point)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-green-100 p-2 rounded-full">
                                            <i class="fas fa-plus text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $point->hsp_description }}</p>
                                            <p class="text-sm text-gray-600">{{ $point->created_at->format('M j, Y \a\t g:i A') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center space-x-2">
                                            @if ($point->hsp_points_earned > 0)
                                                <span class="text-green-600 font-bold">+{{ $point->hsp_points_earned }}</span>
                                            @else
                                                <span class="text-red-600 font-bold">{{ $point->hsp_points_earned }}</span>
                                            @endif
                                            <i class="fas fa-coins text-yellow-500"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 text-center">
                            <a href="{{ route('points.history') }}"
                               class="text-blue-600 hover:text-blue-700 font-medium">
                                View Full History →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-coins text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">No points earned yet</p>
                            <p class="text-sm text-gray-400 mt-2">Complete sales to start earning points!</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Certificates Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">My Certificates</h3>
                </div>
                <div class="p-6">
                    @if ($certificates->count() > 0)
                        <div class="space-y-4">
                            @foreach ($certificates as $certificate)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-full {{ $certificate->hcr_status === 'issued' ? 'bg-green-100' : 'bg-yellow-100' }}">
                                            <i class="fas fa-certificate {{ $certificate->hcr_status === 'issued' ? 'text-green-600' : 'text-yellow-600' }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $certificate->hcr_certificate_number }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ $certificate->hcr_status === 'issued' ? 'Issued' : 'Pending' }} •
                                                {{ $certificate->created_at->format('M j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if ($certificate->hcr_status === 'issued')
                                            <a href="{{ route('points.certificate', $certificate) }}"
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                                View
                                            </a>
                                        @elseif ($certificate->hcr_status === 'pending')
                                            <form action="{{ route('points.cancel-redemption', $certificate) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors"
                                                        onclick="return confirm('Are you sure you want to cancel this redemption? Points will be refunded.')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-certificate text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">No certificates yet</p>
                            <p class="text-sm text-gray-400 mt-2">Earn 3 points to redeem your first certificate!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- How It Works Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-8 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">How Points Work</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-handshake text-blue-600 text-xl"></i>
                    </div>
                    <h4 class="font-medium text-gray-900 mb-2">Complete Sales</h4>
                    <p class="text-sm text-gray-600">Earn 1 point for each successfully completed service</p>
                </div>
                <div class="text-center">
                    <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trophy text-orange-600 text-xl"></i>
                    </div>
                    <h4 class="font-medium text-gray-900 mb-2">Collect Points</h4>
                    <p class="text-sm text-gray-600">Accumulate points over time with each successful service</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-gift text-green-600 text-xl"></i>
                    </div>
                    <h4 class="font-medium text-gray-900 mb-2">Redeem Certificate</h4>
                    <p class="text-sm text-gray-600">Use 1 point to redeem an official certificate from us</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Points Styling Component --}}
@push('styles')
<link href="{{ asset('css/points-dashboard.css') }}" rel="stylesheet">
@endpush
@endsection
