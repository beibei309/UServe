@extends('admin.layout')

@section('title', 'Reward Redemptions')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Reward Redemptions</h1>
            <p class="mt-1 transition-colors duration-300" style="color: var(--text-secondary);">Manage all reward redemptions and their statuses</p>
        </div>
        <div class="flex space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.rewards.export-redemptions') }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
            <a href="{{ route('admin.rewards.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-lg shadow mb-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
        <form method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="User name or redemption code..." 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Used</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reward Type</label>
                    <select name="reward_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="discount" {{ request('reward_type') === 'discount' ? 'selected' : '' }}>Discount</option>
                        <option value="service_credit" {{ request('reward_type') === 'service_credit' ? 'selected' : '' }}>Service Credit</option>
                        <option value="voucher" {{ request('reward_type') === 'voucher' ? 'selected' : '' }}>Voucher</option>
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

    <!-- Redemptions Table -->
    <div class="rounded-lg shadow overflow-hidden transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="transition-colors duration-300" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User & Reward</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Redemption Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points Used</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="transition-colors duration-300" style="border-color: var(--border-color);">
                    @forelse($redemptions as $redemption)
                    <tr class="hover:opacity-80 transition-all duration-200" style="border-bottom: 1px solid var(--border-color);">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                    {{ $redemption->user->hu_name ?? 'Unknown User' }}
                                </div>
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                                    {{ $redemption->user->email ?? 'No email' }}
                                </div>
                                <div class="text-sm font-medium text-blue-600 mt-1">
                                    {{ $redemption->reward->hr_title ?? 'Deleted Reward' }}
                                </div>
                                @if($redemption->reward)
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full mt-1
                                    @if($redemption->reward->hr_type === 'discount') bg-blue-100 text-blue-800
                                    @elseif($redemption->reward->hr_type === 'service_credit') bg-green-100 text-green-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $redemption->reward->hr_type)) }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                {{ $redemption->hrr_redemption_code }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                {{ $redemption->hrr_points_used }} points
                            </div>
                            @if($redemption->reward)
                            <div class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">
                                Value: RM{{ number_format($redemption->reward->hr_value, 2) }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                @if($redemption->hrr_status === 'approved') bg-green-100 text-green-800
                                @elseif($redemption->hrr_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($redemption->hrr_status === 'rejected') bg-red-100 text-red-800
                                @elseif($redemption->hrr_status === 'used') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($redemption->hrr_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm transition-colors duration-300" style="color: var(--text-primary);">
                            <div>
                                <strong>Redeemed:</strong> {{ $redemption->hrr_redeemed_at ? $redemption->hrr_redeemed_at->format('M d, Y H:i') : $redemption->created_at->format('M d, Y H:i') }}
                            </div>
                            @if($redemption->hrr_expires_at)
                            <div class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">
                                <strong>Expires:</strong> {{ $redemption->hrr_expires_at->format('M d, Y H:i') }}
                            </div>
                            @endif
                            @if($redemption->hrr_used_at)
                            <div class="text-xs text-green-600">
                                <strong>Used:</strong> {{ $redemption->hrr_used_at->format('M d, Y H:i') }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button type="button"
                                    data-redemption-open-status
                                    data-redemption-id="{{ $redemption->hrr_id }}"
                                    data-current-status="{{ $redemption->hrr_status }}"
                                    data-current-notes="{{ $redemption->hrr_notes ?? '' }}"
                                    class="text-blue-600 hover:text-blue-900" title="Update Status">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    @if($redemption->hrr_notes)
                    <tr class="transition-colors duration-300" style="background-color: var(--bg-secondary);">
                        <td colspan="6" class="px-6 py-2 text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                            <strong>Notes:</strong> {{ $redemption->hrr_notes }}
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center transition-colors duration-300" style="color: var(--text-secondary);">
                            No redemptions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($redemptions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $redemptions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md transition-colors duration-300" style="background-color: var(--bg-primary); border-color: var(--border-color);">
        <div class="mt-3">
            <h3 class="text-lg font-medium mb-4 transition-colors duration-300" style="color: var(--text-primary);">Update Redemption Status</h3>
            <form id="statusForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Status</label>
                    <select id="status" name="status" required
                            class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                            style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="used">Used</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="3" 
                              class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                              style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);"
                              placeholder="Add any notes about this status update..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" data-redemption-close-status
                            class="px-4 py-2 rounded-lg transition-all duration-200"
                            style="background-color: var(--bg-tertiary); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <div id="adminModuleRewardsRedemptionsConfig"
        data-success-message="{{ session('success') }}"
        data-update-status-route-template="{{ route('admin.rewards.redemptions.update-status', 'REDEMPTION_ID') }}"></div>
    <script src="{{ asset('js/admin-rewards-redemptions.js') }}"></script>
@endsection