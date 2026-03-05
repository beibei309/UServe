window.UServeAdmin.register('categoriesForm', 'adminCategoriesFormConfig', () => {
    const iconInput = document.getElementById('iconInput');
    const previewIcon = document.getElementById('previewIcon');
    if (!iconInput || !previewIcon) return;

    function selectIcon(iconClass) {
        iconInput.value = iconClass;
        previewIcon.className = iconClass;
    }

    window.selectIcon = selectIcon;

    document.querySelectorAll('.icon-option').forEach((option) => {
        option.addEventListener('click', () => {
            selectIcon(option.dataset.icon || '');
        });
        option.addEventListener('mouseenter', () => {
            option.style.borderColor = '#06b6d4';
            option.style.backgroundColor = 'var(--hover-bg)';
        });
        option.addEventListener('mouseleave', () => {
            option.style.borderColor = 'var(--border-color)';
            option.style.backgroundColor = 'var(--bg-secondary)';
        });
    });

    iconInput.addEventListener('input', function () {
        previewIcon.className = this.value;
    });
});

window.UServeAdmin.boot('categoriesForm');
