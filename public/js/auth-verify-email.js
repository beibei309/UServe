(() => {
    const config = document.getElementById('verifyEmailConfig');
    if (!config) return;

    if (config.dataset.linkSent === 'true') {
        Swal.fire({
            icon: 'success',
            title: 'Email Sent!',
            text: 'A new verification link has been sent to your inbox.',
            confirmButtonColor: '#4f46e5',
            timer: 3000,
            timerProgressBar: true,
        });
    }

    const statusCheckUrl = config.dataset.statusCheckUrl;
    if (!statusCheckUrl) return;

    const checkVerificationStatus = async () => {
        try {
            const response = await fetch(statusCheckUrl, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            if (data.verified) {
                window.location.href = data.redirect_to || '/dashboard';
            }
        } catch (_error) {
            // Silent retry on next interval.
        }
    };

    checkVerificationStatus();
    setInterval(checkVerificationStatus, 5000);
})();
