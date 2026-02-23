@extends('layouts.helper')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Page Title --}}
            <br>
            <div class="mb-8 text-left mt-10">
                <h1 class="text-4xl font-bold text-gray-900">My Services</h1>
                <p class="text-gray-600 mt-2">Add, edit, or manage your services offered to the community</p>
            </div>

            <!-- Add Service Button -->
            <div class="mb-8 flex justify-end">
                <a href="{{ route('services.create') }}"
                    class="bg-custom-teal hover:bg-teal-800 text-white px-3 py-3 rounded-lg font-medium flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Add New Service</span>
                </a>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="flex space-x-4 border-b border-gray-200">
                    <button data-tab="all"
                        class="tab-btn py-2 px-4 text-sm font-medium text-gray-700 hover:text-indigo-600 focus:outline-none">
                        All
                    </button>
                    <button data-tab="pending"
                        class="tab-btn py-2 px-4 text-sm font-medium text-gray-700 hover:text-indigo-600 focus:outline-none">
                        Pending
                    </button>
                    <button data-tab="approved"
                        class="tab-btn py-2 px-4 text-sm font-medium text-gray-700 hover:text-indigo-600 focus:outline-none">
                        Approved
                    </button>
                    <button data-tab="rejected"
                        class="tab-btn py-2 px-4 text-sm font-medium text-gray-700 hover:text-indigo-600 focus:outline-none">
                        Rejected
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="tab-contents">
                @php
                    $statuses = ['all', 'pending', 'approved', 'rejected'];
                @endphp

                @foreach ($statuses as $status)
                    <div id="{{ $status }}-tab-content"
                        class="tab-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 {{ $status !== 'all' ? 'hidden' : '' }}">
                        @php
                            $filtered = $status === 'all' ? $services : $services->where('approval_status', $status);
                        @endphp
                        @foreach ($filtered as $service)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
                                data-service-id="{{ $service->id }}">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        @if ($service->image_path)
                                            @php
                                                $isStorageImage = Str::startsWith($service->image_path, 'services/');
                                            @endphp
                                            <img src="{{ $isStorageImage ? asset('storage/' . $service->image_path) : asset($service->image_path) }}"
                                                class="w-full h-40 object-cover rounded-lg mb-4"
                                                alt="{{ $service->title }}">
                                        @else
                                            <img src="{{ asset('images/default_service.jpg') }}"
                                                class="w-full h-40 object-cover rounded-lg mb-4"
                                                alt="default service image">
                                        @endif

                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service->title }}</h3>

                                        @if ($service->category)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                style="color: {{ $service->category->color }}; border: 1px solid {{ $service->category->color }}; font-size: 13px;">
                                                {{ $service->category->name }} </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        @php
                                            $statusClass = match ($service->approval_status) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ ucfirst($service->approval_status) }}
                                        </span>
                                    </div>
                                </div>
                                

                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $service->description }}</p>

                                <div class="text-sm text-gray-700 mt-4 space-y-2">

                                    @if ($service->basic_price)
                                        <div
                                            class="flex justify-between items-center border border-gray-100 rounded-lg p-2">
                                            <span class="font-medium">Basic Package</span>
                                            <span
                                                class="font-semibold text-gray-800">RM{{ number_format($service->basic_price, 2) }}
                                                per {{ $service->basic_frequency }}</span>

                                        </div>
                                    @endif

                                    @if ($service->standard_price)
                                        <div
                                            class="flex justify-between items-center border border-gray-100 rounded-lg p-2">
                                            <span class="font-medium">Standard Package</span>
                                            <span
                                                class="font-semibold text-gray-800">RM{{ number_format($service->standard_price, 2) }}
                                                per {{ $service->standard_frequency }}</span>
                                        </div>
                                    @endif

                                    @if ($service->premium_price)
                                        <div
                                            class="flex justify-between items-center border border-gray-100 rounded-lg p-2">
                                            <span class="font-medium">Premium Package</span>
                                            <span
                                                class="font-semibold text-gray-800">RM{{ number_format($service->premium_price, 2) }}
                                                per {{ $service->premium_frequency }}</span>
                                        </div>
                                    @endif

                                </div>


                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <button onclick="editService({{ $service->id }})"
                                        class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                        Edit
                                    </button>
                                    <button onclick="deleteService({{ $service->id }})"
                                        class="text-red-600 hover:text-red-500 text-sm font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <!-- JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.getAttribute('data-tab');

                    // Hide all
                    tabContents.forEach(tc => tc.classList.add('hidden'));
                    // Show target
                    document.getElementById(`${target}-tab-content`).classList.remove('hidden');

                    // Optional: active button styling
                    tabButtons.forEach(b => b.classList.remove('text-indigo-600', 'border-b-2',
                        'border-indigo-600'));
                    btn.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');

                });
            });

            // Activate default tab
            tabButtons[0].click();
        });

        // Handle service status toggle
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.service-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const serviceId = this.dataset.serviceId;
                    const isActive = this.checked;

                    fetch(`/student-services/${serviceId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                is_active: isActive
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.service) {
                                // Update status badge
                                const statusBadge = this.closest('.bg-white').querySelector(
                                    '.inline-flex.items-center.px-2.py-1');
                                const statusDot = statusBadge.querySelector('.w-1\\.5');

                                if (isActive) {
                                    statusBadge.className =
                                        'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                                    statusDot.className =
                                        'w-1.5 h-1.5 rounded-full bg-green-400 mr-1';
                                    statusBadge.textContent = 'Active';
                                } else {
                                    statusBadge.className =
                                        'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
                                    statusDot.className =
                                        'w-1.5 h-1.5 rounded-full bg-red-400 mr-1';
                                    statusBadge.textContent = 'Inactive';
                                }
                                statusBadge.prepend(statusDot);
                            } else {
                                // Revert toggle if update failed
                                this.checked = !isActive;
                                alert('Error updating service status. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Revert toggle if update failed
                            this.checked = !isActive;
                            alert('Error updating service status. Please try again.');
                        });
                });
            });
        });
        // Delete service
        function deleteService(serviceId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = "{{ route('services.destroy', ':id') }}".replace(':id', serviceId);

                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        }).then(res => res.json())
                        .then(data => {
                            if (data.service) {
                                const card = document.querySelector(`[data-service-id='${serviceId}']`);
                                if (card) card.remove();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'The service has been deleted.',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    position: 'center'
                                });
                            } else {
                                Swal.fire('Error', data.error || 'Unable to delete service.', 'error');
                            }
                        }).catch(err => {
                            Swal.fire('Error', 'Something went wrong.', 'error');
                        });
                }
            });
        }

        function editService(serviceId) {
            window.location.href = `/services/${serviceId}/edit`;
        }
    </script>
@endsection
