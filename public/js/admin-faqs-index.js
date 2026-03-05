window.UServeAdmin.register('faqsIndex', 'adminModuleFaqsIndexConfig', (config) => {
    if (!config) return;

    const csrfToken = config.dataset.csrfToken || '';
    const successMessage = config.dataset.successMessage || '';

    document.querySelectorAll('.delete-faq-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const form = button.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This FAQ will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    document.querySelectorAll('.toggle-form').forEach((form) => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const button = form.querySelector('.toggle-btn');
            const isActive = button.dataset.active === '1';
            button.disabled = true;
            button.style.opacity = '0.6';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) return;
                    if (isActive) {
                        button.textContent = 'Hidden';
                        button.className = 'text-xs px-3 py-1 rounded-full toggle-btn transition-colors duration-300 border bg-gray-100 text-gray-600 border-gray-200';
                        button.dataset.active = '0';
                    } else {
                        button.textContent = 'Active';
                        button.className = 'text-xs px-3 py-1 rounded-full toggle-btn transition-colors duration-300 border bg-green-100 text-green-800 border-green-200';
                        button.dataset.active = '1';
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update FAQ status',
                    });
                })
                .finally(() => {
                    button.disabled = false;
                    button.style.opacity = '1';
                });
        });
    });

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: successMessage,
            showConfirmButton: false,
            timer: 2000,
            iconColor: '#10b981',
        });
    }
});

window.UServeAdmin.boot('faqsIndex');
