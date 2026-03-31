(() => {
    if (window.__adminReportsResolveBound) {
        return;
    }
    window.__adminReportsResolveBound = true;

    function showNotification(message, type = 'success') {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Done' : 'Error',
            text: message,
            timer: type === 'success' ? 2000 : undefined,
            showConfirmButton: type !== 'success',
        });
    }

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('.report-resolve-form');
        if (!form) return;

        event.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn || submitBtn.disabled) return;

        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Updating...';

        try {
            const formData = new FormData(form);
            const reportId = form.dataset.reportId;
            const url = form.dataset.url || `/admin/reports/${reportId}/resolve`;

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
                credentials: 'same-origin',
            });

            const contentType = response.headers.get('content-type') || '';
            const result = contentType.includes('application/json')
                ? await response.json()
                : { success: false, message: 'Unexpected server response format' };

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to update report');
            }

            showNotification(result.message || 'Report updated successfully', 'success');

            const card = form.closest('.report-card');
            if (card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'translateX(100%)';

                setTimeout(() => {
                    card.remove();

                    const remainingCards = document.querySelectorAll('.report-card').length;
                    if (remainingCards === 0) {
                        location.reload();
                    }
                }, 300);
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message || 'An error occurred while updating report', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
})();
