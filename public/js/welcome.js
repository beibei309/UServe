(() => {
    const config = document.getElementById('welcomeScrollConfig');
    const scrollContainer = document.getElementById('scrollContainer');
    const scrollLeftButton = document.getElementById('scrollLeft');
    const scrollRightButton = document.getElementById('scrollRight');
    if (!config || !scrollContainer || !scrollLeftButton || !scrollRightButton) return;

    const step = Number(config.dataset.step || 300);

    scrollLeftButton.addEventListener('click', () => {
        scrollContainer.scrollBy({
            left: -step,
            behavior: 'smooth',
        });
    });

    scrollRightButton.addEventListener('click', () => {
        scrollContainer.scrollBy({
            left: step,
            behavior: 'smooth',
        });
    });
})();
