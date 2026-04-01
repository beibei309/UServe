(() => {
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-confirm-message]');
        if (!form) return;
        const message = form.dataset.confirmMessage || 'Are you sure?';
        if (!window.confirm(message)) {
            event.preventDefault();
        }
        // If confirmed, allow the form to submit normally (no double submit)
    });

    // Remove button click confirmation to avoid double popup
    // document.addEventListener('click', (event) => {
    //     const trigger = event.target.closest('button[data-confirm-message]');
    //     if (!trigger) return;
    //     const message = trigger.dataset.confirmMessage || 'Are you sure?';
    //     if (!window.confirm(message)) {
    //         event.preventDefault();
    //     }
    // });
})();
