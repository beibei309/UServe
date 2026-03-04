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
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
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
            border: 1px solid var(--border-color);
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
    </style>

    <div class="px-4 sm:px-6 py-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Manage Services</h1>
        </div>

        <div class="p-4 rounded-lg shadow-xl mb-6 border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-wrap gap-4">

                {{-- Search --}}
                <div class="flex-1 min-w-[250px]">
                    <input type="text" name="search" placeholder="Search by title, description or student name..."
                        class="w-full px-4 py-2 border rounded-lg transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                        style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);"
                        value="{{ request('search') }}">
                </div>

                {{-- Category Filter --}}
                <div>
                    <select name="category" class="px-4 py-2 border rounded-lg transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                            style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                        <option value="">All Categories</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->hc_id }}"
                                style="color: {{ $category->hc_color }}; background: var(--bg-tertiary); font-weight:600;"
                                {{ request('category') == $category->hc_id ? 'selected' : '' }}>
                                {{ $category->hc_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Rating Filter --}}
                <div>
                    <select name="rating" class="px-4 py-2 border rounded-lg transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                            style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
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
                    <select name="status" class="px-4 py-2 border rounded-lg transition-colors duration-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                            style="background-color: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color);">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>


                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-lg hover:from-cyan-400 hover:to-blue-500 transition-all duration-300">
                    Search
                </button>
            </form>
        </div>

        
        
        {{-- Data Table --}}
        <div class="p-4 rounded-lg shadow-xl border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr style="background-color: var(--bg-tertiary);">
                            <th class="py-3 px-3 text-left text-xs font-medium"
                                style="color: var(--text-secondary);">Service
                            </th>
                            <th class="py-3 px-3 text-center text-xs font-medium"
                                style="color: var(--text-secondary);">Category
                            </th>
                            <th class="py-3 px-3 text-center text-xs font-medium"
                                style="color: var(--text-secondary);">Student
                            </th>
                            <th class="py-3 px-3 text-center text-xs font-medium"
                                style="color: var(--text-secondary);">Avg Rating
                            </th>
                            <th class="py-3 px-3 text-center text-xs font-medium"
                                style="color: var(--text-secondary);">Reviews
                            </th>
                            <th class="py-3 px-3 text-center text-xs font-medium"
                                style="color: var(--text-secondary);">Warning
                            </th>
                            <th class="py-3 px-3 text-center text-xs font-medium"
                                style="color: var(--text-secondary);">Status
                            </th>
                            <th class="py-3 px-3 text-center text-xs font-medium"
                                style="color: var(--text-secondary);">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr class="border-b transition-all duration-300" style="border-color: var(--border-color);">
                                {{-- SERVICE --}}
                                <td class="py-4 px-3">
                                    <div class="flex items-center gap-3">
                                        {{-- Service Image --}}
                                        <div class="h-8 w-8 rounded-lg overflow-hidden border"
                                             style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                                            @if ($service->hss_image_path)
                                                @php
                                                    $path = $service->hss_image_path;
                                                    if (Str::startsWith($path, ['http://', 'https://'])) {
                                                        $imageUrl = $path;
                                                    } elseif (file_exists(public_path('storage/' . $path))) {
                                                        $imageUrl = asset('storage/' . $path);
                                                    } else {
                                                        $imageUrl = asset($path);
                                                    }
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="{{ $service->hss_title }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="flex items-center justify-center w-full h-full text-xs"
                                                    style="color: var(--text-muted);">
                                                    @
                                                </div>
                                            @endif
                                        </div>
                                        {{-- Service Info --}}
                                        <div>
                                            <p class="font-semibold text-sm transition-colors duration-300"
                                                style="color: var(--text-primary);">
                                                {{ Str::limit($service->hss_title, 18) }}
                                            </p>
                                            <p class="text-xs transition-colors duration-300"
                                                style="color: var(--text-secondary);">
                                                {{ Str::limit(strip_tags($service->hss_description), 25) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- CATEGORY --}}
                                <td class="py-4 px-3 text-center">
                                    @if ($service->category)
                                        <span class="inline-block px-2 py-1 rounded text-white text-xs font-semibold"
                                            style="background: {{ $service->category->hc_color ?? '#6b7280' }};">
                                            {{ $service->category->hc_name }}
                                        </span>
                                    @else
                                        <span class="text-xs transition-colors duration-300"
                                            style="color: var(--text-muted);">No Category</span>
                                    @endif
                                </td>

                                {{-- STUDENT --}}
                                <td class="py-4 px-3 text-center">
                                    <div class="text-xs font-medium transition-colors duration-300"
                                        style="color: var(--text-primary);">
                                        {{ $service->user->hu_name ?? 'Unknown' }}
                                    </div>
                                </td>

                                {{-- AVG RATING --}}
                                <td class="py-4 px-3 text-center">
                                    <div class="tooltip">
                                        <span class="font-bold text-sm transition-colors duration-300"
                                            style="color: var(--text-primary);">
                                            {{ number_format($service->reviews_avg_rating ?? 0, 1) }}⭐
                                        </span>
                                        <span class="tooltip-text">{{ number_format($service->reviews_avg_rating ?? 0, 2) }}</span>
                                    </div>
                                </td>

                                {{-- REVIEWS --}}
                                <td class="py-4 px-3 text-center">
                                    <a href="{{ route('admin.services.reviews', $service->hss_id) }}"
                                        class="text-cyan-500 hover:text-cyan-400 transition-colors duration-300 text-xs font-medium">
                                        {{ $service->reviews_count ?? 0 }}
                                    </a>
                                </td>

                                {{-- WARNING --}}
                                <td class="py-4 px-3 text-center">
                                    <span
                                        class="font-mono font-bold text-sm {{ ($service->hss_warning_count ?? 0) >= 2 ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $service->hss_warning_count ?? 0 }}/3
                                    </span>
                                </td>

                                {{-- STATUS --}}
                                <td class="py-4 px-3 text-center">
                                    @if ($service->hss_approval_status == 'pending')
                                        <span
                                            class="px-2 py-1 rounded text-yellow-800 bg-yellow-100 text-xs font-semibold"
                                            data-status="pending">
                                            Pending
                                        </span>
                                    @elseif($service->hss_approval_status == 'approved')
                                        <span
                                            class="px-2 py-1 rounded text-green-800 bg-green-100 text-xs font-semibold"
                                            data-status="approved">
                                            Approved
                                        </span>
                                    @elseif($service->hss_approval_status == 'rejected')
                                        <span
                                            class="px-2 py-1 rounded text-red-800 bg-red-100 text-xs font-semibold"
                                            data-status="rejected">
                                            ✗ Rejected
                                        </span>
                                    @elseif($service->hss_approval_status == 'suspended')
                                        <span
                                            class="px-2 py-1 rounded text-gray-800 bg-gray-100 text-xs font-semibold"
                                            data-status="suspended">
                                            ⏸ Suspended
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 rounded text-gray-600 bg-gray-100 text-xs font-semibold">
                                            {{ $service->hss_approval_status ?? 'Unknown' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="py-4 px-3 text-center">
                                    <a href="{{ route('admin.services.show', $service->hss_id) }}" class="text-blue-500 hover:text-blue-700 transition-colors duration-300"
                                        title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($services->hasPages())
            <div class="mt-4 px-4">
                {{ $services->links() }}
            </div>
        @endif
    </div>

@endsection

<style>
    .modal {
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
    }
</style>

<script>
// Open Edit Modal
function openEditModal(formId, serviceId, status) {
    // Reset form
    document.getElementById(formId).reset();

    // Set service ID
    document.getElementById('editServiceId').value = serviceId;

    // Set current status
    document.getElementById('editStatus').value = status;

    // Show modal
    document.getElementById('editServiceModal').style.display = 'block';

    // Smooth appearance
    setTimeout(() => {
        document.querySelector('.modal-content').style.transform = 'translateY(0)';
        document.querySelector('.modal-content').style.opacity = '1';
    }, 50);
}

// Close Edit Modal
function closeEditModal(modalId) {
    document.querySelector('.modal-content').style.transform = 'translateY(-20px)';
    document.querySelector('.modal-content').style.opacity = '0';

    setTimeout(() => {
        document.getElementById(modalId).style.display = 'none';
    }, 300);
}

// Submit Edit Form
function submitEditForm() {
    const form = document.getElementById('editServiceForm');
    const formData = new FormData(form);

    // Disable submit button during submission
    const submitButton = document.getElementById('updateServiceButton');
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Updating...';

    fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success notification
                showNotification('Service updated successfully!', 'success');

                // Update the status button in the table
                const serviceId = document.getElementById('editServiceId').value;
                const newStatus = document.getElementById('editStatus').value;
                updateStatusInTable(serviceId, newStatus);

                // Close modal
                closeEditModal('editServiceModal');
            } else {
                // Show error notification
                showNotification(data.message || 'Error updating service.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error occurred. Please try again.', 'error');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML =
                '<i class="fa-solid fa-save mr-2"></i>Update Service';
        });

    return false; // Prevent default form submission
}

