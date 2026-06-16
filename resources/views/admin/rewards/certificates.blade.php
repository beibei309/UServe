@extends('admin.layout')

@section('title', 'Certificates')

@section('content')
<div class="admin-list-page">
    <!-- Header -->
    <div class="admin-list-header">
        <div>
            <h1 class="admin-list-title">Certificates</h1>
            <p class="admin-list-subtitle">View all certificate achievements issued to helpers.</p>
        </div>
        <div class="admin-list-actions">
            <a href="{{ route('admin.rewards.export-certificates', request()->query()) }}" class="admin-export-action">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="admin-filter-card">
        <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="User name or certificate number..." 
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500"
                            style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">All Statuses</option>
                        <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200 w-full">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Certificates Table -->
    <div class="admin-table-card">
        <div class="admin-table-scroll">
            <table class="admin-table">
                <thead class="transition-colors duration-300" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points Used</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued / Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="transition-colors duration-300" style="border-color: var(--border-color);">
                    @forelse($certificates as $certificate)
                    <tr class="hover:opacity-80 transition-all duration-200" style="border-bottom: 1px solid var(--border-color);">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                    {{ $certificate->user->hu_name ?? 'Unknown User' }}
                                </div>
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                                    {{ $certificate->user->hu_email ?? $certificate->user->email ?? 'No email' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                {{ $certificate->hcr_certificate_number }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                @if($certificate->hcr_status === 'issued') bg-green-100 text-green-800
                                @elseif($certificate->hcr_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($certificate->hcr_status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($certificate->hcr_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                {{ $certificate->hcr_points_used }} points
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm transition-colors duration-300" style="color: var(--text-primary);">
                            <div>
                                <strong>Created:</strong> {{ $certificate->created_at?->format('M d, Y H:i') ?? '-' }}
                            </div>
                            @if($certificate->hcr_issued_at)
                            <div class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">
                                <strong>Issued:</strong> {{ $certificate->hcr_issued_at->format('M d, Y H:i') }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($certificate->hcr_status === 'issued')
                                <a href="{{ route('admin.rewards.certificates.show', $certificate) }}"
                                   class="text-blue-600 hover:text-blue-900" title="View Certificate">
                                    <i class="fas fa-external-link-alt mr-1"></i>View
                                </a>
                            @else
                                <span class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">No actions</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center transition-colors duration-300" style="color: var(--text-secondary);">
                            No certificates found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certificates->hasPages())
        <div class="admin-pagination">
            {{ $certificates->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
