@extends('admin.layout')

@section('content')
    <div class="px-4 md:px-8">

        <!-- Title -->
        <h1 class="text-4xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500 mt-1">Monitor platform activity and analytics.</p>

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-10">

            <!-- CARD: Total Students -->
            <a href="{{ route('admin.students.index') }}"
                class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl hover:scale-[1.02] transition border border-gray-100 block">
                <p class="text-gray-500 font-medium">Total Students</p>
                <p class="text-5xl font-bold text-blue-600 mt-2">{{ $totalStudents }}</p>
            </a>

            <!-- CARD: Total Community Users -->
            <a href="{{ route('admin.community.index') }}"
                class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl hover:scale-[1.02] transition border border-gray-100 block">
                <p class="text-gray-500 font-medium">Total Community Users</p>
                <p class="text-5xl font-bold text-purple-600 mt-2">{{ $totalCommunityUsers }}</p>
            </a>

            <!-- CARD: Total Services -->
            <a href="{{ route('admin.services.index') }}"
                class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl hover:scale-[1.02] transition border border-gray-100 block">
                <p class="text-gray-500 font-medium">Total Services</p>
                <p class="text-5xl font-bold text-pink-600 mt-2">{{ $totalServices }}</p>
            </a>

            <!-- CARD: Pending Requests -->
            <a href="#"
                class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl hover:scale-[1.02] transition border border-gray-100 block">
                <p class="text-gray-500 font-medium">Pending Requests</p>
                <p class="text-5xl font-bold text-yellow-600 mt-2">{{ $pendingRequests }}</p>
            </a>

        </div>

        @if ($pendingStudents > 0)
            <div
                class="mt-6 bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl flex items-center justify-between">
                <div>
                    <strong>⚠ Action Required</strong><br>
                    {{ $pendingStudents }} student(s) are waiting for approval.
                </div>

                <a href="{{ route('admin.students.index', ['verification_status' => 'pending']) }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Review Now
                </a>
            </div>
        @endif

        @if ($pendingHelpers > 0)
            <div
                class="mt-4 bg-yellow-100 border border-yellow-300 text-yellow-800 px-6 py-4 rounded-xl flex items-center justify-between">
                <div>
                    <strong>⚠ Action Required</strong><br>
                    {{ $pendingHelpers }} helper(s) are waiting for verification.
                </div>

                <a href="{{ route('admin.students.index', ['verification_status' => 'pending']) }}"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                    Review Helpers
                </a>
            </div>
        @endif

        @if ($studentsWithoutStatus > 0)
            <div
                class="mt-6 bg-orange-100 border border-orange-300 text-orange-800 px-6 py-4 rounded-xl flex items-center justify-between">
                <div>
                    <strong>⚠ Action Required</strong><br>
                    {{ $studentsWithoutStatus }} student(s) do not have an academic status assigned.
                </div>

                <a href="{{ route('admin.student_status.index') }}"
                    class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                    Assign Status
                </a>
            </div>
        @endif

        <!-- CHARTS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">

            <!-- LINE CHART -->
            <div class="bg-white p-8 rounded-2xl shadow-md border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Monthly Student Registrations</h2>
                <canvas id="studentChart" height="120"></canvas>
            </div>

            <!-- BAR CHART -->
            <div class="bg-white p-8 rounded-2xl shadow-md border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Services Created Per Month</h2>
                <canvas id="serviceChart" height="120"></canvas>
            </div>

        </div>

    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        /* =========================
       MONTH LABELS (Jan–Dec)
    ========================= */
        const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        /* =========================
           LINE CHART – STUDENTS
        ========================= */
        const studentCtx = document.getElementById('studentChart').getContext('2d');

        new Chart(studentCtx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Students',
                    data: {!! json_encode(array_values($studentsPerMonth)) !!},
                    borderColor: '#6366F1', // Indigo
                    backgroundColor: 'rgba(99, 102, 241, 0.25)',
                    pointBackgroundColor: '#4F46E5',
                    pointBorderColor: '#ffffff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E5E7EB'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        /* =========================
           BAR CHART – SERVICES
        ========================= */
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');

        new Chart(serviceCtx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Services Created',
                    data: {!! json_encode(array_values($servicesPerMonth)) !!},
                    backgroundColor: [
                        '#22C55E', '#16A34A', '#10B981', '#34D399',
                        '#4ADE80', '#86EFAC', '#6EE7B7', '#2DD4BF',
                        '#14B8A6', '#0D9488', '#059669', '#047857'
                    ],
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E5E7EB'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endsection
