@extends('layouts.helper')

@section('title', 'Certificate - ' . $redemption->hcr_certificate_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8 certificate-print-wrapper">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 certificate-print-content">
        {{-- Header Section --}}
        <div class="text-center mb-8 no-print">
            <a href="{{ route('points.dashboard') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Points Dashboard
            </a>
        </div>

        {{-- Certificate Display --}}
        <div class="certificate-container">
            <div class="certificate-header">
                <h1 class="certificate-title">Certificate of Achievement</h1>
                <p class="certificate-subtitle">UPSI2u Seller Excellence Program</p>
            </div>

            <div class="certificate-body">
                <img src="{{ asset('images/upsilogo.png') }}" alt="UPSI Logo" class="certificate-logo">

                <p class="certificate-to">This is to certify that</p>
                <h2 class="certificate-name">{{ $redemption->user->hu_name }}</h2>

                <p class="certificate-description">
                    has successfully completed <strong>3 sales</strong> in the UPSI2u platform and demonstrated
                    excellence in providing quality services to the UPSI community.
                </p>

                <div class="certificate-details">
                    <div class="certificate-detail-grid">
                        <div class="certificate-detail-item">
                            <p class="certificate-detail-label">Certificate Number</p>
                            <p class="certificate-detail-value">{{ $redemption->hcr_certificate_number }}</p>
                        </div>
                        <div class="certificate-detail-item">
                            <p class="certificate-detail-label">Issue Date</p>
                            <p class="certificate-detail-value">
                                {{ $redemption->hcr_issued_at ? $redemption->hcr_issued_at->format('F j, Y') : 'Pending' }}
                            </p>
                        </div>
                        <div class="certificate-detail-item">
                            <p class="certificate-detail-label">Status</p>
                            <span class="certificate-status {{ $redemption->hcr_status }}">
                                {{ ucfirst($redemption->hcr_status) }}
                            </span>
                        </div>
                        <div class="certificate-detail-item">
                            <p class="certificate-detail-label">User ID</p>
                            <p class="certificate-detail-value">{{ $redemption->user->hu_student_id ?? $redemption->user->hu_id }}</p>
                        </div>
                    </div>
                </div>

                @if ($redemption->hcr_status === 'issued')
                    <div class="certificate-signatures">
                        <div class="certificate-signature-grid">
                            <div class="certificate-signature">
                                <p class="certificate-signature-name">UPSI2u Administrator</p>
                                <p class="certificate-signature-title">Digital Certificate</p>
                            </div>
                            <div class="certificate-signature">
                                <p class="certificate-signature-name">UPSI Official</p>
                                <p class="certificate-signature-title">Authorized Signatory</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="certificate-footer">
                <div class="certificate-footer-content">
                    <div class="certificate-footer-item">
                        <span class="certificate-footer-icon">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <span>Verified by UPSI2u Platform</span>
                    </div>
                    <div class="certificate-footer-item">
                        <span class="certificate-footer-icon">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <span>Generated on {{ now()->format('F j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 mt-8 no-print">
            @if ($redemption->hcr_status === 'issued')
                <button type="button" data-certificate-print
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                    <i class="fas fa-print mr-2"></i>
                    Print Certificate
                </button>
                <button type="button" data-certificate-download
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                    <i class="fas fa-download mr-2"></i>
                    Download PDF
                </button>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <i class="fas fa-clock text-yellow-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-yellow-800 mb-2">Certificate Pending</h3>
                    <p class="text-yellow-700 text-sm">Your certificate is being processed and will be available soon.</p>
                </div>
            @endif
        </div>

        {{-- Additional Information --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8 no-print">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Certificate Information
            </h3>
            <div class="text-sm text-blue-800">
                <ul class="space-y-2">
                    <li>• This certificate acknowledges your dedication to providing quality services on the UPSI2u platform.</li>
                    <li>• Certificates are issued after successfully completing 3 verified sales transactions.</li>
                    <li>• This digital certificate is officially recognized by UPSI and can be used for portfolio purposes.</li>
                    <li>• You can continue earning more certificates as you complete additional sales milestones.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Print Styles --}}
@push('styles')
<link href="{{ asset('css/certificate.css') }}" rel="stylesheet">
<style>
    @media print {
        html, body {
            background: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* The helper layout renders navbar BEFORE <main>. Hide everything except <main>. */
        body > .min-h-screen > :not(main) {
            display: none !important;
        }

        body > .min-h-screen,
        body > .min-h-screen > main {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Print area should not force extra height/padding */
        .certificate-print-wrapper {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: auto !important;
        }

        .certificate-print-content {
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Only the certificate should print */
        .no-print { display: none !important; }

        .certificate-container {
            break-inside: avoid-page !important;
            page-break-inside: avoid !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/points-certificate.js') }}"></script>
@endpush
@endsection