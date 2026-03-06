(() => {
    const showNotification = (message, type = 'success') => {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.remove();
        }, 3000);
    };

    const toggleFavorite = async (btn) => {
        const userId = Number(btn.dataset.userId || 0);
        if (!userId) return;
        const icon = document.getElementById(`favorite-icon-${userId}`);
        const text = document.getElementById(`favorite-text-${userId}`);

        btn.disabled = true;
        try {
            const response = await fetch('/favorites/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ user_id: userId }),
            });
            const data = await response.json();
            if (!data.success) {
                showNotification(data.message || 'Failed to update favorite', 'error');
                return;
            }
            if (data.favorited) {
                btn.classList.remove('border-gray-300', 'text-gray-700', 'bg-white', 'hover:bg-gray-50', 'focus:ring-indigo-500');
                btn.classList.add('border-red-300', 'text-red-700', 'bg-red-50', 'hover:bg-red-100', 'focus:ring-red-500');
                icon?.setAttribute('fill', 'currentColor');
                icon?.classList.add('fill-current', 'text-red-600');
                if (text) text.textContent = 'Remove from Favorites';
                btn.dataset.favorited = 'true';
                btn.setAttribute('aria-pressed', 'true');
            } else {
                btn.classList.remove('border-red-300', 'text-red-700', 'bg-red-50', 'hover:bg-red-100', 'focus:ring-red-500');
                btn.classList.add('border-gray-300', 'text-gray-700', 'bg-white', 'hover:bg-gray-50', 'focus:ring-indigo-500');
                icon?.setAttribute('fill', 'none');
                icon?.classList.remove('fill-current', 'text-red-600');
                if (text) text.textContent = 'Add to Favorites';
                btn.dataset.favorited = 'false';
                btn.setAttribute('aria-pressed', 'false');
            }
            showNotification(data.message, 'success');
        } catch (error) {
            showNotification('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
        }
    };

    document.querySelectorAll('[data-favorite-button]').forEach((btn) => {
        if (btn.dataset.favoriteBound === 'true') return;
        btn.dataset.favoriteBound = 'true';
        btn.addEventListener('click', () => {
            toggleFavorite(btn);
        });
    });
})();
