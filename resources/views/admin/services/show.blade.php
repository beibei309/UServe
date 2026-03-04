@extends('admin.layout')

@section('content')

<div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                Service Details
            </h1>
            <p class="mt-1 font-medium transition-colors duration-300" style="color: var(--text-secondary);">
                Review and manage service details, approval status, and administrative actions.
            </p>
        </div>

        <a href="{{ route('admin.services.index') }}"
           class="bg-gradient-to-r from-slate-500 to-slate-700 hover:from-slate-400 hover:to-slate-600 text-white px-6 py-3 rounded-xl transition-all duration-300 whitespace-nowrap shadow-lg hover:shadow-xl">
            ← Back to Services
        </a>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="rounded-2xl shadow-xl border transition-all duration-300 overflow-hidden"
         style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%); border-color: var(--border-color);">

        {{-- SERVICE OVERVIEW --}}
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- IMAGE SECTION --}}
                <div class="relative">
                    <div class="aspect-video rounded-xl overflow-hidden border transition-colors duration-300" style="border-color: var(--border-color); background-color: var(--bg-primary);">
                        @if ($service->hss_image_path)
                            @php
                                $path = $service->hss_image_path;
                                // 1. Check if external URL
                                if (Str::startsWith($path, ['http://', 'https://'])) {
                                    $imageUrl = $path;
                                } 
                                // 2. Check if file exists in 'storage' (public/storage/...)
                                elseif (file_exists(public_path('storage/' . $path))) {
                                    $imageUrl = asset('storage/' . $path);
                                } 
                                // 3. Fallback: Assume it's in public root (public/...)
                                else {
                                    $imageUrl = asset($path);
                                }
                            @endphp
                            <img src="{{ $imageUrl }}" 
                                alt="{{ $service->hss_title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full transition-colors duration-300" style="color: var(--text-muted);">
                                <div class="text-center">
                                    <i class="fas fa-image text-4xl mb-2 opacity-50"></i>
                                    <p>No Image</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SERVICE INFO --}}
                <div class="space-y-4">
                    <div>
                        <h2 class="text-2xl font-bold transition-colors duration-300 mb-2" style="color: var(--text-primary);">
                            {{ $service->hss_title }}
                        </h2>

                        {{-- Category --}}
                        @if($service->category)
                            <div class="mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white shadow-lg"
                                      style="background: {{ $service->category->hc_color }}">
                                    {{ $service->category->hc_name }}
                                </span>
                            </div>
                        @endif

                        {{-- Status Badge --}}
                        <div class="mb-4">
                            @if ($service->hss_approval_status === 'approved')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-2"></i>Approved
                                </span>
                            @elseif($service->hss_approval_status === 'rejected')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-times-circle mr-2"></i>Rejected
                                </span>
                            @elseif($service->hss_approval_status === 'suspended')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                    <i class="fas fa-ban mr-2"></i>Suspended
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <i class="fas fa-clock mr-2"></i>Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Service Statistics --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl border transition-colors duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Provider</div>
                            <div class="text-lg font-semibold transition-colors duration-300 truncate" style="color: var(--text-primary);">{{ $service->user->hu_name ?? 'Unknown' }}</div>
                        </div>
                        
                        <div class="p-4 rounded-xl border transition-colors duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Rating</div>
                            <div class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">
                                {{ number_format($service->reviews_avg_rating ?? 0,1) }} ⭐ ({{ $service->reviews_count }})
                            </div>
                        </div>
                        
                        <div class="p-4 rounded-xl border transition-colors duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Warnings</div>
                            <div class="text-lg font-semibold {{ ($service->hss_warning_count ?? 0) >= 2 ? 'text-red-500' : '' }}" style="color: {{ ($service->hss_warning_count ?? 0) >= 2 ? '#ef4444' : 'var(--text-primary)' }};">
                                {{ $service->hss_warning_count ?? 0 }}/3
                            </div>
                        </div>
                        
                        <div class="p-4 rounded-xl border transition-colors duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-secondary);">Created</div>
                            <div class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">{{ $service->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- DESCRIPTION SECTION --}}
        <div class="border-t p-6" style="border-color: var(--border-color);">
            <h3 class="text-lg font-bold transition-colors duration-300 mb-4" style="color: var(--text-primary);">
                <i class="fas fa-file-text mr-2 text-blue-500"></i>Service Description
            </h3>
            <div class="rounded-xl border p-6 transition-colors duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                <div class="prose max-w-none transition-colors duration-300" style="color: var(--text-primary);">
                    {!! $service->hss_description !!}
                </div>
            </div>
        </div>

        {{-- PRICING PACKAGES SECTION --}}
        <div class="border-t p-6" style="border-color: var(--border-color);">
            <h3 class="text-lg font-bold transition-colors duration-300 mb-4" style="color: var(--text-primary);">
                <i class="fas fa-tags mr-2 text-green-500"></i>Service Packages
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- BASIC PACKAGE --}}
                @if($service->hss_basic_price)
                <div class="group relative rounded-xl border p-6 transition-all duration-300 hover:shadow-lg"
                     style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-color: var(--border-color);">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300 rounded-xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-blue-800 uppercase tracking-wide">Basic Package</h4>
                            <i class="fas fa-box text-blue-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-blue-900 mb-3">RM {{ number_format($service->hss_basic_price, 2) }}</div>
                        <div class="text-sm text-blue-700 leading-relaxed">
                            {!! $service->hss_basic_description ?: '<em>No description provided</em>' !!}
                        </div>
                    </div>
                </div>
                @endif

                {{-- STANDARD PACKAGE --}}
                @if($service->hss_standard_price)
                <div class="group relative rounded-xl border p-6 transition-all duration-300 hover:shadow-lg"
                     style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-color: var(--border-color);">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500 to-yellow-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300 rounded-xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-yellow-800 uppercase tracking-wide">Standard Package</h4>
                            <i class="fas fa-cube text-yellow-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-yellow-900 mb-3">RM {{ number_format($service->hss_standard_price, 2) }}</div>
                        <div class="text-sm text-yellow-700 leading-relaxed">
                            {!! $service->hss_standard_description ?: '<em>No description provided</em>' !!}
                        </div>
                    </div>
                </div>
                @endif

                {{-- PREMIUM PACKAGE --}}
                @if($service->hss_premium_price)
                <div class="group relative rounded-xl border p-6 transition-all duration-300 hover:shadow-lg"
                     style="background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); border-color: var(--border-color);">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-purple-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300 rounded-xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-purple-800 uppercase tracking-wide">Premium Package</h4>
                            <i class="fas fa-crown text-purple-600"></i>
                        </div>
                        <div class="text-2xl font-bold text-purple-900 mb-3">RM {{ number_format($service->hss_premium_price, 2) }}</div>
                        <div class="text-sm text-purple-700 leading-relaxed">
                            {!! $service->hss_premium_description ?: '<em>No description provided</em>' !!}
                        </div>
                    </div>
                </div>
                @endif

            </div>

            @if(!$service->hss_basic_price && !$service->hss_standard_price && !$service->hss_premium_price)
                <div class="text-center py-12 rounded-xl border transition-colors duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                    <i class="fas fa-exclamation-circle text-4xl mb-4 opacity-50" style="color: var(--text-muted);"></i>
                    <p class="text-lg font-medium transition-colors duration-300" style="color: var(--text-secondary);">No packages have been defined for this service</p>
                </div>
            @endif
        </div>

        {{-- ADMINISTRATIVE ACTIONS --}}
        <div class="border-t p-6" style="border-color: var(--border-color);">
            <h3 class="text-lg font-bold transition-colors duration-300 mb-6" style="color: var(--text-primary);">
                <i class="fas fa-cog mr-2 text-indigo-500"></i>Administrative Actions
            </h3>
            
            <div class="space-y-6">
                
                {{-- Quick Actions Row --}}
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.services.reviews', $service->hss_id) }}" 
                       class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                        <i class="fas fa-star"></i>
                        View Reviews ({{ $service->reviews_count ?? 0 }})
                    </a>
                </div>

                {{-- Status-Based Actions --}}
                @if ($service->hss_approval_status === 'pending')
                    <div class="rounded-xl border p-6" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.1) 100%); border-color: #f59e0b;">
                        <h4 class="font-semibold text-yellow-800 mb-4 flex items-center">
                            <i class="fas fa-clock mr-2"></i>Pending Service Review
                        </h4>
                        <p class="text-sm text-yellow-700 mb-4">This service requires administrative review. You can approve, reject, or issue a warning.</p>
                        <div class="flex flex-wrap gap-3">
                            <form action="{{ route('admin.services.approve', $service->hss_id) }}" method="POST" class="inline-block">
                                @csrf @method('PATCH')
                                <button type="submit" 
                                        class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center gap-2"
                                        onclick="return confirm('Are you sure you want to approve this service?')">
                                    <i class="fas fa-check"></i>Approve Service
                                </button>
                            </form>
                            
                            <button onclick="openRejectModal('{{ route('admin.services.reject', $service->hss_id) }}')" 
                                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                                <i class="fas fa-times"></i>Reject Service
                            </button>
                            
                            <button onclick="openWarningModal('{{ route('admin.services.warn', $service->hss_id) }}')" 
                                    class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i>Issue Warning
                            </button>
                        </div>
                    </div>
                @endif

                @if ($service->hss_approval_status === 'approved')
                    <div class="rounded-xl border p-6" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(74, 222, 128, 0.1) 100%); border-color: #22c55e;">
                        <h4 class="font-semibold text-green-800 mb-4 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>Approved Service Management
                        </h4>
                        <p class="text-sm text-green-700 mb-4">This service is currently approved and active. You can issue warnings if needed.</p>
                        <div class="flex flex-wrap gap-3">
                            <button onclick="openWarningModal('{{ route('admin.services.warn', $service->hss_id) }}')" 
                                    class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i>Issue Warning
                            </button>
                        </div>
                    </div>
                @endif

                @if ($service->hss_approval_status === 'rejected')
                    <div class="rounded-xl border p-6" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(248, 113, 113, 0.1) 100%); border-color: #ef4444;">
                        <h4 class="font-semibold text-red-800 mb-4 flex items-center">
                            <i class="fas fa-times-circle mr-2"></i>Rejected Service
                        </h4>
                        <p class="text-sm text-red-700 mb-4">This service was previously rejected. You can reconsider and approve if appropriate.</p>
                        <div class="flex flex-wrap gap-3">
                            <form action="{{ route('admin.services.approve', $service->hss_id) }}" method="POST" class="inline-block">
                                @csrf @method('PATCH')
                                <button type="submit" 
                                        class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center gap-2"
                                        onclick="return confirm('Are you sure you want to approve this previously rejected service?')">
                                    <i class="fas fa-redo"></i>Reconsider & Approve
                                </button>
                            </form>
                        </div>
                        
                        @if($service->hss_reject_reason)
                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <h5 class="font-medium text-red-800 mb-2">Rejection Reason:</h5>
                                <p class="text-sm text-red-700">{{ $service->hss_reject_reason }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($service->hss_approval_status === 'suspended')
                    <div class="rounded-xl border p-6" style="background: linear-gradient(135deg, rgba(107, 114, 128, 0.1) 0%, rgba(156, 163, 175, 0.1) 100%); border-color: #6b7280;">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-ban mr-2"></i>Suspended Service
                        </h4>
                        <p class="text-sm text-gray-700 mb-4">This service has been suspended due to policy violations or warnings. You can reactivate it if appropriate.</p>
                        <div class="flex flex-wrap gap-3">
                            <button onclick="confirmUnblock('{{ route('admin.services.unblock', $service->hss_id) }}')" 
                                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                                <i class="fas fa-unlock"></i>Reactivate Service
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Critical Actions --}}
                @if (($service->hss_warning_count ?? 0) >= 3 && $service->hss_approval_status !== 'suspended')
                    <div class="rounded-xl border-2 border-red-300 p-6" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(248, 113, 113, 0.15) 100%);">
                        <h4 class="font-semibold text-red-800 mb-4 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Maximum Warnings Reached
                        </h4>
                        <p class="text-sm text-red-700 mb-4">This service has reached the maximum number of warnings (3/3). It should be suspended.</p>
                        <div class="flex flex-wrap gap-3">
                            <button onclick="confirmSuspend('{{ route('admin.services.suspend', $service->hss_id) }}')" 
                                    class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center gap-2">
                                <i class="fas fa-ban"></i>Suspend Service
                            </button>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>

