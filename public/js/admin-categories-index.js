window.UServeAdmin.register('categoriesIndex', 'adminCategoriesIndexConfig', (config) => {
    if (!config) return;
    const successMessage = config.dataset.successMessage || '';
    const errorMessage = config.dataset.errorMessage || '';

    window.confirmDelete = function (id) {
        Swal.fire({
            title: 'Delete Category?',
            text: 'This action cannot be undone. All services using this category will be affected.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete Category',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg',
                cancelButton: 'rounded-lg',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    };

    document.addEventListener('click', (event) => {
        const deleteTrigger = event.target.closest('[data-category-delete]');
        if (!deleteTrigger) return;
        window.confirmDelete(deleteTrigger.dataset.categoryId);
    });

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: successMessage,
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-2xl' },
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: errorMessage,
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-2xl' },
        });
    }
});

window.UServeAdmin.boot('categoriesIndex');
