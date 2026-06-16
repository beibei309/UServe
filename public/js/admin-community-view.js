window.UServeAdmin.register('communityView', 'adminModuleCommunityViewConfig', (config) => {
    if (!config) return;

    const csrfToken = config.dataset.csrfToken || '';
    const blacklistRouteTemplate = config.dataset.blacklistRouteTemplate || '';
    const userId = config.dataset.userId || '';
    const lat = parseFloat(config.dataset.lat || '');
    const lng = parseFloat(config.dataset.lng || '');
    const userName = config.dataset.userName || 'User';
    let selectedUserId = null;

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
            window.UServeAdmin.alert({
                icon: 'warning',
                title: 'Reason Required',
                text: 'Please enter account suspended reason.',
            });
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = blacklistRouteTemplate.replace('ID_PLACEHOLDER', selectedUserId || userId);

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

    window.openDocumentModal = function (id) {
        const modal = document.getElementById('documentModal');
        const frame = document.getElementById('modalDocumentFrame');
        const loader = document.getElementById('docLoading');

        loader.classList.remove('hidden');
        const url = `/admin/verifications/${id}/document`;
        const cacheBuster = `cb=${Date.now()}`;
        frame.src = url + (url.includes('?') ? '&' : '?') + cacheBuster;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeDocumentModal = function () {
        const modal = document.getElementById('documentModal');
        const frame = document.getElementById('modalDocumentFrame');
        modal.classList.add('hidden');
        frame.src = '';
        document.body.style.overflow = 'auto';
    };

    window.openSelfieModal = function (imageUrl) {
        const modal = document.getElementById('selfieModal');
        const modalImg = document.getElementById('modalSelfieImage');
        modalImg.src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };

    window.closeSelfieModal = function () {
        const modal = document.getElementById('selfieModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    };

    const bootMap = (() => {
        let initialized = false;
        let attempts = 0;
        const maxAttempts = 40;
        const waitMs = 120;

        return function bootMapWhenReady() {
            if (initialized) return;
            attempts += 1;

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const mapEl = document.getElementById('map');
            if (!mapEl) return;

            if (typeof L === 'undefined') {
                if (attempts < maxAttempts) {
                    window.setTimeout(bootMapWhenReady, waitMs);
                }
                return;
            }

            initialized = true;

            const map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
            }).addTo(map);

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup(`<b>${userName}</b><br>Location Registered.`)
                .openPopup();
        };
    })();

    window.addEventListener('upsi2u:map-tools-ready', bootMap, { once: true });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') window.closeSelfieModal();
    });

    document.addEventListener('click', (event) => {
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

        const selfieOpen = event.target.closest('[data-selfie-open]');
        if (selfieOpen) {
            window.openSelfieModal(selfieOpen.dataset.selfieUrl);
            return;
        }

        if (event.target.closest('[data-selfie-close]')) {
            window.closeSelfieModal();
            return;
        }

        const documentOpen = event.target.closest('[data-document-open]');
        if (documentOpen) {
            const modal = document.getElementById('documentModal');
            const loader = document.getElementById('docLoading');
            if (modal) modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (loader) loader.classList.remove('hidden');

            const oldFrame = document.getElementById('modalDocumentFrame');
            if (oldFrame) {
                oldFrame.parentNode.removeChild(oldFrame);
            }

            const newFrame = document.createElement('iframe');
            newFrame.id = 'modalDocumentFrame';
            newFrame.className = 'w-full h-full border-none';
            newFrame.src = '';
            newFrame.addEventListener('load', () => {
                if (loader) loader.classList.add('hidden');
            });

            const container = modal.querySelector('.flex-grow.bg-slate-200.relative');
            if (container) {
                container.appendChild(newFrame);
            }

            setTimeout(() => {
                const url = documentOpen.dataset.documentUrl || '';
                const cacheBuster = `cb=${Date.now()}`;
                newFrame.src = url + (url.includes('?') ? '&' : '?') + cacheBuster;
            }, 50);
            return;
        }

        if (event.target.closest('[data-document-close]')) {
            window.closeDocumentModal();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const frame = document.getElementById('modalDocumentFrame');
        if (frame) {
            frame.addEventListener('load', () => {
                const loader = document.getElementById('docLoading');
                if (loader) loader.classList.add('hidden');
            });
        }

        bootMap();
    });
});

window.UServeAdmin.boot('communityView');
