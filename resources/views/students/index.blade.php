@extends('layouts.helper')

@section('content')
    {{-- Google Fonts for a more premium look --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="availabilityComponent()">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Dashboard</h1>
                <p class="text-slate-500 mt-1">Welcome back, {{ Auth::user()->name }}! Here's what's happening today.</p>
            </div>

            <div class="bg-white p-2 pr-4 rounded-full shadow-sm border border-gray-200 flex items-center gap-3">
                <div class="h-10 w-10 rounded-full flex items-center justify-center transition-colors duration-300"
                    :class="isAvailable ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                    <i class="fa-solid fa-power-off text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Status</span>
                    <span class="text-sm font-bold" :class="isAvailable ? 'text-green-600' : 'text-red-600'"
                        x-text="isAvailable ? 'Accepting Orders' : 'Currently Unavailable'">
                    </span>
                </div>
                <div class="h-8 w-px bg-gray-200 mx-2"></div>
                <button @click="openModal()"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                    Settings
                </button>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('service-requests.index') }}#pending"
                class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-blue-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-blue-600 transition-colors">Pending Request
                    </p>
                    <h3 class="text-2xl font-bold text-yellow-500 mt-1">
                        {{-- Mengambil jumlah dari array newOrders yang sudah di-sum di controller --}}
                        {{ array_sum($newOrders ?? []) }}
                    </h3>
                </div>
                <div
                    class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                </div>
            </a>
            {{-- 1. Active Services Link --}}
            <a href="{{ route('services.manage') }}"
                class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-indigo-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-indigo-600 transition-colors">My
                        Services</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ Auth::user()->studentServices()->count() }}</h3>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-briefcase text-xl"></i>
                </div>
            </a>

            {{-- 2. Pending Requests Link --}}
        <a href="{{ route('service-requests.index') }}#completed"
                class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-emerald-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-emerald-600 transition-colors">Completed
                        Order</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">
                        {{-- Menggunakan variable $completedOrders dari controller --}}
                        {{ $completedOrders ?? 0 }}
                    </h3>
                </div>
                <div
                    class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
            </a>


            {{-- 3. Total Revenue Link --}}
            <a href="#" {{-- Masukkan route wallet/payout sini --}}
                class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-emerald-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-emerald-600 transition-colors">Total
                        Estimated Revenue</p>
                    <h3 class="text-2xl font-bold text-green-500 mt-1">RM{{ number_format(array_sum($sales ?? []), 2) }}
                    </h3>
                </div>
                <div
                    class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-coins text-xl"></i>
                </div>
            </a>

            {{-- 4. Average Rating Link --}}
            <a href="{{ route('students.edit') }}" {{-- Atau link ke profile review --}}
                class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-yellow-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-yellow-600 transition-colors">Average
                        Rating</p>
                    <div class="flex items-center gap-2 mt-1">
                        <h3 class="text-2xl font-bold text-slate-900">4.9</h3>
                        <i class="fa-solid fa-star text-yellow-400 text-sm"></i>
                    </div>
                </div>
                <div
                    class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-trophy text-xl"></i>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 h-full">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Performance Overview</h2>
                            <p class="text-sm text-gray-500">Track your earnings and order volume</p>
                        </div>
                        <form method="GET">
                            <select name="range" onchange="this.form.submit()"
                                class="bg-gray-50 border-0 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-medium">
                                <option value="30days" {{ ($range ?? '30days') === '30days' ? 'selected' : '' }}>Last 30
                                    Days</option>
                                <option value="3months" {{ ($range ?? '') === '3months' ? 'selected' : '' }}>Last 3 Months
                                </option>
                                <option value="yearly" {{ ($range ?? '') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </form>
                    </div>
                    <div class="relative h-[300px] w-full">
                        <canvas id="overviewChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        @php
                            $actions = [
                                [
                                    'title' => 'Create New Service',
                                    'icon' => 'fa-plus',
                                    'color' => 'text-indigo-600',
                                    'bg' => 'bg-indigo-50',
                                    'route' => route('services.create'),
                                ],
                                [
                                    'title' => 'Manage Services',
                                    'icon' => 'fa-list-check',
                                    'color' => 'text-blue-600',
                                    'bg' => 'bg-blue-50',
                                    'route' => route('services.manage'),
                                ],

                                [
                                    'title' => 'Edit Profile',
                                    'icon' => 'fa-user-pen',
                                    'color' => 'text-gray-600',
                                    'bg' => 'bg-gray-50',
                                    'route' => route('students.edit'),
                                ],
                            ];
                        @endphp

                        @foreach ($actions as $action)
                            <a href="{{ $action['route'] }}"
                                class="flex items-center p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-200 transition-all group">
                                <div
                                    class="w-10 h-10 {{ $action['bg'] }} {{ $action['color'] }} rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid {{ $action['icon'] }}"></i>
                                </div>
                                <span
                                    class="font-medium text-gray-700 group-hover:text-gray-900">{{ $action['title'] }}</span>
                                <i
                                    class="fa-solid fa-chevron-right ml-auto text-gray-300 group-hover:text-gray-500 text-xs"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Latest Feedback</h2>
                    <div class="space-y-4">
                        @forelse($recentReviews ?? [] as $review)
                            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-gray-900">{{ $review->user->name }}</span>
                                    </div>
                                    <div class="flex text-yellow-400 text-xs">
                                        @for ($i = 0; $i < $review->rating; $i++)
                                            <i class="fa-solid fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 italic">"{{ Str::limit($review->comment, 80) }}"</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-2">No reviews yet.</p>
                        @endforelse
                    </div>
                </div>

                <div x-show="!isAvailable && startDate && endDate" x-cloak
                    class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
                    <div class="flex items-start gap-3">
                        <i class="fa-regular fa-calendar-xmark text-amber-600 text-xl mt-1"></i>
                        <div>
                            <h3 class="font-bold text-amber-900">Scheduled Time Off</h3>
                            <p class="text-sm text-amber-700 mt-1">
                                You are set to be unavailable from
                                <span class="font-bold" x-text="formatDate(startDate)"></span> to
                                <span class="font-bold" x-text="formatDate(endDate)"></span>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50 px-4" style="display: none;"
            x-cloak>
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden transform transition-all scale-100">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Availability Settings</h2>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <div>
                            <span class="block text-sm font-bold text-indigo-900">Accepting Orders</span>
                            <span class="text-xs text-indigo-700">Toggle to pause all services</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" x-model="isAvailable">
                            <div
                                class="w-11 h-6 bg-gray-300 rounded-full peer peer-focus:ring-4 peer-focus:ring-indigo-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                            </div>
                        </label>
                    </div>

                    <div x-show="!isAvailable" x-transition class="space-y-4">
                        <p class="text-sm text-gray-500">Please select the dates you will be away. Your services will be
                            hidden during this period.</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Start Date</label>
                                <input type="date" x-model="startDate"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">End Date</label>
                                <input type="date" x-model="endDate"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mr-1">Quick
                                Add:</span>
                            <button @click="addDuration(7, 0)"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 rounded-lg text-xs font-medium transition border border-gray-200 hover:border-indigo-200">
                                +1 Week
                            </button>
                            <button @click="addDuration(0, 1)"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 rounded-lg text-xs font-medium transition border border-gray-200 hover:border-indigo-200">
                                +1 Month
                            </button>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-between items-center">
                    <button @click="deleteDates()" x-show="!isAvailable"
                        class="text-red-600 text-sm font-semibold hover:text-red-800 transition">Clear Dates</button>
                    <span x-show="isAvailable"></span>
                    <button @click="saveChanges()"
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-md shadow-indigo-200">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($labels ?? []);
        const sales = @json($sales ?? []);
        const cancelled = @json($cancelled ?? []);
        const completed = @json($completedDaily ?? []);
        const newOrders = @json($newOrders ?? []);

        const ctx = document.getElementById('overviewChart').getContext('2d');

        // Create a gradient for the primary line
        const gradientSales = ctx.createLinearGradient(0, 0, 0, 300);
        gradientSales.addColorStop(0, 'rgba(90, 219, 232, 0.2)');
        gradientSales.addColorStop(1, 'rgba(90, 219, 232, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Sales ($)',
                        data: sales,
                        borderWidth: 3,
                        borderColor: '#0EA5E9', // Sky Blue
                        backgroundColor: gradientSales,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0EA5E9',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'New Orders',
                        data: newOrders,
                        borderWidth: 2,
                        borderColor: '#10B981', // Emerald
                        pointBackgroundColor: '#10B981',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.4
                    },
                    {
                        label: 'Completed',
                        data: completed,
                        borderWidth: 2,
                        borderColor: '#6366F1', // Indigo
                        pointBackgroundColor: '#6366F1',
                        fill: false,
                        tension: 0.4,
                        hidden: true // Hidden by default to reduce clutter
                    },
                    {
                        label: 'Cancelled',
                        data: cancelled,
                        borderWidth: 2,
                        borderColor: '#EF4444', // Red
                        pointBackgroundColor: '#EF4444',
                        fill: false,
                        tension: 0.4,
                        hidden: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 13
                        },
                        bodyFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 12
                        },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            borderDash: [4, 4]
                        },
                        ticks: {
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            color: '#64748b'
                        },
                        border: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            color: '#64748b'
                        },
                        border: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });

        // Alpine Logic (Preserved from original but cleaned up)
        function availabilityComponent() {
            return {
                isAvailable: {{ Auth::user()->is_available ? 'true' : 'false' }},
                showModal: false,
                startDate: '{{ Auth::user()->unavailable_start_date ?? '' }}',
                endDate: '{{ Auth::user()->unavailable_end_date ?? '' }}',

                openModal() {
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    return new Date(dateString).toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });
                },

                // LOGIC: Add 1 Week or 1 Month
                addDuration(daysToAdd, monthsToAdd) {
                    // 1. If Start Date is empty, set it to Today
                    if (!this.startDate) {
                        this.startDate = new Date().toISOString().split('T')[0];
                    }

                    // 2. Determine base date (Start from existing EndDate if present, otherwise StartDate)
                    let baseString = this.endDate ? this.endDate : this.startDate;

                    // Add T12:00:00 to avoid timezone rolling the date back
                    let dateObj = new Date(baseString + 'T12:00:00');

                    // 3. Add duration
                    if (daysToAdd > 0) dateObj.setDate(dateObj.getDate() + daysToAdd);
                    if (monthsToAdd > 0) dateObj.setMonth(dateObj.getMonth() + monthsToAdd);

                    // 4. Update End Date
                    this.endDate = dateObj.toISOString().split('T')[0];
                },

                deleteDates() {
                    this.startDate = '';
                    this.endDate = '';
                    this.isAvailable = true;
                },

                saveChanges() {
                    let finalStartDate = this.startDate;
                    let finalEndDate = this.endDate;

                    // Validation
                    if (!this.isAvailable) {
                        if (!finalStartDate || !finalEndDate) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Date Required',
                                text: 'Please select both start and end dates.',
                                confirmButtonColor: '#3085d6'
                            });
                            return;
                        }
                        if (new Date(finalStartDate) > new Date(finalEndDate)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Dates',
                                text: 'Start date cannot be after end date.',
                                confirmButtonColor: '#d33'
                            });
                            return;
                        }
                    } else {
                        // If available, we clear dates in the backend logic usually, 
                        // but we send null here to be sure.
                        finalStartDate = null;
                        finalEndDate = null;
                    }

                    // API Request
     fetch("{{ route('availability.updateSettings') }}", {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        is_available: this.isAvailable,
        start_date: finalStartDate,
        end_date: finalEndDate
    })
})
.then(res => {
    if (!res.ok) return res.json().then(e => { throw new Error(e.message || 'Server error'); });
    return res.json();
})
.then(data => {
    this.isAvailable = data.is_available;
    this.startDate = data.start_date || '';
    this.endDate = data.end_date || '';
    this.closeModal();
    Swal.fire({
        icon: 'success',
        title: 'Updated!',
        text: data.message,
        showConfirmButton: false,
        timer: 1500
    });
})
.catch(err => {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: err.message,
        confirmButtonColor: '#d33'
    });
});
                }
            }
        }
    </script>
@endsection
