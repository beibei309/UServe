window.UServeAdmin.register('communityIndex', 'adminModuleCommunityIndexConfig', (config) => {
    if (!config) return;

    const csrfToken = config.dataset.csrfToken || '';
    const blacklistRouteTemplate = config.dataset.blacklistRouteTemplate || '';
    const successMessage = config.dataset.successMessage || '';
    let selectedUserId = null;

    window.openReviewsModal = function (modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeReviewsModal = function (modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    };

    window.openBlacklistModal = function (id) {
        selectedUserId = id;
        document.getElementById('blacklistModal').classList.remove('hidden');
    };

    window.closeBlacklistModal = function () {
        document.getElementById('blacklistModal').classList.add('hidden');
        document.getElementById('blacklistReason').value = '';
    };

    window.submitBlacklist = function () {
        const reason = document.getElementById('blacklistReason').value.trim();
        if (!reason) {
            alert('Please enter account suspended reason.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = blacklistRouteTemplate.replace('ID_PLACEHOLDER', selectedUserId);

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrfToken;
        form.appendChild(token);

        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'blacklist_reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);

        document.body.appendChild(form);
        form.submit();
    };

    window.confirmUnblacklist = function (button) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This user will regain access to the platform.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reactivate user account.',
        }).then((result) => {
            if (result.isConfirmed) button.closest('form').submit();
        });
    };

    document.addEventListener('click', (event) => {
        const reviewOpen = event.target.closest('[data-reviews-open]');
        if (reviewOpen) {
            window.openReviewsModal(reviewOpen.dataset.modalId);
            return;
        }

        const reviewClose = event.target.closest('[data-reviews-close]');
        if (reviewClose) {
            window.closeReviewsModal(reviewClose.dataset.modalId);
            return;
        }

        const blacklistOpen = event.target.closest('[data-blacklist-open]');
        if (blacklistOpen) {
            window.openBlacklistModal(blacklistOpen.dataset.userId);
            return;
        }

        if (event.target.closest('[data-blacklist-close]')) {
            window.closeBlacklistModal();
            return;
        }

        if (event.target.closest('[data-blacklist-submit]')) {
            window.submitBlacklist();
            return;
        }

        const unblacklistConfirm = event.target.closest('[data-unblacklist-confirm]');
        if (unblacklistConfirm) {
            window.confirmUnblacklist(unblacklistConfirm);
        }
    });

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: successMessage,
            timer: 3000,
            showConfirmButton: false,
        });
    }
});

window.UServeAdmin.boot('communityIndex');
