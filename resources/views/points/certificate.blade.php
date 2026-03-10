@extends('layouts.helper')

@section('title', 'Certificate - ' . $redemption->hcr_certificate_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="text-center mb-8">
            <a href="{{ route('points.dashboard') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Points Dashboard
            </a>
        </div>

        {{-- Certificate Display --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            {{-- Certificate Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 px-8 py-6 text-white text-center">
                <h1 class="text-3xl font-bold mb-2">Certificate of Achievement</h1>
                <p class="text-blue-100">UServe Seller Excellence Program</p>
            </div>

            {{-- Certificate Body --}}
            <div class="p-12 text-center bg-white">
                {{-- UPSI Logo --}}
                <div class="mb-8">
                    <img src="{{ asset('images/upsilogo.png') }}" alt="UPSI Logo" class="h-20 mx-auto">
                </div>

                {{-- Certificate Content --}}
                <div class="mb-8">
                    <h2 class="text-xl text-gray-600 mb-6">This is to certify that</h2>
                    <h3 class="text-4xl font-bold text-gray-900 mb-6 border-b-2 border-gray-300 pb-4 inline-block">
                        {{ $redemption->user->hu_name }}
                    </h3>
                    <p class="text-lg text-gray-700 mb-6 max-w-2xl mx-auto leading-relaxed">
                        has successfully completed <strong>3 sales</strong> in the UServe platform and demonstrated 
                        excellence in providing quality services to the UPSI community.
                    </p>
                </div>

                {{-- Certificate Details --}}
                <div class="border-t border-gray-200 pt-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Certificate Number</p>
                            <p class="font-bold text-gray-900">{{ $redemption->hcr_certificate_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Issue Date</p>
                            <p class="font-bold text-gray-900">
                                {{ $redemption->hcr_issued_at ? $redemption->hcr_issued_at->format('F j, Y') : 'Pending' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Achieved Status</p>
                            @if ($redemption->hcr_status === 'issued')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Issued
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-medium">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ ucfirst($redemption->hcr_status) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">User ID</p>
                            <p class="font-bold text-gray-900">{{ $redemption->user->hu_student_id ?? $redemption->user->hu_id }}</p>
                        </div>
                    </div>
                </div>

                {{-- Signature Section --}}
                @if ($redemption->hcr_status === 'issued')
                    <div class="border-t border-gray-200 pt-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-lg mx-auto">
                            <div class="text-center border-t-2 border-gray-400 pt-2">
                                <p class="font-semibold text-gray-900">UServe Administrator</p>
                                <p class="text-sm text-gray-600">Digital Certificate</p>
                            </div>
                            <div class="text-center border-t-2 border-gray-400 pt-2">
                                <p class="font-semibold text-gray-900">UPSI Official</p>
                                <p class="text-sm text-gray-600">Authorized Signatory</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Certificate Footer --}}
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <span>Verified by UServe Platform</span>
                    </div>
                    <div class="flex items-center mt-2 sm:mt-0">
                        <i class="fas fa-calendar mr-2"></i>
                        <span>Generated on {{ now()->format('F j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 mt-8">
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
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Certificate Information
            </h3>
            <div class="text-sm text-blue-800">
                <ul class="space-y-2">
                    <li>• This certificate acknowledges your dedication to providing quality services on the UServe platform.</li>
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
        .bg-gray-50 { background: white !important; }
        .shadow-lg { box-shadow: none !important; }
        .border { border: 1px solid #ddd !important; }
        .bg-gradient-to-r { background: #4f46e5 !important; }
        nav, footer, .print\\:hidden { display: none !important; }
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/points-certificate.js') }}"></script>
@endpush
@endsection