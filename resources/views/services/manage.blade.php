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
                @php $statuses = ['all', 'pending', 'approved', 'rejected']; @endphp

                @foreach ($statuses as $status)
                    <div id="{{ $status }}-tab-content"
                        class="tab-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 {{ $status !== 'all' ? 'hidden' : '' }}">

                        @php
                            $filtered = $status === 'all' ? $services : $services->where('hss_approval_status', $status);
                        @endphp

                        @foreach ($filtered as $service)
                            <div class="group relative rounded-2xl shadow-sm transition-all duration-300 border border-gray-100 flex flex-col overflow-hidden
                                 {{ strtolower($service->hss_approval_status) == 'suspended' ? 'opacity-80 pointer-events-none' : 'bg-white hover:shadow-lg' }}"
                                data-service-id="{{ $service->hss_id }}">

                                @if (strtolower($service->hss_approval_status) == 'suspended')
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
                                    @php
                                        $imageUrl = 'https://via.placeholder.com/400x300?text=Service+Image';

                                        if (!empty($service->hss_image_path)) {
                                            $path = $service->hss_image_path;

                                            if (Str::startsWith($path, ['http://', 'https://'])) {
                                                $imageUrl = $path;
                                            } elseif (Str::startsWith($path, 'storage/')) {
                                                $imageUrl = asset($path);
                                            } elseif (Str::startsWith($path, 'services/')) {
                                                $imageUrl = asset('storage/' . $path);
                                            } elseif (file_exists(public_path('storage/' . $path))) {
                                                $imageUrl = asset('storage/' . $path);
                                            } elseif (file_exists(public_path($path))) {
                                                $imageUrl = asset($path);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $imageUrl }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                        onerror="this.src='https://via.placeholder.com/400x300?text=Service+Image'">

                                    {{-- 🟢 UPDATE: Only show badge if NOT approved --}}
                                    @if (strtolower($service->hss_approval_status) !== 'approved')
                                        <div class="absolute top-3 right-3">
                                            @php
                                                $badgeClass = match (strtolower($service->hss_approval_status)) {
                                                    'pending' => 'bg-amber-500 text-white',
                                                    'rejected' => 'bg-red-500 text-white',
                                                    default => 'bg-gray-500 text-white',
                                                };
                                                $icon = match (strtolower($service->hss_approval_status)) {
                                                    'pending' => '<i class="fa-solid fa-clock mr-1"></i>',
                                                    'rejected' => '<i class="fa-solid fa-circle-xmark mr-1"></i>',
                                                    default => '',
                                                };
                                            @endphp
                                            <span
                                                class="backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm {{ $badgeClass }}">
                                                {!! $icon !!} {{ $service->hss_approval_status }}
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
                                        <button onclick="editService({{ $service->hss_id }})"
                                            class="flex items-center justify-center px-4 py-2.5 bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-100 hover:text-gray-900 transition-all border border-gray-200">
                                            Edit / Status
                                        </button>
                                        <button onclick="openServiceModal({{ json_encode($service) }})"
                                            class="flex items-center justify-center px-4 py-2.5 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-xl hover:bg-indigo-100 transition-all border border-indigo-100">
                                            Preview
                                        </button>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <button onclick="deleteService({{ $service->hss_id }})"
                                            class="text-xs text-red-400 hover:text-red-600 hover:underline transition-colors font-medium">
                                            Delete Service
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($filtered->isEmpty())
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
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeServiceModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

                    {{-- Modal Header Image --}}
                    <div class="relative h-64 bg-gray-200 group">
                        <img id="modalImage" src="" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                        <button onclick="closeServiceModal()"
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
                        <button type="button" onclick="closeServiceModal()"
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

    <script>
        // --- 1. Tab Logic ---
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-tab');
                tabContents.forEach(tc => tc.classList.add('hidden'));
                document.getElementById(`${target}-tab-content`).classList.remove('hidden');

                tabButtons.forEach(b => {
                    b.classList.remove('border-indigo-600', 'text-indigo-600');
                    b.classList.add('border-transparent', 'text-gray-500');
                });
                btn.classList.remove('border-transparent', 'text-gray-500');
                btn.classList.add('border-indigo-600', 'text-indigo-600');
            });
        });

        // --- 2. Modal Logic ---
        function openServiceModal(service) {
            const modal = document.getElementById('serviceModal');

            document.getElementById('modalTitle').textContent = service.title;
             document.getElementById('modalImage').src = service.full_image_url;           

            if (service.category) {
                document.getElementById('modalCategory').textContent = service.category.name;
            }

            const descContent = document.getElementById(`data-desc-${service.id}`).innerHTML;
            document.getElementById('modalDescription').innerHTML = descContent;

            const pkgContainer = document.getElementById('modalPackagesContainer');
            pkgContainer.innerHTML = '';

            const tiers = [{
                    key: 'basic',
                    label: 'Basic Tier',
                    color: 'teal',
                    badge: 'bg-teal-100 text-teal-700'
                },
                {
                    key: 'standard',
                    label: 'Standard Tier',
                    color: 'yellow',
                    badge: 'bg-yellow-100 text-yellow-700'
                },
                {
                    key: 'premium',
                    label: 'Premium Tier',
                    color: 'red',
                    badge: 'bg-red-100 text-red-700'
                }
            ];

            let hasPackages = false;

            tiers.forEach(tier => {
                if (service[`${tier.key}_price`]) {
                    hasPackages = true;
                    const desc = document.getElementById(`data-pkg-${tier.key}-desc-${service.id}`).innerHTML;

                    const colors = {
                        teal: 'bg-white border-teal-600 hover:border-teal-600',
                        yellow: 'bg-yellow-50/50 border-yellow-600 hover:border-yellow-600',
                        red: 'bg-red-50/50 border-red-600 hover:border-red-600'
                    };

                    const html = `
                    <div class="relative flex flex-col md:flex-row gap-5 border rounded-2xl p-5 ${colors[tier.color]} transition hover:shadow-md group">
                        <div class="md:w-1/3 flex flex-col justify-center border-b md:border-b-0 md:border-r border-black/5 pb-4 md:pb-0 md:pr-5">
                            <span class="inline-block self-start px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mb-2 ${tier.badge}">${tier.label}</span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-extrabold text-gray-900">RM${service[`${tier.key}_price`]}</span>
                            </div>
                            <span class="text-xs text-gray-500 font-medium">per ${service[`${tier.key}_frequency`]}</span>
                        </div>
                        <div class="md:w-2/3 prose prose-sm max-w-none text-gray-600 text-sm flex items-center">
                            <div>${desc || '<span class="italic opacity-50">No description provided.</span>'}</div>
                        </div>
                    </div>
                `;
                    pkgContainer.innerHTML += html;
                }
            });

            if (!hasPackages) {
                pkgContainer.innerHTML =
                    '<div class="text-center p-6 text-gray-400 italic bg-gray-50 rounded-xl border border-dashed border-gray-200">No pricing packages configured.</div>';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeServiceModal() {
            document.getElementById('serviceModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // --- 3. Actions ---
        function editService(id) {
            window.location.href = "{{ url('services') }}/" + id + "/edit";
        }

        function deleteService(serviceId) {
            Swal.fire({
                title: 'Delete Service?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = "{{ route('services.destroy', ':id') }}".replace(':id', serviceId);
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json()).then(data => {
                        if (data.service) {
                            document.querySelector(`[data-service-id='${serviceId}']`).remove();
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Service deleted successfully'
                            });
                        } else {
                            Swal.fire('Error', 'Unable to delete.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
