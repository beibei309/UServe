function initRewardsFormModule() {
    const termsContainer = document.getElementById('terms-list');
    const addTermBtn = document.getElementById('add-term');

    if (!termsContainer || !addTermBtn) return;

    function createTermRow() {
        const templateRow = termsContainer.querySelector('.term-row');
        const newTermRow = templateRow
            ? templateRow.cloneNode(true)
            : document.createElement('div');

        if (!templateRow) {
            newTermRow.className = 'flex items-center space-x-2 term-row';
            newTermRow.innerHTML = '<input type="text" name="hr_terms[]" class="flex-1 rounded px-3 py-2" placeholder="Enter a term or condition"><button type="button" class="text-red-600 hover:text-red-800 remove-term"><i class="fas fa-times"></i></button>';
        }

        const input = newTermRow.querySelector('input[name="hr_terms[]"]');
        if (input) input.value = '';

        return newTermRow;
    }

    addTermBtn.addEventListener('click', () => {
        termsContainer.appendChild(createTermRow());
    });

    termsContainer.addEventListener('click', (event) => {
        const removeTrigger = event.target.closest('.remove-term');
        if (!removeTrigger) return;

        const termRows = termsContainer.querySelectorAll('.term-row');
        if (termRows.length <= 1) return;

        const termRow = removeTrigger.closest('.term-row');
        if (termRow) {
            termRow.remove();
        }
    });
}

window.UServeAdmin.register('rewardsCreate', 'adminModuleRewardsCreateConfig', () => {
    initRewardsFormModule();
});

window.UServeAdmin.register('rewardsEdit', 'adminModuleRewardsEditConfig', () => {
    initRewardsFormModule();
});

window.UServeAdmin.boot('rewardsCreate');
window.UServeAdmin.boot('rewardsEdit');
