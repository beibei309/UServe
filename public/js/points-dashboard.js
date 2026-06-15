(() => {
    const showError = (message) => {
        if (window.Swal) {
            return window.Swal.fire({
                title: 'Error',
                text: message,
                icon: 'error',
                confirmButtonColor: '#dc2626',
            });
        }

        window.alert(message);
        return Promise.resolve();
    };

    const askConfirm = ({ title, text, confirmButtonText = 'Yes', confirmButtonColor = '#059669' }) => {
        if (window.Swal) {
            return window.Swal.fire({
                title,
                text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText,
                cancelButtonText: 'Cancel',
            }).then((result) => result.isConfirmed);
        }

        return Promise.resolve(window.confirm([title, text].filter(Boolean).join('\n')));
    };

    function redeemCertificate(config) {
        const redeemUrl = config?.dataset?.redeemUrl;
        const csrfToken = config?.dataset?.csrfToken;
        if (!redeemUrl || !csrfToken) return;

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

        askConfirm({
            title: 'Unlock Certificate Achievement?',
            text: 'Congratulations! You have earned enough points to unlock your certificate achievement.',
            confirmButtonText: 'Yes, Unlock Achievement!',
        }).then((confirmed) => {
            if (confirmed) proceed();
        });
    }

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-points-redeem-certificate]')) {
            const config = document.getElementById('pointsDashboardConfig');
            redeemCertificate(config);
            return;
        }

        const cancelButton = event.target.closest('[data-points-cancel-redemption]');
        if (!cancelButton) return;

        event.preventDefault();
        askConfirm({
            title: 'Cancel Redemption?',
            text: 'Points will be refunded after cancellation.',
            confirmButtonText: 'Yes, cancel redemption',
            confirmButtonColor: '#dc2626',
        }).then((confirmed) => {
            if (confirmed) cancelButton.closest('form')?.submit();
        });
    });
})();
