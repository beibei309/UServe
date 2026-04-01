(() => {
    const config = document.getElementById('dashboardConfig');
    if (!config || config.dataset.bound === 'true') return;
    config.dataset.bound = 'true';

    const searchInput = document.getElementById('dashboard-search-input');
    if (!searchInput) return;

    const searchQuery = (JSON.parse(config.dataset.searchQuery || '""') || '').toString();
    if (searchQuery && searchInput.value !== searchQuery) {
        searchInput.value = searchQuery;
    }

    const bindHorizontalScroller = ({ leftId, rightId, containerId, delta = 320 }) => {
        const left = document.getElementById(leftId);
        const right = document.getElementById(rightId);
        const container = document.getElementById(containerId);
        if (!left || !right || !container) return;

        left.addEventListener('click', () => {
            container.scrollBy({ left: -delta, behavior: 'smooth' });
        });

        right.addEventListener('click', () => {
            container.scrollBy({ left: delta, behavior: 'smooth' });
        });
    };

    bindHorizontalScroller({
        leftId: 'dashboardCategoriesScrollLeft',
        rightId: 'dashboardCategoriesScrollRight',
        containerId: 'dashboardCategoriesScrollContainer',
        delta: 340,
    });

    bindHorizontalScroller({
        leftId: 'dashboardHelpersScrollLeft',
        rightId: 'dashboardHelpersScrollRight',
        containerId: 'dashboardHelpersScrollContainer',
        delta: 320,
    });
})();
