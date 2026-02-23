@extends('admin.layout')

@section('content')
    <style>
        .rich-text ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .rich-text ol {
            list-style-type: decimal;
            padding-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .rich-text p {
            margin-bottom: 0.5rem;
        }

        .rich-text strong {
            font-weight: 600;
        }

        /* Tooltip container */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 60px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 50;
            bottom: 125%;
            left: 50%;
            margin-left: -30px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.7rem;
            pointer-events: none;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600
        }

        .badge-red {
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600
        }

        .badge-yellow {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600
        }

        .badge-dark {
            background: #1f2937;
            color: #fff;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600
        }

        .btn-blue {
            background: #eff6ff;
            color: #2563eb;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px
        }

        .btn-orange {
            background: #fff7ed;
            color: #ea580c;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px
        }

        .btn-green {
            background: #ecfdf5;
            color: #059669;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px
        }

        .btn-red {
            background: #fef2f2;
            color: #dc2626;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px
        }

        .btn-blue:hover,
        .btn-orange:hover,
        .btn-green:hover,
        .btn-red:hover {
            opacity: .8
        }
    </style>

    <div class="px-6 py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manage Services</h1>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
            <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-wrap gap-4">

                {{-- Search --}}
                <div class="flex-1 min-w-[250px]">
                    <input type="text" name="search" placeholder="Search by title, description or student name..."
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                        value="{{ request('search') }}">
                </div>

                {{-- Category Filter --}}
                <div>
                    <select name="category" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                style="color: {{ $category->color }}; background:white; font-weight:600;"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Rating Filter --}}
                <div>
                    <select name="rating" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Ratings</option>
                        <option value="0-1" {{ request('rating') == '0-1' ? 'selected' : '' }}>0.0 – 1.0 ⭐</option>
                        <option value="1-2" {{ request('rating') == '1-2' ? 'selected' : '' }}>1.0 – 2.0 ⭐</option>
                        <option value="2-3" {{ request('rating') == '2-3' ? 'selected' : '' }}>2.0 – 3.0 ⭐</option>
                        <option value="3-4" {{ request('rating') == '3-4' ? 'selected' : '' }}>3.0 – 4.0 ⭐</option>
                        <option value="4-5" {{ request('rating') == '4-5' ? 'selected' : '' }}>4.0 – 5.0 ⭐</option>
                    </select>
                </div>


                {{-- Status Filter --}}
                <div>
                    <select name="status" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended
                        </option>
                    </select>
                </div>


                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
            </form>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase ">Service</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-center">Category</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-center">Seller</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-center">Avg Rating</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-center">Reviews</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-center">Warning</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-center">Status</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($services as $service)
                            <tr class="hover:bg-gray-50 transition">

                                {{-- SERVICE --}}
                             <td class="py-4 px-6">
    <div class="flex gap-3 items-center">
        <div class="h-14 w-14 bg-gray-100 rounded-lg overflow-hidden border">
            @if ($service->image_path)
                @php
                    $path = $service->image_path;
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
                     alt="{{ $service->title }}"
                     class="w-full h-full object-cover">
            @else
                <div class="flex items-center justify-center w-full h-full text-gray-400 text-xs">
                    No Image
                </div>
            @endif
        </div>
        <div>
            <p class="font-bold text-gray-900">
                {{ Str::limit($service->title, 16) }}
            </p>
            <p class="text-xs text-gray-500">
                {{ Str::limit(strip_tags($service->description), 16) }}
            </p>
        </div>
    </div>
