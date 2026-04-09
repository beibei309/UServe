@extends('admin.layout')

@section('title', 'Certificate - ' . $redemption->hcr_certificate_number)

@section('content')
<div class="p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Certificate Details</h1>
                <p class="mt-1 text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                    View certificate achievement information for this helper
                </p>
            </div>
            <a href="{{ route('admin.rewards.certificates') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200 text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Certificates
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg border transition-colors duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 px-8 py-6 text-white text-center rounded-t-xl">
                <h2 class="text-2xl font-bold mb-1">Certificate of Achievement</h2>
                <p class="text-blue-100 text-sm">UServe Seller Excellence Program</p>
            </div>

            <div class="p-8 md:p-10">
                <div class="text-center mb-8">
                    <p class="text-gray-500 text-sm mb-4">This is to certify that</p>
                    <h3 class="text-3xl font-bold mb-4" style="color: var(--text-primary);">
                        {{ $redemption->user->hu_name ?? 'Unknown User' }}
                    </h3>
                    <p class="text-base md:text-lg leading-relaxed transition-colors duration-300" style="color: var(--text-secondary);">
                        has successfully completed <strong>3 sales</strong> in the UServe platform and demonstrated
                        excellence in providing quality services to the UPSI community.
                    </p>
                </div>

                <div class="border-t pt-6 mt-4 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500 mb-1">Certificate Number</p>
                        <p class="font-semibold" style="color: var(--text-primary);">{{ $redemption->hcr_certificate_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if ($redemption->hcr_status === 'issued') bg-green-100 text-green-800
                            @elseif ($redemption->hcr_status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif ($redemption->hcr_status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($redemption->hcr_status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Issued At</p>
                        <p class="font-semibold" style="color: var(--text-primary);">
                            {{ $redemption->hcr_issued_at ? $redemption->hcr_issued_at->format('F j, Y') : 'Pending' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Created At</p>
                        <p class="font-semibold" style="color: var(--text-primary);">
                            {{ $redemption->created_at?->format('F j, Y H:i') ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Helper Email</p>
                        <p class="font-semibold truncate" style="color: var(--text-primary);">
                            {{ $redemption->user->hu_email ?? $redemption->user->email ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Helper ID</p>
                        <p class="font-semibold" style="color: var(--text-primary);">
                            {{ $redemption->user->hu_student_id ?? $redemption->user->hu_id ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500 mb-1">Notes</p>
                        <p class="font-medium" style="color: var(--text-primary);">
                            {{ $redemption->hcr_notes ?: '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
