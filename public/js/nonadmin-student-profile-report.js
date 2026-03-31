(() => {
    const config = document.getElementById('studentProfileReportConfig');
    const openBtn = document.getElementById('open-student-report-modal');
    const modal = document.getElementById('student-report-modal');
    const overlay = document.getElementById('student-report-modal-overlay');
    const closeBtn = document.getElementById('close-student-report-modal');
    const cancelBtn = document.getElementById('cancel-student-report-modal');
    const form = document.getElementById('student-report-form');
    const submitBtn = document.getElementById('submit-student-report');
    const feedback = document.getElementById('student-report-feedback');

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
        feedback.className = 'hidden text-sm';
        feedback.textContent = '';
        form.reset();
    };

    openBtn.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

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
            if (feedback.textContent.trim() !== '') {
                feedback.classList.remove('hidden');
            }
        }
    });
})();
