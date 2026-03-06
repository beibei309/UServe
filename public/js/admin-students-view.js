window.UServeAdmin.register('studentsView', 'adminModuleStudentsViewConfig', (config) => {
    if (!config) return;

    const banRouteTemplate = config.dataset.banRouteTemplate || '';
    const selfieBaseUrl = config.dataset.selfieBaseUrl || '';
    let selectedStudentId = null;

    window.openSelfieModal = function (imageUrl) {
        const modal = document.getElementById('selfieModal');
        const modalImg = document.getElementById('modalSelfieImage');
        if (!modal || !modalImg) return;
        modalImg.src = imageUrl;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeSelfieModal = function () {
        const modal = document.getElementById('selfieModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('modalSelfieImage').src = '';
    };

    window.openBanModal = function (id) {
        selectedStudentId = id;
        document.getElementById('banModal').classList.remove('hidden');
    };

    window.closeBanModal = function () {
        document.getElementById('banModal').classList.add('hidden');
        document.getElementById('banReason').value = '';
    };

    window.submitBan = function () {
        const reason = document.getElementById('banReason').value.trim();
        if (!reason) {
            alert('Please enter a reason.');
            return;
        }

        const form = document.getElementById('banForm');
        const csrfValue = form.querySelector('input[name="_token"]')?.value || '';
        form.action = banRouteTemplate.replace('ID_PLACEHOLDER', selectedStudentId);
        form.innerHTML = `<input type="hidden" name="_token" value="${csrfValue}"><input type="hidden" name="blacklist_reason" value="${reason}">`;
        form.submit();
    };

    window.openHelperSelfieModal = function (studentId) {
        const modal = document.createElement('div');
        modal.id = 'helperSelfieModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="relative max-w-4xl max-h-full">
                <button type="button" data-helper-selfie-close
                        class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">×</button>
                <img src="${selfieBaseUrl}/${studentId}/selfie"
                     class="max-w-full max-h-[90vh] rounded-lg shadow-2xl"
                     alt="Helper Verification Selfie">
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) modal.remove();
        });
    };

    document.addEventListener('click', (event) => {
        const banOpen = event.target.closest('[data-ban-open]');
        if (banOpen) {
            window.openBanModal(banOpen.dataset.studentId);
            return;
        }

        if (event.target.closest('[data-ban-close]')) {
            window.closeBanModal();
            return;
        }

        if (event.target.closest('[data-ban-submit]')) {
            window.submitBan();
            return;
        }

        const selfieOpen = event.target.closest('[data-selfie-open]');
        if (selfieOpen) {
            window.openSelfieModal(selfieOpen.dataset.selfieUrl);
            return;
        }

        if (event.target.closest('[data-selfie-close]')) {
            window.closeSelfieModal();
            return;
        }

        const helperSelfieClose = event.target.closest('[data-helper-selfie-close]');
        if (helperSelfieClose) {
            const helperModal = document.getElementById('helperSelfieModal');
            if (helperModal) helperModal.remove();
            return;
        }

        const submitButton = event.target.closest('button[data-confirm-message]');
        if (submitButton) {
            const message = submitButton.dataset.confirmMessage || 'Are you sure?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') window.closeSelfieModal();
    });
});

window.UServeAdmin.boot('studentsView');
