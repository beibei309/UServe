(() => {
    function initializeTheme() {
        const savedTheme = localStorage.getItem('admin-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme || (prefersDark ? 'dark' : 'light');

        document.body.setAttribute('data-theme', theme);
        updateNavigation();
    }

    function toggleTheme(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const currentTheme = document.body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        requestAnimationFrame(() => {
            document.body.setAttribute('data-theme', newTheme);
            localStorage.setItem('admin-theme', newTheme);
            updateNavigation();
        });

        return false;
    }

    function updateNavigation() {
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        const navLinks = document.querySelectorAll('nav a');

        navLinks.forEach((link) => {
            const isActive = link.classList.contains('bg-gradient-to-r') || link.classList.contains('text-white');
            if (isActive) return;
            link.style.color = isDark ? '#e2e8f0' : '#475569';
        });

        const submenuButtons = document.querySelectorAll('[data-submenu-toggle]');
        submenuButtons.forEach((button) => {
            const isActive = button.classList.contains('text-white') || button.classList.contains('font-semibold');
            if (isActive) return;
            button.style.color = isDark ? '#e2e8f0' : '#475569';
        });
    }

    function toggleSubMenu(menuId, arrowId) {
        const menu = document.getElementById(menuId);
        const arrow = document.getElementById(arrowId);

        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            arrow.classList.add('rotate-90');
        } else {
            menu.classList.add('hidden');
            arrow.classList.remove('rotate-90');
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');
        const isDesktop = window.innerWidth >= 1024;

        if (isDesktop) {
            sidebar.classList.toggle('hidden');
        } else {
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                if (overlay) overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
            }
        }
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');
        if (window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
        }
    }

    function setupThemeListeners() {
        initializeTheme();

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('admin-theme')) {
                document.body.setAttribute('data-theme', e.matches ? 'dark' : 'light');
                updateNavigation();
            }
        });

        window.addEventListener('resize', () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (window.innerWidth >= 1024 && overlay) {
                overlay.classList.add('hidden');
            }
            if (window.innerWidth < 1024 && !sidebar.classList.contains('-translate-x-full') && overlay) {
                overlay.classList.remove('hidden');
            }
        });
    }

    function setupUiActions() {
        document.addEventListener('click', (event) => {
            const sidebarTrigger = event.target.closest('[data-sidebar-toggle]');
            if (sidebarTrigger) {
                event.preventDefault();
                toggleSidebar();
                return;
            }

            const themeTrigger = event.target.closest('[data-theme-toggle]');
            if (themeTrigger) {
                toggleTheme(event);
                return;
            }

            const submenuTrigger = event.target.closest('[data-submenu-toggle]');
            if (submenuTrigger) {
                event.preventDefault();
                const menuId = submenuTrigger.dataset.menuId;
                const arrowId = submenuTrigger.dataset.arrowId;
                if (menuId && arrowId) {
                    toggleSubMenu(menuId, arrowId);
                }
            }
        });
    }

    function setupAjaxNavigation() {
        const loadingIndicator = document.createElement('div');
        loadingIndicator.id = 'loading-indicator';
        loadingIndicator.innerHTML = '<div class="loading-spinner"></div>';
        loadingIndicator.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.1);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            `;
        document.body.appendChild(loadingIndicator);

        const spinnerStyle = document.createElement('style');
        spinnerStyle.textContent = `
                .loading-spinner {
                    width: 40px;
                    height: 40px;
                    border: 3px solid #f3f3f3;
                    border-top: 3px solid #06b6d4;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
        document.head.appendChild(spinnerStyle);

        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');

            if (
                link &&
                link.href.includes('/admin/') &&
                !link.href.includes('#') &&
                !link.target &&
                !link.download &&
                link.origin === window.location.origin
            ) {
                e.preventDefault();
                closeMobileSidebar();
                navigateTo(link.href);
            }
        });

        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.isAjax) {
                loadContent(window.location.href, false);
            }
        });

        function navigateTo(url) {
            loadContent(url, true);
        }

        function loadContent(url, pushState = true) {
            showLoading();

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'text/html',
                },
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then((html) => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    const newContent = tempDiv.querySelector('main');
                    const currentMain = document.querySelector('#main-content');

                    if (newContent && currentMain) {
                        currentMain.classList.add('transitioning');
                        currentMain.style.opacity = '0';

                        setTimeout(() => {
                            currentMain.innerHTML = newContent.innerHTML;
                            currentMain.style.opacity = '1';
                            currentMain.classList.remove('transitioning');

                            const newTitle = tempDiv.querySelector('title');
                            if (newTitle) {
                                document.title = newTitle.textContent;
                            }

                            const incomingPageScriptContent = tempDiv.querySelector('#page-script-content');
                            const currentPageScriptContent = document.getElementById('page-script-content');
                            if (incomingPageScriptContent && currentPageScriptContent) {
                                currentPageScriptContent.innerHTML = incomingPageScriptContent.innerHTML;
                                executeScripts(currentPageScriptContent);
                            }

                            executeScripts(currentMain);

                            if (pushState) {
                                window.history.pushState({ isAjax: true }, '', url);
                            }

                            updateNavigationState(url);

                            hideLoading();
                        }, 150);
                    } else {
                        window.location.href = url;
                    }
                })
                .catch(() => {
                    window.location.href = url;
                });
        }

        function showLoading() {
            loadingIndicator.style.display = 'flex';
        }

        function hideLoading() {
            loadingIndicator.style.display = 'none';
        }

        function executeScripts(container) {
            const scripts = container.querySelectorAll('script');
            scripts.forEach((script) => {
                if (script.src) {
                    const existingDynamicScripts = document.querySelectorAll('script[data-admin-dynamic-src]');
                    existingDynamicScripts.forEach((existingScript) => {
                        if (existingScript.getAttribute('data-admin-dynamic-src') === script.src) {
                            existingScript.remove();
                        }
                    });

                    const newScript = document.createElement('script');
                    newScript.src = script.src;
                    newScript.setAttribute('data-admin-dynamic-src', script.src);
                    document.head.appendChild(newScript);
                } else {
                    // Inline scripts are intentionally not executed for security hardening.
                    // Page behavior should be provided by versioned external JS modules only.
                }
            });
        }

        function updateNavigationState(url) {
            const navLinks = document.querySelectorAll('nav a, nav button');
            navLinks.forEach((link) => {
                link.classList.remove('bg-gradient-to-r', 'from-cyan-500', 'to-blue-600', 'text-white', 'font-semibold');
                link.style.backgroundColor = '';
                link.style.color = '';
            });

            const currentPath = new URL(url).pathname;
            navLinks.forEach((link) => {
                if (link.href && new URL(link.href).pathname === currentPath) {
                    link.classList.add('bg-gradient-to-r', 'from-cyan-500', 'to-blue-600', 'text-white', 'font-semibold');
                }
            });
        }

        updateNavigationState(window.location.href);

        if (!window.history.state) {
            window.history.replaceState({ isAjax: true }, '', window.location.href);
        }
    }

    window.toggleTheme = toggleTheme;
    window.toggleSubMenu = toggleSubMenu;
    window.toggleSidebar = toggleSidebar;

    document.addEventListener('DOMContentLoaded', () => {
        setupUiActions();
        setupThemeListeners();
        setupAjaxNavigation();
    });
})();
