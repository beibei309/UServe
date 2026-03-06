(() => {
    const config = document.getElementById('favoritesIndexConfig');
    if (!config) return;

    const toggleUrl = config.dataset.toggleUrl || '';
    const csrfToken = config.dataset.csrfToken || '';

    const performRemove = (serviceId, btn) => {
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';
        btn.disabled = true;

        fetch(toggleUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ service_id: serviceId }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (!data.success) {
                    throw new Error('Request failed');
                }
                const card = btn.closest('.service-card');
                if (!card) return;
                card.classList.add('card-removed');
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
                toast.fire({ icon: 'success', title: 'Service removed' });
                setTimeout(() => {
                    card.remove();
                    if (document.querySelectorAll('.service-card').length === 0) {
                        location.reload();
                    }
                }, 400);
            })
            .catch(() => {
                Swal.fire('Error', 'Something went wrong!', 'error');
                btn.innerHTML = '<i class="fa-solid fa-heart"></i>';
                btn.disabled = false;
            });
    };

    const confirmRemove = (serviceId, btn) => {
        Swal.fire({
            title: 'Remove from favorites?',
            text: 'You can always add this service back later.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            borderRadius: '1rem',
            customClass: {
                popup: 'rounded-2xl',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                performRemove(serviceId, btn);
            }
        });
    };

    document.querySelectorAll('[data-favorite-remove]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const serviceId = Number(btn.dataset.serviceId || 0);
            if (!serviceId) return;
            confirmRemove(serviceId, btn);
        });
    });
})();
