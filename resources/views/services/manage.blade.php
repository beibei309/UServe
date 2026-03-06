@extends('layouts.helper')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Service Portfolio</h1>
                    <p class="text-gray-500 mt-2">Manage your listings and check approval status.</p>
                </div>

                <a href="{{ route('services.create') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New Service
                </a>
            </div>

            {{-- Tabs --}}
            <div class="mb-8 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @foreach (['all' => 'All Services', 'pending' => 'Pending Approval', 'approved' => 'Live / Approved', 'rejected' => 'Rejected'] as $key => $label)
                        <button data-tab="{{ $key }}"
                            class="tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 {{ $key === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- Grid Content --}}
            <div id="tab-contents">
                @foreach (['all', 'pending', 'approved', 'rejected'] as $status)
                    <div id="{{ $status }}-tab-content"
                        class="tab-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 {{ $status !== 'all' ? 'hidden' : '' }}">
                        @foreach ($servicesByStatus[$status] as $service)
                            <div class="group relative rounded-2xl shadow-sm transition-all duration-300 border border-gray-100 flex flex-col overflow-hidden
                                 {{ $service->ui_is_suspended ? 'opacity-80 pointer-events-none' : 'bg-white hover:shadow-lg' }}"
                                data-service-id="{{ $service->hss_id }}">

                                @if ($service->ui_is_suspended)
                                    <div
                                        class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center text-center z-20">
                                        <div class="text-white text-xl font-extrabold tracking-widest uppercase mb-2">
                                            <i class="fa-solid fa-ban mr-2 text-red-400"></i>
                                            Suspended
                                        </div>
                                        <p class="text-gray-300 text-sm max-w-xs">
                                            Your service has been suspended by admin. Please contact support for more
                                            information.
                                        </p>
                                    </div>
                                @endif


                                {{-- Hidden Data for Modal --}}
                                <div class="hidden" id="data-desc-{{ $service->hss_id }}">{!! $service->hss_description !!}</div>
                                <div class="hidden" id="data-pkg-basic-desc-{{ $service->hss_id }}">{!! $service->hss_basic_description !!}
                                </div>
                                <div class="hidden" id="data-pkg-standard-desc-{{ $service->hss_id }}">{!! $service->hss_standard_description !!}
                                </div>
                                <div class="hidden" id="data-pkg-premium-desc-{{ $service->hss_id }}">{!! $service->hss_premium_description !!}
                                </div>

                                {{-- Card Image & Overlays --}}
                                <div class="relative h-48 overflow-hidden bg-gray-100">
                                    <img src="{{ $service->ui_image_url }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                        data-fallback-src="https://via.placeholder.com/400x300?text=Service+Image">

                                    {{-- 🟢 UPDATE: Only show badge if NOT approved --}}
                                    @if (strtolower($service->hss_approval_status) !== 'approved')
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm {{ $service->ui_badge_class }}">
                                                {!! $service->ui_badge_icon !!} {{ $service->hss_approval_status }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Card Body --}}
                                <div class="p-5 flex-1 flex flex-col">
                                    {{-- Category --}}
                                    <div class="flex justify-between items-start mb-2">
                                        @if ($service->category)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-sm border"
                                                style="
                background: {{ $service->category->hc_color ?? '#E5E7EB' }};
                color: white;
                border-color: rgba(0,0,0,0.1);
            ">
                                                {{ $service->category->hc_name }}
                                            </span>
                                        @endif
                                    </div>


                                    {{-- Title --}}
                                    <h3
                                        class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 leading-tight group-hover:text-indigo-600 transition-colors">
                                        {{ $service->hss_title }}
                                    </h3>

                                    {{-- 🟢 UPDATE: Booking Status as Solid Bubble --}}
                                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Service
                                            Status</span>

                                        @if ($service->hss_status === 'available')
                                            {{-- Available Bubble --}}
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-sm">
                                                <i class="fa-solid fa-check-circle text-[10px]"></i> Available
                                            </span>
                                        @else
                                            {{-- Unavailable Bubble --}}
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-400 text-white shadow-sm">
                                                <i class="fa-solid fa-ban text-[10px]"></i> Unavailable
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Price Summary --}}
                                    <div class="mb-4">
                                        @if ($service->hss_basic_price)
                                            <p class="text-xs text-gray-400 font-medium uppercase mb-0.5">Starts from</p>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-xl font-extrabold text-gray-900">RM
                                                    {{ number_format($service->hss_basic_price) }}</span>
                                                <span class="text-xs text-gray-500 font-medium">/
                                                    {{ $service->hss_basic_frequency }}</span>
                                            </div>
                                        @else
                                            <span class="text-sm italic text-gray-400">Price not set</span>
                                        @endif
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="mt-auto grid grid-cols-2 gap-3">
                                        <button type="button" data-edit-service="{{ $service->hss_id }}"
                                            class="flex items-center justify-center px-4 py-2.5 bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-100 hover:text-gray-900 transition-all border border-gray-200">
                                            Edit / Status
                                        </button>
                                        <button type="button" data-open-service-modal='@json($service)'
                                            class="flex items-center justify-center px-4 py-2.5 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-xl hover:bg-indigo-100 transition-all border border-indigo-100">
                                            Preview
                                        </button>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <button type="button" data-delete-service="{{ $service->hss_id }}"
                                            class="text-xs text-red-400 hover:text-red-600 hover:underline transition-colors font-medium">
                                            Delete Service
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($servicesByStatus[$status]->isEmpty())
                            <div class="col-span-full py-16 flex flex-col items-center justify-center text-center">
                                <div
                                    class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                    <i class="fa-solid fa-folder-open text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">No services found</h3>
                                <p class="text-gray-500 mt-1 mb-6 max-w-sm mx-auto">It looks empty here. Start by creating
                                    your first service listing.</p>
                                <a href="{{ route('services.create') }}"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg transition-transform hover:-translate-y-0.5">
                                    Create Service
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="serviceModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" data-close-service-modal></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

                    {{-- Modal Header Image --}}
                    <div class="relative h-64 bg-gray-200 group">
                        <img id="modalImage" src="" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                        <button type="button" data-close-service-modal
                            class="absolute top-4 right-4 bg-black/20 hover:bg-black/40 text-white rounded-full p-2 transition backdrop-blur-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <div class="absolute bottom-0 left-0 p-8 w-full">
                            <span id="modalCategory"
                                class="inline-block px-2.5 py-1 mb-3 text-xs font-bold text-white bg-white/20 backdrop-blur-md rounded-lg uppercase tracking-wider border border-white/30"></span>
                            <h3 id="modalTitle" class="text-3xl font-bold text-white leading-tight shadow-sm"></h3>
                        </div>
                    </div>

                    {{-- Modal Content --}}
                    <div class="px-8 py-8 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        <div class="mb-8">
                            <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wide mb-3 flex items-center">
                                <i class="fa-solid fa-align-left mr-2 text-indigo-500"></i> Description
                            </h4>
                            <div id="modalDescription"
                                class="prose prose-sm prose-indigo text-gray-600 leading-relaxed max-w-none"></div>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wide mb-4 flex items-center">
                                <i class="fa-solid fa-tags mr-2 text-indigo-500"></i> Pricing Packages
                            </h4>
                            <div class="grid grid-cols-1 gap-4" id="modalPackagesContainer">
                                {{-- JS Injects here --}}
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse border-t border-gray-100">
                        <button type="button" data-close-service-modal
                            class="w-full inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }

        .prose ul {
            list-style-type: disc;
            padding-left: 1.5em;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .prose ol {
            list-style-type: decimal;
            padding-left: 1.5em;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .prose p {
            margin-bottom: 0.75em;
        }
    </style>

    <div id="servicesManageConfig"
        data-edit-url-template="{{ url('/services/__ID__/edit') }}"
        data-delete-url-template="{{ route('services.destroy', '__ID__') }}"></div>
    @push('scripts')
        <script src="{{ asset('js/nonadmin-services-manage.js') }}"></script>
    @endpush
@endsection
