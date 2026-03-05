window.UServeAdmin.register('verificationsIndex', 'adminModuleVerificationsIndexConfig', (config) => {
    if (!config) return;

    const selfieUrlTemplate = config.dataset.selfieUrlTemplate || '';
    const documentUrlTemplate = config.dataset.documentUrlTemplate || '';

    function buildUrl(template, userId) {
        return template.replace('USER_ID_PLACEHOLDER', userId);
    }

    function removeModalById(id) {
        const modal = document.getElementById(id);
        if (modal) modal.remove();
    }

    window.openSelfieModal = function (userId) {
        removeModalById('selfieModal');
        const modal = document.createElement('div');
        modal.id = 'selfieModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
        modal.onclick = () => modal.remove();
        modal.innerHTML = `
        <div class="relative max-w-4xl max-h-full">
            <button type="button" data-close-modal="selfieModal" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">&times;</button>
            <img src="${buildUrl(selfieUrlTemplate, userId)}" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl" alt="Selfie">
        </div>
    `;
        document.body.appendChild(modal);
    };

    window.openDocumentModal = function (userId) {
        removeModalById('documentModal');
        const modal = document.createElement('div');
        modal.id = 'documentModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
        modal.onclick = () => modal.remove();
        modal.innerHTML = `
        <div class="relative max-w-6xl max-h-full w-full">
            <button type="button" data-close-modal="documentModal" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">&times;</button>
            <iframe src="${buildUrl(documentUrlTemplate, userId)}" class="w-full h-[90vh] bg-white rounded-lg shadow-2xl"></iframe>
        </div>
    `;
        document.body.appendChild(modal);
    };

    document.addEventListener('click', (event) => {
        const selfieTrigger = event.target.closest('[data-verification-selfie]');
        if (selfieTrigger) {
            window.openSelfieModal(selfieTrigger.dataset.userId);
            return;
        }

        const documentTrigger = event.target.closest('[data-verification-document]');
        if (documentTrigger) {
            window.openDocumentModal(documentTrigger.dataset.userId);
            return;
        }

        const closeButton = event.target.closest('[data-close-modal]');
        if (!closeButton) return;
        event.stopPropagation();
        const modalId = closeButton.getAttribute('data-close-modal');
        removeModalById(modalId);
    });
});

window.UServeAdmin.boot('verificationsIndex');
