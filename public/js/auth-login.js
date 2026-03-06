(() => {
    const config = document.getElementById('loginConfig');
    if (!config) return;

    const sessionErrorMessage = (JSON.parse(config.dataset.sessionError || '""') || '').toString();
    const emailErrorMessage = (JSON.parse(config.dataset.emailError || '""') || '').toString();

    const showAccountAlert = (message) => {
        const lower = message.toLowerCase();
        if (!lower.includes('suspended') && !lower.includes('banned') && !lower.includes('blocked') && !lower.includes('blacklisted')) {
            return;
        }
        const title = lower.includes('blacklisted')
            ? 'Account Blacklisted'
            : (lower.includes('blocked') ? 'Account Blocked' : 'Account Suspended');
        Swal.fire({
            icon: 'error',
            title,
            html: message,
            confirmButtonColor: '#4F46E5',
            confirmButtonText: 'Back to Login',
        });
    };

    if (sessionErrorMessage) {
        showAccountAlert(sessionErrorMessage);
    }
    if (emailErrorMessage) {
        showAccountAlert(emailErrorMessage);
    }
})();
