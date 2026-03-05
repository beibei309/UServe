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
})();
