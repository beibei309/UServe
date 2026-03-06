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
})();
