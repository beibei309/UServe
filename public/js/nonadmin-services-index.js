(() => {
    const config = document.getElementById('servicesIndexConfig');
    if (!config) return;

    const shareModal = document.getElementById('shareModal');
    if (!shareModal) return;

    function handleShare(button) {
        const url = button.dataset.url;
        const modalContent = shareModal.querySelector('div.bg-white');
        document.getElementById('shareLinkInput').value = url;
        shareModal.classList.remove('opacity-0', 'pointer-events-none');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }

    function closeShareModal() {
        const modalContent = shareModal.querySelector('div.bg-white');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            shareModal.classList.add('opacity-0', 'pointer-events-none');
            document.getElementById('copyMessage').classList.add('opacity-0');
        }, 150);
    }

    function copyShareLink() {
        const input = document.getElementById('shareLinkInput');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);
        const msg = document.getElementById('copyMessage');
        msg.classList.remove('opacity-0');
        setTimeout(() => msg.classList.add('opacity-0'), 2000);
    }

    document.querySelectorAll('img[data-fallback-src]').forEach((img) => {
        img.addEventListener('error', () => {
            if (img.dataset.fallbackApplied === '1') return;
            img.dataset.fallbackApplied = '1';
            img.src = img.dataset.fallbackSrc;
        });
    });

    document.querySelectorAll('select[data-auto-submit]').forEach((select) => {
        select.addEventListener('change', () => {
            select.form?.submit();
        });
    });

    document.addEventListener('click', (event) => {
        const shareTrigger = event.target.closest('[data-share-trigger]');
        if (shareTrigger) {
            handleShare(shareTrigger);
            return;
        }

        if (event.target.closest('[data-close-share]')) {
            closeShareModal();
            return;
        }

        if (event.target.closest('[data-copy-share]')) {
            copyShareLink();
        }
    });
})();
