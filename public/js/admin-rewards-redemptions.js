window.UServeAdmin.register('rewardsRedemptions', 'adminModuleRewardsRedemptionsConfig', (config) => {
    if (!config) return;

    const successMessage = config.dataset.successMessage || '';
    const routeTemplate = config.dataset.updateStatusRouteTemplate || '';

    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    const statusSelect = document.getElementById('status');
    const notesTextarea = document.getElementById('notes');

    if (successMessage) {
        Swal.fire({
            title: 'Success!',
            text: successMessage,
            icon: 'success',
            confirmButtonText: 'OK',
        });
    }

    function openStatusModal(redemptionId, currentStatus, currentNotes) {
        if (!modal || !form || !statusSelect || !notesTextarea || !routeTemplate) return;

        form.action = routeTemplate.replace('REDEMPTION_ID', redemptionId);
        statusSelect.value = currentStatus || 'pending';
        notesTextarea.value = currentNotes || '';
        modal.classList.remove('hidden');
    }

    function closeStatusModal() {
        if (!modal) return;
        modal.classList.add('hidden');
    }

    document.addEventListener('click', (event) => {
        const openTrigger = event.target.closest('[data-redemption-open-status]');
        if (openTrigger) {
            openStatusModal(
                openTrigger.dataset.redemptionId,
                openTrigger.dataset.currentStatus,
                openTrigger.dataset.currentNotes
            );
            return;
        }

        if (event.target.closest('[data-redemption-close-status]')) {
            closeStatusModal();
            return;
        }

        if (modal && event.target === modal) {
            closeStatusModal();
        }
    });
});

window.UServeAdmin.boot('rewardsRedemptions');
