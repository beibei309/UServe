@extends('admin.layout')

@section('title', 'All Rewards')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">All Rewards</h1>
            <p class="mt-1 transition-colors duration-300" style="color: var(--text-secondary);">Manage all reward offerings</p>
        </div>
        <a href="{{ route('admin.rewards.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 mt-4 sm:mt-0">
            <i class="fas fa-plus mr-2"></i>Create Reward
        </a>
    </div>

    <!-- Filters -->
    <div class="rounded-lg shadow mb-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
        <form method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search rewards..." 
                           class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                           style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Type</label>
                    <select name="type" class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                            style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <option value="">All Types</option>
                        <option value="discount" {{ request('type') === 'discount' ? 'selected' : '' }}>Discount</option>
                        <option value="service_credit" {{ request('type') === 'service_credit' ? 'selected' : '' }}>Service Credit</option>
                        <option value="voucher" {{ request('type') === 'voucher' ? 'selected' : '' }}>Voucher</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Status</label>
                    <select name="status" class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                            style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
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

    <!-- Rewards Table -->
    <div class="rounded-lg shadow overflow-hidden transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="transition-colors duration-300" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider transition-colors duration-300" style="color: var(--text-secondary);">Reward</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider transition-colors duration-300" style="color: var(--text-secondary);">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider transition-colors duration-300" style="color: var(--text-secondary);">Cost</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider transition-colors duration-300" style="color: var(--text-secondary);">Value</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider transition-colors duration-300" style="color: var(--text-secondary);">Redemptions</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider transition-colors duration-300" style="color: var(--text-secondary);">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider transition-colors duration-300" style="color: var(--text-secondary);">Actions</th>
                    </tr>
                </thead>
                <tbody class="transition-colors duration-300" style="border-color: var(--border-color);">
                    @forelse($rewards as $reward)
                    <tr class="hover:opacity-80 transition-all duration-200" style="border-bottom: 1px solid var(--border-color);">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $reward->hr_title }}</div>
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">{{ Str::limit($reward->hr_description, 50) }}</div>
                                @if($reward->hr_expires_at)
                                <div class="text-xs mt-1 transition-colors duration-300" style="color: var(--text-muted);">
                                    Expires: {{ $reward->hr_expires_at->format('M d, Y') }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                @if($reward->hr_type === 'discount') bg-blue-100 text-blue-800
                                @elseif($reward->hr_type === 'service_credit') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $reward->hr_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">{{ $reward->hr_points_cost }} pts</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium transition-colors duration-300" style="color: var(--text-primary);">RM {{ number_format($reward->hr_value, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium transition-colors duration-300 inline-flex items-baseline justify-center gap-1" style="color: var(--text-primary);">
                                {{ $reward->redemptions_count }}
                                @if($reward->hr_usage_limit)
                                    <span class="text-xs transition-colors duration-300" style="color: var(--text-secondary);">/ {{ $reward->hr_usage_limit }} limit</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $reward->ui_status_badge }}">
                                {{ $reward->ui_status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex justify-center items-center space-x-2">
                                <a href="{{ route('admin.rewards.edit', $reward) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.rewards.toggle-status', $reward) }}" 
                                      class="inline" title="{{ $reward->hr_is_active ? 'Deactivate' : 'Activate' }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-{{ $reward->hr_is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.rewards.destroy', $reward) }}" 
                                      class="inline delete-form" title="Delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center transition-colors duration-300" style="color: var(--text-secondary);">
                            No rewards found. <a href="{{ route('admin.rewards.create') }}" class="text-blue-600 hover:text-blue-800">Create your first reward</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rewards->hasPages())
        <div class="px-6 py-4 transition-colors duration-300" style="border-top: 1px solid var(--border-color);">
            {{ $rewards->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
    <div id="adminModuleRewardsListConfig"
        data-success-message="{{ session('success') }}"></div>
    <script src="{{ asset('js/admin-rewards-list.js') }}"></script>
@endsection