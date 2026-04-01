function getThemeSwalOptions() {
    return {
        background: getComputedStyle(document.documentElement).getPropertyValue('--bg-primary') || '#fff',
        color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary') || '#222',
        customClass: { popup: 'rounded-2xl' },
    };
}

function readFlashMessages() {
    const config = document.getElementById('adminAdminsIndexConfig');
    const main = document.getElementById('main-content');
    const body = document.body;

    const successMessage =
        (config && (config.dataset.successMessage || config.getAttribute('data-success-message'))) ||
        (main && main.getAttribute('data-success-message')) ||
        (body && body.getAttribute('data-success-message')) ||
        '';

    const errorMessage =
        (config && (config.dataset.errorMessage || config.getAttribute('data-error-message'))) ||
        (main && main.getAttribute('data-error-message')) ||
        (body && body.getAttribute('data-error-message')) ||
        '';

    return {
        successMessage: successMessage || '',
        errorMessage: errorMessage || '',
    };
}

function fireAlert({ icon, title, text }) {
    const opts = getThemeSwalOptions();
    if (typeof Swal === 'undefined') {
        alert(text);
        return;
    }

    Swal.fire({
        icon,
        title,
        text,
        timer: 3000,
        showConfirmButton: false,
        ...opts,
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const updated = urlParams.get('updated') === '1';
    if (updated) {
        fireAlert({ icon: 'success', title: 'Success!', text: 'Admin updated successfully.' });
        window.history.replaceState({}, document.title, window.location.pathname);
        return;
    }

    const { successMessage, errorMessage } = readFlashMessages();

    if (successMessage) {
        fireAlert({ icon: 'success', title: 'Success!', text: successMessage });
    }
    if (errorMessage) {
        fireAlert({ icon: 'error', title: 'Error!', text: errorMessage });
    }
});
