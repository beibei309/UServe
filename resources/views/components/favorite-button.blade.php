@props(['userId', 'isFavorited' => false])

<button
    onclick="toggleFavorite({{ $userId }})"
    id="favorite-btn-{{ $userId }}"
    aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
    {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-3 rounded-lg border text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 ' . ($isFavorited ? 'border-red-300 text-red-700 bg-red-50 hover:bg-red-100 focus:ring-red-500' : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:ring-indigo-500')]) }}
    data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
>
    <svg id="favorite-icon-{{ $userId }}" class="h-5 w-5 {{ $isFavorited ? 'fill-current text-red-600' : 'text-gray-700' }}" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
    <span id="favorite-text-{{ $userId }}" class="ml-2">
        {{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}
    </span>
</button>

@once
@push('scripts')
<script>
async function toggleFavorite(userId) {
    const btn = document.getElementById(`favorite-btn-${userId}`);
    const icon = document.getElementById(`favorite-icon-${userId}`);
    const text = document.getElementById(`favorite-text-${userId}`);
    
    // Disable button during request
    btn.disabled = true;
    
    try {
        const response = await fetch('/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        });

        const data = await response.json();

        if (data.success) {
            // Update button state
            if (data.favorited) {
                btn.classList.remove('border-gray-300', 'text-gray-700', 'bg-white', 'hover:bg-gray-50', 'focus:ring-indigo-500');
                btn.classList.add('border-red-300', 'text-red-700', 'bg-red-50', 'hover:bg-red-100', 'focus:ring-red-500');
                icon.setAttribute('fill', 'currentColor');
                icon.classList.add('fill-current', 'text-red-600');
                text.textContent = 'Remove from Favorites';
                btn.dataset.favorited = 'true';
                btn.setAttribute('aria-pressed', 'true');
            } else {
                btn.classList.remove('border-red-300', 'text-red-700', 'bg-red-50', 'hover:bg-red-100', 'focus:ring-red-500');
                btn.classList.add('border-gray-300', 'text-gray-700', 'bg-white', 'hover:bg-gray-50', 'focus:ring-indigo-500');
                icon.setAttribute('fill', 'none');
                icon.classList.remove('fill-current', 'text-red-600');
                text.textContent = 'Add to Favorites';
                btn.dataset.favorited = 'false';
                btn.setAttribute('aria-pressed', 'false');
            }

            // Show success message (optional)
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Failed to update favorite', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    } finally {
        btn.disabled = false;
    }
}

function showNotification(message, type = 'success') {
    // Create a simple notification
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
@endonce