// Update status in table after successful update
function updateStatusInTable(serviceId, newStatus) {
    // Find the row with the service
    const rows = document.querySelectorAll('tr');
    for (let row of rows) {
        const statusElement = row.querySelector('[data-status]');
        if (statusElement) {
            // Check if this is the correct row by finding the view link
            const viewLink = row.querySelector(`a[href*="${serviceId}"]`);
            if (viewLink) {
                // Update the status element
                updateStatusElement(statusElement, newStatus);
                break;
            }
        }
    }
}

// Update individual status element
function updateStatusElement(element, newStatus) {
    // Clear existing classes
    element.className = 'px-2 py-1 rounded text-xs font-semibold';

    // Set new status and styling
    switch (newStatus) {
        case 'pending':
            element.className += ' text-yellow-800 bg-yellow-100';
            element.textContent = 'Pending';
            break;
        case 'approved':
            element.className += ' text-green-800 bg-green-100';
            element.textContent = 'Approved';
            break;
        case 'rejected':
            element.className += ' text-red-800 bg-red-100';
            element.textContent = '✗ Rejected';
            break;
        case 'suspended':
            element.className += ' text-gray-800 bg-gray-100';
            element.textContent = '⏸ Suspended';
            break;
    }

    // Update data attribute
    element.setAttribute('data-status', newStatus);
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;

    // Set styling based on type
    switch (type) {
        case 'success':
            notification.className += ' bg-green-500 text-white';
            break;
        case 'error':
            notification.className += ' bg-red-500 text-white';
            break;
        case 'warning':
            notification.className += ' bg-yellow-500 text-black';
            break;
        default:
            notification.className += ' bg-blue-500 text-white';
    }

    notification.textContent = message;

    // Add to page
    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target == modal) {
            closeEditModal(modal.id);
        }
    });
}

// Press Escape to close modals
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const visibleModals = Array.from(document.querySelectorAll('.modal')).filter(modal =>
            modal.style.display === 'block'
        );
        visibleModals.forEach(modal => {
            closeEditModal(modal.id);
        });
    }
});
</script>
