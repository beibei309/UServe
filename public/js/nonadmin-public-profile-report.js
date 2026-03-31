(() => {
    const config = document.getElementById('publicProfileReportConfig');
    const openBtn = document.getElementById('open-report-user-modal');
    const modal = document.getElementById('report-user-modal');
    const overlay = document.getElementById('report-user-modal-overlay');
    const closeBtn = document.getElementById('close-report-user-modal');
    const cancelBtn = document.getElementById('cancel-report-user-modal');
    const form = document.getElementById('report-user-form');
    const submitBtn = document.getElementById('submit-report-user');
    const feedback = document.getElementById('report-user-feedback');

    if (!config || !openBtn || !modal || !form || !submitBtn || !feedback) {
        return;
    }

    const reportUrl = config.dataset.reportUrl || '/reports';
    const csrfToken = config.dataset.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    };

    openBtn.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        feedback.classList.add('hidden');
        feedback.textContent = '';
        feedback.className = 'hidden text-sm';

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

            setTimeout(closeModal, 800);
        } catch (error) {
            feedback.textContent = error.message || 'Unable to submit report.';
            feedback.className = 'text-sm text-red-600';
        } finally {
            feedback.classList.remove('hidden');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Report';
        }
    });
})();
