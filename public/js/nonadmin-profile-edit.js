(() => {
    const config = document.getElementById('profileEditConfig');
    if (!config || config.dataset.bound === 'true') return;
    config.dataset.bound = 'true';

    const initialTab = config.dataset.initialTab || 'profile';
    const autoHideMs = Number(config.dataset.autohideMs || 3000);
    const root = document.getElementById('profileEditRoot');
    if (!root) return;

    const tabButtons = Array.from(root.querySelectorAll('[data-profile-tab-button]'));
    const panels = Array.from(root.querySelectorAll('[data-profile-tab-panel]'));

    const setActiveTab = (tab) => {
        tabButtons.forEach((button) => {
            const activeClass = button.dataset.activeClass || '';
            const inactiveClass = button.dataset.inactiveClass || '';
            const isActive = button.dataset.profileTabButton === tab;
            activeClass.split(' ').filter(Boolean).forEach((className) => {
                button.classList.toggle(className, isActive);
            });
            inactiveClass.split(' ').filter(Boolean).forEach((className) => {
                button.classList.toggle(className, !isActive);
            });
        });

        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.profileTabPanel !== tab);
        });
    };

    root.addEventListener('click', (event) => {
        const button = event.target.closest('[data-profile-tab-button]');
        if (!button) return;
        setActiveTab(button.dataset.profileTabButton);
    });

    const photoInput = document.getElementById('profile_photo');
    const previewImage = document.getElementById('profile-photo-preview');
    const currentImage = document.getElementById('profile-photo-current');
    const fallback = document.getElementById('profile-photo-fallback');

    if (photoInput && previewImage) {
        photoInput.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (!file) {
                previewImage.src = '';
                previewImage.classList.add('hidden');
                if (currentImage) currentImage.classList.remove('hidden');
                if (fallback) fallback.classList.remove('hidden');
                return;
            }

            previewImage.src = URL.createObjectURL(file);
            previewImage.classList.remove('hidden');
            if (currentImage) currentImage.classList.add('hidden');
            if (fallback) fallback.classList.add('hidden');
        });
    }

    if (autoHideMs > 0) {
        root.querySelectorAll('[data-profile-autohide]').forEach((node) => {
            setTimeout(() => {
                node.classList.add('hidden');
            }, autoHideMs);
        });
    }

    setActiveTab(initialTab);
})();
