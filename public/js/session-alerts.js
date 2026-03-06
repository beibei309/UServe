(() => {
    const config = document.getElementById('sessionAlertConfig');
    if (!config) return;

    const successMessage = (JSON.parse(config.dataset.success || '""') || '').toString();
    const errorMessage = (JSON.parse(config.dataset.error || '""') || '').toString();

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: successMessage,
            timer: 2000,
            showConfirmButton: false,
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: errorMessage,
            confirmButtonColor: '#d33',
        });
    }
})();
