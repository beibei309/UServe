window.UServeAdmin.register('studentStatusForm', 'adminStudentStatusFormConfig', (config) => {
    if (!config) return;

    const mode = config.dataset.mode || 'create';
    const status = document.getElementById('status');
    const semesterContainer = document.getElementById('semester-container');
    const semester = document.getElementById('semester');
    const dateContainer = document.getElementById('graduation-date-container');
    if (!status || !semesterContainer || !dateContainer) return;

    function toggleFields() {
        const value = status.value;

        if (mode === 'create' && semester) {
            if (value === 'Graduated') {
                semester.value = 'Final';
                semester.disabled = true;
                semesterContainer.classList.add('opacity-60');
            } else if (value === 'Dismissed') {
                semester.value = 'N/A';
                semester.disabled = true;
                semesterContainer.classList.add('opacity-60');
            } else {
                semester.disabled = false;
                semester.value = '';
                semesterContainer.classList.remove('opacity-60');
            }
        } else {
            if (value === 'Graduated' || value === 'Dismissed') {
                semesterContainer.style.display = 'none';
            } else {
                semesterContainer.style.display = 'block';
            }
        }

        if (value === 'Dismissed') {
            dateContainer.style.display = 'none';
        } else {
            dateContainer.style.display = 'block';
        }
    }

    toggleFields();
    status.addEventListener('change', toggleFields);
});

window.UServeAdmin.boot('studentStatusForm');
