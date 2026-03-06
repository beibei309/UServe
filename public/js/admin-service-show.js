window.UServeAdmin.register('serviceShow', 'adminModuleServiceShowConfig', (config) => {
    if (!config) return;

    const csrfToken = config.dataset.csrfToken || '';
    const successMessage = config.dataset.successMessage || '';
    const errorMessage = config.dataset.errorMessage || '';
    const warningMessage = config.dataset.warningMessage || '';
    const warningLimit = parseInt(config.dataset.warningLimit || '3', 10);

    window.openWarningModal = function (url) {
        document.getElementById('warningForm').action = url;
        document.getElementById('warningModal').classList.remove('hidden');
    };

    window.closeWarningModal = function () {
        document.getElementById('warningModal').classList.add('hidden');
    };

    window.openRejectModal = function (url) {
        document.getElementById('rejectForm').action = url;
        document.getElementById('rejectModal').classList.remove('hidden');
    };

    window.closeRejectModal = function () {
        document.getElementById('rejectModal').classList.add('hidden');
    };

    function submitPatchForm(url) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrfToken;
        form.appendChild(token);

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }

    window.confirmSuspend = function (url) {
        Swal.fire({
            title: 'Suspend Service?',
            text: `This service will be suspended due to reaching maximum warnings (${warningLimit}/${warningLimit}). This action can be undone later.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Suspend Service',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg',
                cancelButton: 'rounded-lg',
            },
        }).then((result) => {
            if (result.isConfirmed) submitPatchForm(url);
        });
    };

    window.confirmUnblock = function (url) {
        Swal.fire({
            title: 'Reactivate Service?',
            text: 'This service will be reactivated and become available again to students.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reactivate',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg',
                cancelButton: 'rounded-lg',
            },
        }).then((result) => {
            if (result.isConfirmed) submitPatchForm(url);
        });
    };

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

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.closeRejectModal();
            window.closeWarningModal();
        }
    });

    document.addEventListener('click', (event) => {
        const openReject = event.target.closest('[data-service-open-reject]');
        if (openReject) {
            window.openRejectModal(openReject.dataset.url);
            return;
        }

        const openWarning = event.target.closest('[data-service-open-warning]');
        if (openWarning) {
            window.openWarningModal(openWarning.dataset.url);
            return;
        }

        if (event.target.closest('[data-service-close-reject]')) {
            window.closeRejectModal();
            return;
        }

        if (event.target.closest('[data-service-close-warning]')) {
            window.closeWarningModal();
            return;
        }

        const unblockTrigger = event.target.closest('[data-service-unblock]');
        if (unblockTrigger) {
            window.confirmUnblock(unblockTrigger.dataset.url);
            return;
        }

        const suspendTrigger = event.target.closest('[data-service-suspend]');
        if (suspendTrigger) {
            window.confirmSuspend(suspendTrigger.dataset.url);
        }
    });
});

window.UServeAdmin.boot('serviceShow');
