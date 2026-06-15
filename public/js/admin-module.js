(() => {
    const root = (window.UServeAdmin = window.UServeAdmin || {});
    root.modules = root.modules || {};

    root.register = function (name, configId, init) {
        root.modules[name] = { configId, init };
    };

    root.boot = function (name) {
        const module = root.modules[name];
        if (!module || typeof module.init !== 'function') {
            return;
        }
        const config = module.configId ? document.getElementById(module.configId) : null;
        module.init(config);
    };

    root.alert = function ({ icon = 'info', title = '', text = '', confirmButtonColor = '#4f46e5', ...options } = {}) {
        if (window.Swal) {
            return window.Swal.fire({
                icon,
                title,
                text,
                confirmButtonColor,
                ...options,
            });
        }

        window.alert([title, text].filter(Boolean).join('\n'));
        return Promise.resolve();
    };

    root.confirm = function ({
        icon = 'warning',
        title = 'Are you sure?',
        text = '',
        confirmButtonText = 'Yes',
        cancelButtonText = 'Cancel',
        confirmButtonColor = '#4f46e5',
        cancelButtonColor = '#6b7280',
    } = {}) {
        if (window.Swal) {
            return window.Swal.fire({
                icon,
                title,
                text,
                showCancelButton: true,
                confirmButtonText,
                cancelButtonText,
                confirmButtonColor,
                cancelButtonColor,
            }).then((result) => result.isConfirmed);
        }

        return Promise.resolve(window.confirm([title, text].filter(Boolean).join('\n')));
    };
})();
