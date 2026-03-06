(() => {
    const config = document.getElementById('verifyEmailConfig');
    if (!config) return;
    if (config.dataset.linkSent !== 'true') return;

    Swal.fire({
        icon: 'success',
        title: 'Email Sent!',
        text: 'A new verification link has been sent to your inbox.',
        confirmButtonColor: '#4f46e5',
        timer: 3000,
        timerProgressBar: true,
    });
})();
