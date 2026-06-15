(() => {
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-confirm-message]');
        if (!form) return;
        if (form.dataset.confirmed === '1') return;

        const message = form.dataset.confirmMessage || 'Are you sure?';
        event.preventDefault();

        window.UServeAdmin.confirm({
            title: 'Please Confirm',
            text: message,
            confirmButtonText: form.dataset.confirmButtonText || 'Yes, continue',
        }).then((confirmed) => {
            if (!confirmed) return;
            form.dataset.confirmed = '1';
            form.submit();
        });
    });
})();
