window.UServeAdmin.register('feedback', 'adminModuleFeedbackConfig', (config) => {
    const successMessage = config ? config.dataset.successMessage || '' : '';
    const errorMessage   = config ? config.dataset.errorMessage   || '' : '';
    const warningMessage = config ? config.dataset.warningMessage || '' : '';

    // --- Warning Modal ---
    const warnModal    = document.getElementById('feedbackWarningModal');
    const warnForm     = document.getElementById('feedbackWarningForm');
    const warnSubtitle = document.getElementById('feedbackWarningSubtitle');
    const warnBackdrop = document.getElementById('feedbackWarningBackdrop');
    const warnCancel   = document.getElementById('feedbackWarningCancel');
    const warnTextarea = document.getElementById('feedbackWarningReason');

    function openWarningModal(url, name, count, limit) {
        warnForm.action = url;
        warnSubtitle.textContent = `Issuing warning ${count}/${limit} to ${name}.`;
        warnTextarea.value = '';
        warnModal.classList.remove('hidden');
        setTimeout(() => warnTextarea.focus(), 100);
    }

    function closeWarningModal() {
        warnModal.classList.add('hidden');
        warnTextarea.value = '';
    }

    if (warnCancel)   warnCancel.addEventListener('click', closeWarningModal);
    if (warnBackdrop) warnBackdrop.addEventListener('click', closeWarningModal);

    // --- Enforce Modal ---
    const enforceModal    = document.getElementById('feedbackEnforceModal');
    const enforceForm     = document.getElementById('feedbackEnforceForm');
    const enforceSubtitle = document.getElementById('feedbackEnforceSubtitle');
    const enforceBackdrop = document.getElementById('feedbackEnforceBackdrop');
    const enforceCancel   = document.getElementById('feedbackEnforceCancel');
    const enforceSubmit   = document.getElementById('feedbackEnforceSubmit');
    const enforceTextarea = document.getElementById('feedbackEnforceReason');

    function openEnforceModal(url, name, action) {
        enforceForm.action = url;
        enforceSubtitle.textContent = `Action: "${action}" will be applied to ${name}.`;
        enforceSubmit.textContent = action;
        enforceTextarea.value = '';
        enforceModal.classList.remove('hidden');
        setTimeout(() => enforceTextarea.focus(), 100);
    }

    function closeEnforceModal() {
        enforceModal.classList.add('hidden');
        enforceTextarea.value = '';
    }

    if (enforceCancel)   enforceCancel.addEventListener('click', closeEnforceModal);
    if (enforceBackdrop) enforceBackdrop.addEventListener('click', closeEnforceModal);

    // --- Unblock Modal ---
    const unblockModal    = document.getElementById('feedbackUnblockModal');
    const unblockForm     = document.getElementById('feedbackUnblockForm');
    const unblockSubtitle = document.getElementById('feedbackUnblockSubtitle');
    const unblockBackdrop = document.getElementById('feedbackUnblockBackdrop');
    const unblockCancel   = document.getElementById('feedbackUnblockCancel');

    function openUnblockModal(url, name) {
        unblockForm.action = url;
        unblockSubtitle.textContent = `Restoring seller access for ${name}.`;
        unblockModal.classList.remove('hidden');
    }

    function closeUnblockModal() {
        unblockModal.classList.add('hidden');
    }

    if (unblockCancel)   unblockCancel.addEventListener('click', closeUnblockModal);
    if (unblockBackdrop) unblockBackdrop.addEventListener('click', closeUnblockModal);

    // --- Unsuspend Modal ---
    const unsuspendModal    = document.getElementById('feedbackUnsuspendModal');
    const unsuspendForm     = document.getElementById('feedbackUnsuspendForm');
    const unsuspendSubtitle = document.getElementById('feedbackUnsuspendSubtitle');
    const unsuspendBackdrop = document.getElementById('feedbackUnsuspendBackdrop');
    const unsuspendCancel   = document.getElementById('feedbackUnsuspendCancel');

    function openUnsuspendModal(url, name) {
        unsuspendForm.action = url;
        unsuspendSubtitle.textContent = `Restoring account access for ${name}.`;
        unsuspendModal.classList.remove('hidden');
    }

    function closeUnsuspendModal() {
        unsuspendModal.classList.add('hidden');
    }

    if (unsuspendCancel)   unsuspendCancel.addEventListener('click', closeUnsuspendModal);
    if (unsuspendBackdrop) unsuspendBackdrop.addEventListener('click', closeUnsuspendModal);

    // --- Escape key closes all modals ---
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeWarningModal();
            closeEnforceModal();
            closeUnblockModal();
            closeUnsuspendModal();
        }
    });

    // --- Click delegation ---
    document.addEventListener('click', (event) => {
        const warnTrigger = event.target.closest('[data-feedback-open-warning]');
        if (warnTrigger) {
            openWarningModal(
                warnTrigger.dataset.url,
                warnTrigger.dataset.name,
                warnTrigger.dataset.count,
                warnTrigger.dataset.limit
            );
            return;
        }

        const enforceTrigger = event.target.closest('[data-feedback-open-enforce]');
        if (enforceTrigger) {
            openEnforceModal(
                enforceTrigger.dataset.url,
                enforceTrigger.dataset.name,
                enforceTrigger.dataset.action
            );
            return;
        }

        const unblockTrigger = event.target.closest('[data-feedback-open-unblock]');
        if (unblockTrigger) {
            openUnblockModal(
                unblockTrigger.dataset.url,
                unblockTrigger.dataset.name
            );
            return;
        }

        const unsuspendTrigger = event.target.closest('[data-feedback-open-unsuspend]');
        if (unsuspendTrigger) {
            openUnsuspendModal(
                unsuspendTrigger.dataset.url,
                unsuspendTrigger.dataset.name
            );
            return;
        }
    });

    // --- Unblock / Unsuspend confirm (SweetAlert) ---
    document.addEventListener('submit', (event) => {
        const f = event.target.closest('form[data-confirm-message]');
        if (!f) return;
        event.preventDefault();
        const msg = f.getAttribute('data-confirm-message');
        Swal.fire({
            title: 'Are you sure?',
            text: msg,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, confirm',
            cancelButtonText: 'Cancel',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-lg', cancelButton: 'rounded-lg' },
        }).then((result) => {
            if (result.isConfirmed) f.submit();
        });
    });

    // --- Notification popups ---
    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: successMessage,
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-2xl' },
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: errorMessage,
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-2xl' },
        });
    }

    if (warningMessage) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: warningMessage,
            timer: 4000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-2xl' },
        });
    }
});

window.UServeAdmin.boot('feedback');