</div>

{{-- REJECT REASON MODAL --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRejectModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border"
             style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <form id="rejectForm" method="POST" action="">
                @csrf 
                @method('PATCH')
                
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-red-100">
                            <i class="fas fa-times text-red-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold transition-colors duration-300 mb-2" style="color: var(--text-primary);" id="modal-title">
                                Reject Service
                            </h3>
                            <p class="text-sm transition-colors duration-300 mb-4" style="color: var(--text-secondary);">
                                Please provide a clear reason for rejecting this service. This will be shown to the student.
                            </p>
                            <textarea name="reject_reason" rows="4" 
                                class="w-full rounded-xl border-2 p-4 transition-all duration-300 focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);" 
                                placeholder="e.g. Inappropriate images, Description too short, Policy violation..." required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="border-t px-6 py-4 flex gap-3 justify-end" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <button type="button" onclick="closeRejectModal()" 
                            class="px-6 py-3 rounded-lg border transition-all duration-300 font-medium"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white px-6 py-3 rounded-lg transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                        Reject Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- WARNING MODAL --}}
<div id="warningModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="warning-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeWarningModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border"
             style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <form id="warningForm" method="POST" action="">
                @csrf 
                @method('PATCH')
                
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-orange-100">
                            <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold transition-colors duration-300 mb-2" style="color: var(--text-primary);" id="warning-modal-title">
                                Issue Warning
                            </h3>
                            <p class="text-sm transition-colors duration-300 mb-4" style="color: var(--text-secondary);">
                                Provide a clear explanation for this warning. The student will be notified via email.
                            </p>
                            <textarea name="warning_reason" rows="4" 
                                class="w-full rounded-xl border-2 p-4 transition-all duration-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);" 
                                placeholder="e.g. Late response to customer, Quality issues, Policy reminder..." required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="border-t px-6 py-4 flex gap-3 justify-end" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <button type="button" onclick="closeWarningModal()" 
                            class="px-6 py-3 rounded-lg border transition-all duration-300 font-medium"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white px-6 py-3 rounded-lg transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                        Issue Warning
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Warning Modal Functions
    function openWarningModal(url) {
        document.getElementById('warningForm').action = url;
        document.getElementById('warningModal').classList.remove('hidden');
    }

    function closeWarningModal() {
        document.getElementById('warningModal').classList.add('hidden');
    }

    // Reject Modal Functions
    function openRejectModal(url) {
        document.getElementById('rejectForm').action = url;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Suspend Confirmation with Modern SweetAlert
    function confirmSuspend(url) {
        Swal.fire({
            title: 'Suspend Service?',
            text: "This service will be suspended due to reaching maximum warnings (3/3). This action can be undone later.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Suspend Service',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg',
                cancelButton: 'rounded-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                let token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';
                form.appendChild(token);
                
                let methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Unblock/Reactivate Confirmation
    function confirmUnblock(url) {
        Swal.fire({
            title: 'Reactivate Service?',
            text: "This service will be reactivated and become available again to students.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reactivate',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg',
                cancelButton: 'rounded-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                let token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';
                form.appendChild(token);
                
                let methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Modern Success/Error Messages
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "{{ session('error') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    @endif

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRejectModal();
            closeWarningModal();
        }
    });
</script>

@endsection
