@extends('admin.layout')

@section('title', 'Edit Reward')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Edit Reward</h1>
            <p class="mt-1 transition-colors duration-300" style="color: var(--text-secondary);">Update reward: {{ $reward->hr_title }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.rewards.list') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="rounded-lg shadow transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
        <form method="POST" action="{{ route('admin.rewards.update', $reward) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Basic Information</h3>
                </div>
                
                <div>
                    <label for="hr_title" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Reward Title *</label>
                    <input type="text" id="hr_title" name="hr_title" value="{{ old('hr_title', $reward->hr_title) }}" required
                           class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                           @error('hr_title') border-red-500 @enderror"
                           style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    @error('hr_title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hr_type" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Reward Type *</label>
                    <select id="hr_type" name="hr_type" required
                            class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                            @error('hr_type') border-red-500 @enderror"
                            style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <option value="">Select Type</option>
                        <option value="discount" {{ old('hr_type', $reward->hr_type) === 'discount' ? 'selected' : '' }}>Discount</option>
                        <option value="service_credit" {{ old('hr_type', $reward->hr_type) === 'service_credit' ? 'selected' : '' }}>Service Credit</option>
                        <option value="voucher" {{ old('hr_type', $reward->hr_type) === 'voucher' ? 'selected' : '' }}>Voucher</option>
                    </select>
                    @error('hr_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-2">
                    <label for="hr_description" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Description *</label>
                    <textarea id="hr_description" name="hr_description" rows="4" required
                              class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                              @error('hr_description') border-red-500 @enderror"
                              style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">{{ old('hr_description', $reward->hr_description) }}</textarea>
                    @error('hr_description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pricing & Values -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4 mt-8 transition-colors duration-300" style="color: var(--text-primary);">Pricing & Values</h3>
                </div>

                <div>
                    <label for="hr_points_cost" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Points Cost *</label>
                    <input type="number" id="hr_points_cost" name="hr_points_cost" value="{{ old('hr_points_cost', $reward->hr_points_cost) }}" 
                           min="1" required
                           class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                           @error('hr_points_cost') border-red-500 @enderror"
                           style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    @error('hr_points_cost')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hr_value" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Monetary Value (RM) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 transition-colors duration-300" style="color: var(--text-muted);">RM</span>
                        <input type="number" id="hr_value" name="hr_value" value="{{ old('hr_value', $reward->hr_value) }}" 
                               step="0.01" min="0" required
                               class="w-full rounded-lg pl-12 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                               @error('hr_value') border-red-500 @enderror"
                               style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    @error('hr_value')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hr_code_prefix" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Code Prefix *</label>
                    <input type="text" id="hr_code_prefix" name="hr_code_prefix" value="{{ old('hr_code_prefix', $reward->hr_code_prefix) }}" 
                           maxlength="20" required placeholder="e.g., DISC, VOUCHER, CREDIT"
                           class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                           @error('hr_code_prefix') border-red-500 @enderror"
                           style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    @error('hr_code_prefix')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-muted);">This will be used to generate unique redemption codes</p>
                </div>

                <!-- Usage Limits -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4 mt-8 transition-colors duration-300" style="color: var(--text-primary);">Usage Limits</h3>
                </div>

                <div>
                    <label for="hr_usage_limit" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Total Usage Limit</label>
                    <input type="number" id="hr_usage_limit" name="hr_usage_limit" value="{{ old('hr_usage_limit', $reward->hr_usage_limit) }}" 
                           min="1" placeholder="Leave empty for unlimited"
                           class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                           @error('hr_usage_limit') border-red-500 @enderror"
                           style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    @error('hr_usage_limit')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-muted);">Maximum number of times this reward can be redeemed by all users</p>
                </div>

                <div>
                    <label for="hr_user_limit" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Per-User Limit *</label>
                    <input type="number" id="hr_user_limit" name="hr_user_limit" value="{{ old('hr_user_limit', $reward->hr_user_limit) }}" 
                           min="1" required
                           class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                           @error('hr_user_limit') border-red-500 @enderror"
                           style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    @error('hr_user_limit')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-muted);">Maximum number of times each user can redeem this reward</p>
                </div>

                <!-- Expiration -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4 mt-8 transition-colors duration-300" style="color: var(--text-primary);">Expiration</h3>
                </div>

                <div class="lg:col-span-2">
                    <label for="hr_expires_at" class="block text-sm font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Expiration Date</label>
                    <input type="datetime-local" id="hr_expires_at" name="hr_expires_at" 
                           value="{{ old('hr_expires_at', $reward->hr_expires_at ? $reward->hr_expires_at->format('Y-m-d\\TH:i') : '') }}"
                           class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300
                           @error('hr_expires_at') border-red-500 @enderror"
                           style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    @error('hr_expires_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm mt-1 transition-colors duration-300" style="color: var(--text-muted);">Leave empty if reward doesn't expire</p>
                </div>

                <!-- Terms and Conditions -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4 mt-8 transition-colors duration-300" style="color: var(--text-primary);">Terms and Conditions</h3>
                </div>

                <div class="lg:col-span-2">
                    <div id="terms-container">
                        <div class="flex items-center mb-4">
                            <label class="block text-sm font-medium mr-4 transition-colors duration-300" style="color: var(--text-secondary);">Terms & Conditions</label>
                            <button type="button" id="add-term" 
                                    class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition duration-200">
                                <i class="fas fa-plus mr-1"></i>Add Term
                            </button>
                        </div>
                        
                        <div id="terms-list" class="space-y-2">
                            @php
                                $terms = old('hr_terms', $reward->hr_terms ?: []);
                                if (empty($terms)) {
                                    $terms = [''];
                                }
                            @endphp
                            
                            @foreach($terms as $index => $term)
                            <div class="flex items-center space-x-2 term-row">
                                <input type="text" name="hr_terms[]" value="{{ $term }}" 
                                       class="flex-1 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                                       style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);"
                                       placeholder="Enter a term or condition">
                                <button type="button" class="text-red-600 hover:text-red-800 remove-term">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Reward Status -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4 mt-8 transition-colors duration-300" style="color: var(--text-primary);">Status</h3>
                    <div class="flex items-center">
                        <input type="hidden" name="hr_is_active" value="0">
                        <input type="checkbox" id="hr_is_active" name="hr_is_active" value="1" 
                               {{ old('hr_is_active', $reward->hr_is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500 transition-colors duration-300"
                               style="border-color: var(--border-color);">
                        <label for="hr_is_active" class="ml-2 text-sm transition-colors duration-300" style="color: var(--text-primary);">
                            Reward is active and available for redemption
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 transition-colors duration-300" style="border-top: 1px solid var(--border-color);">
                <a href="{{ route('admin.rewards.list') }}" 
                   class="px-6 py-2 rounded-lg transition duration-200"
                   style="background-color: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color);">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Update Reward
                </button>
            </div>
        </form>
    </div>

    <!-- Redemption Statistics -->
    @if($reward->redemptions()->count() > 0)
    <div class="rounded-lg shadow mt-6 transition-colors duration-300" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
        <div class="px-6 py-4 transition-colors duration-300" style="border-bottom: 1px solid var(--border-color);">
            <h3 class="text-lg font-semibold transition-colors duration-300" style="color: var(--text-primary);">Redemption Statistics</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $reward->redemptions()->count() }}</div>
                    <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Total Redemptions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $reward->redemptions()->where('hrr_status', 'approved')->count() }}</div>
                    <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Approved</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $reward->redemptions()->where('hrr_status', 'pending')->count() }}</div>
                    <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">Pending</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const termsContainer = document.getElementById('terms-list');
    const addTermBtn = document.getElementById('add-term');

    // Add new term input
    addTermBtn.addEventListener('click', function() {
        const newTermRow = document.createElement('div');
        newTermRow.className = 'flex items-center space-x-2 term-row';
        newTermRow.innerHTML = `
            <input type="text" name="hr_terms[]" 
                   class="flex-1 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                   style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);"
                   placeholder="Enter a term or condition">
            <button type="button" class="text-red-600 hover:text-red-800 remove-term">
                <i class="fas fa-times"></i>
            </button>
        `;
        termsContainer.appendChild(newTermRow);
    });

    // Remove term input
    termsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-term') || e.target.closest('.remove-term')) {
            const termRows = termsContainer.querySelectorAll('.term-row');
            if (termRows.length > 1) {
                e.target.closest('.term-row').remove();
            }
        }
    });
});
</script>
@endsection