</td>
                                {{-- CATEGORY --}}
                                <td class="py-4 px-6 text-center">
                                    @if ($service->category)
                                        <span class="inline-block px-1 py-0.5 rounded-full font-semibold text-white"
                                            style="
                font-size: 10px;
                white-space: nowrap;
                background: {{ $service->category->color ?? '#6b7280' }};
              ">
                                            {{ $service->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">No Category</span>
                                    @endif
                                </td>




                                {{-- SELLER --}}
                                <td class="py-4 px-6 text-sm text-gray-700">
                                    {{ $service->user->name ?? 'Unknown' }}
                                </td>

                                {{-- AVG RATING --}}
                                <td class="py-4 px-6 text-center">
                                    <span class="font-bold text-gray-800">
                                        {{ number_format($service->reviews_avg_rating ?? 0, 1) }}
                                    </span>
                                    ⭐
                                </td>

                                {{-- REVIEWS --}}
                                <td class="py-4 px-6 text-center">
                                    <a href="{{ route('admin.services.reviews', $service->id) }}"
                                        class="text-blue-600 hover:underline text-sm">
                                        View all ({{ $service->reviews_count ?? 0 }}) reviews
                                    </a>
                                </td>

                                {{-- WARNING --}}
                                <td class="py-4 px-6 text-center">
                                    <span
                                        class="font-mono font-bold {{ ($service->warning_count ?? 0) >= 2 ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ $service->warning_count ?? 0 }}/3
                                    </span>
                                </td>

                                {{-- STATUS --}}
                                <td class="py-4 px-6 text-center">
                                    @if ($service->approval_status === 'approved')
                                        <span class="badge-green">Approved</span>
                                    @elseif($service->approval_status === 'rejected')
                                        <span class="badge-red">Rejected</span>
                                    @elseif($service->approval_status === 'suspended')
                                        <span class="badge-dark">Suspended</span>
                                    @else
                                        <span class="badge-yellow">Pending</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="py-4 px-6 text-center">
                                    <div class="flex justify-center gap-2">

                                        {{-- VIEW Always --}}
                                        <a href="{{ route('admin.services.show', $service->id) }}" class="btn-blue">
                                            View
                                        </a>

                                        {{-- IF APPROVED → Allow Warning --}}
                                        @if ($service->approval_status === 'approved')
                                            <button
                                                onclick="openWarningModal('{{ route('admin.services.warn', $service->id) }}')"
                                                class="btn-orange">
                                                Warning
                                            </button>
                                        @endif

                                        {{-- IF PENDING --}}
                                        @if ($service->approval_status === 'pending')
                                            <button
                                                onclick="openWarningModal('{{ route('admin.services.warn', $service->id) }}')"
                                                class="btn-orange">
                                                Warning
                                            </button>

                                            <form action="{{ route('admin.services.approve', $service->id) }}"
                                                method="POST">
                                                @csrf @method('PATCH')
                                                <button class="btn-green">Approve</button>
                                            </form>

                                           <button onclick="openRejectModal('{{ route('admin.services.reject', $service->id) }}')" 
        class="btn-red">
    Reject
</button>
                                        @endif

                                        {{-- IF WARNING 3/3 & NOT YET SUSPENDED --}}
                                        @if (($service->warning_count ?? 0) >= 3 && $service->approval_status !== 'suspended')
                                            <button
                                                onclick="confirmSuspend('{{ route('admin.services.suspend', $service->id) }}')"
                                                class="btn-red">
                                                Suspend
                                            </button>
                                        @endif



                                        {{-- IF SUSPENDED (BLOCKED) → Only Unblock --}}
                                        @if ($service->approval_status === 'suspended')
                                            <button
                                                onclick="confirmUnblock('{{ route('admin.services.unblock', $service->id) }}')"
                                                class="btn-green">
                                                Reactive
                                            </button>
                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-gray-500">
                                    No services found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
            @if ($services->hasPages())
                <div class="bg-white px-6 py-4 border-t border-gray-200">{{ $services->links() }}</div>
            @endif
        </div>
    </div>

{{-- REJECT REASON MODAL --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRejectModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="rejectForm" method="POST" action="">
                @csrf 
                @method('PATCH') {{-- Important for Laravel Update --}}
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            {{-- Red X Icon --}}
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Reject Service
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-2">
                                    Please provide a reason why this service is being rejected. This will be shown to the student.
                                </p>
                                <textarea name="reject_reason" rows="4" 
                                    class="w-full shadow-sm focus:ring-red-500 focus:border-red-500 mt-1 block sm:text-sm border border-gray-300 rounded-md p-2" 
                                    placeholder="e.g. Inappropriate images, Description too short..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Rejection
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    {{-- Warning Modal --}}
    <div id="warningModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeWarningModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="warningForm" method="POST" action="">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Issue Warning
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Please state the reason for this warning. This will be emailed to the student.
                                    </p>
                                    <textarea name="reason" rows="4"
                                        class="w-full shadow-sm focus:ring-orange-500 focus:border-orange-500 mt-1 block sm:text-sm border border-gray-300 rounded-md p-2"
                                        placeholder="Example: Inappropriate service description..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Send Warning
                        </button>
                        <button type="button" onclick="closeWarningModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- Javascript Baru untuk Warning Modal ---
        function openWarningModal(url) {
            document.getElementById('warningForm').action = url;
            document.getElementById('warningModal').classList.remove('hidden');
        }

        function closeWarningModal() {
            document.getElementById('warningModal').classList.add('hidden');
        }

        // --- Javascript Asal (Service Detail Modal) ---
        function openServiceModal(service, providerName, categoryName) {
            document.getElementById('modal-title').textContent = service.title;
            document.getElementById('modal-category').textContent = categoryName;
            document.getElementById('modal-provider').textContent = providerName;
            document.getElementById('modal-description').textContent = service.description;

            const badge = document.getElementById('modal-status-badge');

            // --- LOGIC BARU (HANDLE SUSPENDED) ---
            let badgeClass = 'bg-yellow-100 text-yellow-800'; // Default Pending
            if (service.approval_status === 'approved') {
                badgeClass = 'bg-green-100 text-green-800';
            } else if (service.approval_status === 'rejected') {
                badgeClass = 'bg-red-100 text-red-800';
            } else if (service.approval_status === 'suspended') {
                badgeClass = 'bg-gray-800 text-white'; // Style untuk Suspended
            }

            badge.className = `px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${badgeClass}`;
            badge.textContent = service.approval_status.charAt(0).toUpperCase() + service.approval_status.slice(1);

            const date = new Date(service.created_at);
            document.getElementById('modal-date').textContent = date.toLocaleDateString();

            const img = document.getElementById('modal-image');
            if (service.image_path) {
                img.src = '/storage/' + service.image_path;
                img.classList.remove('hidden');
            } else {
                img.src = 'https://via.placeholder.com/600x400?text=No+Image+Provided';
            }

            // Logic Harga Basic
            if (service.basic_price) {
                document.getElementById('pkg-basic').classList.remove('hidden');
                document.getElementById('modal-basic-price').textContent = 'RM ' + service.basic_price;
                document.getElementById('modal-basic-desc').textContent = service.basic_description || 'No description';
                document.getElementById('modal-basic-duration').textContent = (service.basic_duration || 0) + ' hrs';
            } else {
                document.getElementById('pkg-basic').classList.add('hidden');
            }

            // Logic Harga Standard
            if (service.standard_price) {
                document.getElementById('pkg-standard').classList.remove('hidden');
                document.getElementById('modal-std-price').textContent = 'RM ' + service.standard_price;
                document.getElementById('modal-std-desc').textContent = service.standard_description || 'No description';
                document.getElementById('modal-std-duration').textContent = (service.standard_duration || 0) + ' hrs';
            } else {
                document.getElementById('pkg-standard').classList.add('hidden');
            }

            // Logic Harga Premium (Note: Your modal HTML was missing pkg-premium elements in the logic but had them in loops previously. Added checking logic assuming elements exist or hidden)
            // Note: In your HTML structure above, you have "pkg-standard" but I didn't see "pkg-premium" ID in the modal HTML part you provided. 
            // I'll leave checking to avoid JS errors if element missing.
            const premPkg = document.getElementById('pkg-premium');
            if (premPkg) {
                if (service.premium_price) {
                    premPkg.classList.remove('hidden');
                    document.getElementById('modal-prem-price').textContent = 'RM ' + service.premium_price;
                    document.getElementById('modal-prem-desc').textContent = service.premium_description ||
                        'No description';
                    document.getElementById('modal-prem-duration').textContent = (service.premium_duration || 0) + ' hrs';
                } else {
                    premPkg.classList.add('hidden');
                }
            }

            document.getElementById('serviceModal').classList.remove('hidden');
        }

        function closeServiceModal() {
            document.getElementById('serviceModal').classList.add('hidden');
        }

        // --- Action Logic (Approve/Reject) ---
function openRejectModal(url) {
    // Set the form action to the route (e.g., admin/services/123/reject)
    document.getElementById('rejectForm').action = url;
    // Show the modal
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
        function confirmAction(url, action) {
            const title = action === 'approve' ? 'Approve Service?' : 'Reject Service?';
            const text = action === 'approve' ?
                "This service will become visible to the public." :
                "This service will be rejected.";
            const confirmBtnColor = action === 'approve' ? '#10B981' : '#EF4444';

            // FIXED: Defined the method variable here
            const method = 'POST';

            Swal.fire({
                title: title,
                text: text,
                icon: action === 'approve' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, ' + action + ' it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = method;
                    form.appendChild(methodField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmSuspend(url) {
            Swal.fire({
                title: 'Suspend Service?',
                text: 'This service will be blocked and hidden from users.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Suspend'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';
                    form.appendChild(methodField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }


        function confirmUnblock(url) {
            Swal.fire({
                title: 'Reactive Service?',
                text: 'This service will be active again and visible to users.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Reactivate the service'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';
                    form.appendChild(methodField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
