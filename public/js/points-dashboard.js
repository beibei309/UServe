(() => {
    function redeemCertificate(config) {
        const redeemUrl = config?.dataset?.redeemUrl;
        const csrfToken = config?.dataset?.csrfToken;
        if (!redeemUrl || !csrfToken) return;

        const showError = (message) => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Error',
                    text: message,
                    icon: 'error',
                    confirmButtonColor: '#dc2626',
                });
            } else {
                alert(message);
            }
        };

        const proceed = () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Unlocking your certificate achievement',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => Swal.showLoading(),
                });
            }

            fetch(redeemUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        showError(data.message || 'An error occurred while unlocking your achievement.');
                        return;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '🏆 Achievement Unlocked!',
                            html: `<p>Congratulations! You have unlocked your certificate achievement!</p><p><strong>Certificate Number:</strong> ${data.certificate_number}</p>`,
                            icon: 'success',
                            confirmButtonColor: '#059669',
                            confirmButtonText: 'View Certificate',
                        }).then(() => {
                            window.location.href = data.certificate_url || window.location.href;
                        });
                    } else {
                        window.location.href = data.certificate_url || window.location.href;
                    }
                })
                .catch(() => {
                    showError('An unexpected error occurred. Please try again.');
                });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Unlock Certificate Achievement?',
                text: 'Congratulations! You have earned enough points to unlock your certificate achievement.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Unlock Achievement!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    proceed();
                }
            });
            return;
        }

        if (confirm('Unlock your certificate achievement now?')) {
            proceed();
        }
    }

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-points-redeem-certificate]')) {
            const config = document.getElementById('pointsDashboardConfig');
            redeemCertificate(config);
            return;
        }

        const cancelButton = event.target.closest('[data-points-cancel-redemption]');
        if (!cancelButton) return;

        const confirmed = confirm('Are you sure you want to cancel this redemption? Points will be refunded.');
        if (!confirmed) {
            event.preventDefault();
        }
    });
})();
