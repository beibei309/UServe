(() => {
    const config = document.getElementById('servicesIndexReportConfig');
    const modal = document.getElementById('reportUserModal');
    const form = document.getElementById('reportUserForm');
    const feedback = document.getElementById('reportUserFeedback');
    const submitBtn = document.getElementById('reportUserSubmitBtn');
    const targetIdInput = document.getElementById('reportUserTargetId');
    const targetNameEl = document.getElementById('reportUserTargetName');
    const closeButtons = document.querySelectorAll('[data-close-report-user]');

    if (!config || !modal || !form || !feedback || !submitBtn || !targetIdInput || !targetNameEl) {
        return;
    }

    const reportUrl = config.dataset.reportUrl || '/reports';
    const csrfToken = config.dataset.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';

    const openModal = (targetId, targetName) => {
        targetIdInput.value = targetId || '';
        targetNameEl.textContent = targetName || '-';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        feedback.className = 'hidden text-sm';
        feedback.textContent = '';
        form.reset();
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-report-user-trigger]');
        if (trigger) {
            openModal(trigger.dataset.targetUserId, trigger.dataset.targetUserName);
        }
    });

    closeButtons.forEach((button) => button.addEventListener('click', closeModal));

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        feedback.className = 'hidden text-sm';
        feedback.textContent = '';

        try {
            const response = await fetch(reportUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
                credentials: 'same-origin',
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to submit report.');
            }

            feedback.textContent = data.message || 'Report submitted successfully.';
            feedback.className = 'text-sm text-emerald-600';
            form.reset();
            setTimeout(closeModal, 900);
        } catch (error) {
            feedback.textContent = error.message || 'Unable to submit report.';
            feedback.className = 'text-sm text-red-600';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Report';
        }
    });
})();
