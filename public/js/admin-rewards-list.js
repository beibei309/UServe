window.UServeAdmin.register('rewardsList', 'adminModuleRewardsListConfig', (config) => {
    if (!config) return;

    const successMessage = config.dataset.successMessage || '';

    if (successMessage) {
        Swal.fire({
            title: 'Success!',
            text: successMessage,
            icon: 'success',
            confirmButtonText: 'OK',
        });
    }

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('.delete-form');
        if (!form) return;

        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

window.UServeAdmin.boot('rewardsList');
