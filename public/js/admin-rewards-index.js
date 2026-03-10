window.UServeAdmin.register('rewardsIndex', 'adminModuleRewardsIndexConfig', (config) => {
    if (!config) return;

    const successMessage = config.dataset.successMessage || '';
    if (!successMessage) return;

    Swal.fire({
        title: 'Success!',
        text: successMessage,
        icon: 'success',
        confirmButtonText: 'OK',
    });
});

window.UServeAdmin.boot('rewardsIndex');
