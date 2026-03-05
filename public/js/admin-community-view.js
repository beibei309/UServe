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
            alert('Please enter account suspended reason.');
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
        frame.src = `/admin/verifications/${id}/document`;

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
            const frame = document.getElementById('modalDocumentFrame');
            const loader = document.getElementById('docLoading');
            if (loader) loader.classList.remove('hidden');
            if (frame) frame.src = documentOpen.dataset.documentUrl || '';
            if (modal) modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
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
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
        if (typeof L === 'undefined') return;
        const mapEl = document.getElementById('map');
        if (!mapEl) return;

        const map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
        }).addTo(map);

        const userIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            iconSize: [38, 38],
            iconAnchor: [19, 38],
            popupAnchor: [0, -38],
        });

        L.marker([lat, lng], { icon: userIcon })
            .addTo(map)
            .bindPopup(`<b>${userName}</b><br>Location Registered.`)
            .openPopup();
    });
});

window.UServeAdmin.boot('communityView');
