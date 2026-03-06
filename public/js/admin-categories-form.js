window.UServeAdmin.register('categoriesForm', 'adminCategoriesFormConfig', () => {
    const iconInput = document.getElementById('iconInput');
    const previewIcon = document.getElementById('previewIcon');
    const colorInput = document.querySelector('input[name="color"]');
    const colorValue = document.getElementById('colorValue');
    if (!iconInput || !previewIcon) return;

    const iconOptions = Array.from(document.querySelectorAll('.icon-option'));
    const previewContainer = previewIcon.closest('div');

    function updateColorPreview(color) {
        if (colorValue) {
            colorValue.textContent = color;
        }
        if (previewContainer) {
            previewContainer.style.backgroundColor = `${color}20`;
            previewContainer.style.color = color;
        }
    }

    function syncSelectedIcon(iconClass) {
        iconOptions.forEach((option) => {
            const active = (option.dataset.icon || '') === iconClass;
            option.classList.toggle('selected', active);
        });
    }

    function selectIcon(iconClass) {
        iconInput.value = iconClass;
        previewIcon.className = iconClass || 'fa fa-folder';
        syncSelectedIcon(iconClass || '');
    }

    window.selectIcon = selectIcon;

    iconOptions.forEach((option) => {
        option.addEventListener('click', () => {
            selectIcon(option.dataset.icon || '');
        });
    });

    iconInput.addEventListener('input', function () {
        selectIcon(this.value || '');
    });

    if (colorInput) {
        updateColorPreview(colorInput.value || '#4f46e5');
        colorInput.addEventListener('input', function () {
            updateColorPreview(this.value || '#4f46e5');
        });
    }

    selectIcon(iconInput.value || '');
});

window.UServeAdmin.boot('categoriesForm');
