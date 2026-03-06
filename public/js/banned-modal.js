(() => {
    const config = document.getElementById('bannedModalConfig');
    if (!config) return;

    const title = (JSON.parse(config.dataset.title || '""') || '').toString();
    const message = (JSON.parse(config.dataset.message || '""') || '').toString();
    const reason = (JSON.parse(config.dataset.reason || '""') || '').toString();
    const logoutUrl = config.dataset.logoutUrl || '';
    const csrfToken = config.dataset.csrfToken || '';

    Swal.fire({
        icon: 'error',
        title,
        html: `
            <p class="text-slate-600 mb-4">${message}</p>
            <div class="bg-red-50 p-3 rounded-lg text-left border border-red-100 mb-4">
                <p class="text-xs font-bold text-red-500 uppercase">Reason:</p>
                <p class="text-sm text-slate-800 italic">"${reason}"</p>
            </div>
            <p class="text-xs text-slate-500">
                If you believe this is a mistake, please contact our support team at
                <a href="mailto:support@U-Serve.upsi.edu.my" class="text-indigo-600 hover:underline font-bold"><br>support@U-Serve.upsi.edu.my</a>.
            </p>
        `,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: true,
        confirmButtonText: 'Log Out',
        confirmButtonColor: '#1e293b',
        customClass: {
            container: 'swal-high-zindex',
        },
    }).then((result) => {
        if (!result.isConfirmed) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = logoutUrl;
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrfToken;
        form.appendChild(token);
        document.body.appendChild(form);
        form.submit();
    });
})();
