(() => {
    const config = document.getElementById('studentsEditProfileConfig');
    if (!config) return;

    const ensureSwal = () => Promise.resolve(window.Swal || null);

    let swal = null;

    const showAlert = (options) => {
        if (swal) {
            return swal.fire(options);
        }

        const title = options?.title ? `${options.title}\n` : '';
        const text = options?.text || '';
        window.alert(`${title}${text}`.trim());
        return Promise.resolve({ isConfirmed: true });
    };

    const showConfirm = (options) => {
        if (swal) {
            return swal.fire(options);
        }

        const title = options?.title ? `${options.title}\n` : '';
        const text = options?.text || '';
        const isConfirmed = window.confirm(`${title}${text}`.trim());
        return Promise.resolve({ isConfirmed });
    };

    const init = async () => {
        swal = await ensureSwal();

        const form = document.querySelector('form[action]');
        const deleteFileForm = document.getElementById('delete-file-form');
        const deleteFileTrigger = document.querySelector('[data-delete-file-trigger]');
        const fileInput = document.getElementById('work_experience_file');
        const fileNameDisplay = document.getElementById('file-name-display');
        const fileDropArea = document.getElementById('file-drop-area');
        const currentFileContainer = document.getElementById('current-file-container');
        const successMessage = config.dataset.successMessage || '';
        const errorMessage = config.dataset.errorMessage || '';
        const maxWorkFileSizeBytes = 1024 * 1024;

        if (deleteFileTrigger && deleteFileForm) {
            deleteFileTrigger.addEventListener('click', () => {
                showConfirm({
                    title: 'Delete File?',
                    text: 'Are you sure you want to remove your document? This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteFileForm.submit();
                    }
                });
            });
        }

        if (fileInput && fileNameDisplay && fileDropArea) {
            fileInput.addEventListener('change', () => {
                if (fileInput.files && fileInput.files.length > 0) {
                    const selectedFile = fileInput.files[0];
                    if (selectedFile.size > maxWorkFileSizeBytes) {
                        fileInput.value = '';
                        fileNameDisplay.textContent = 'PDF, DOC, DOCX up to 1MB';
                        fileNameDisplay.classList.remove('text-indigo-600', 'font-medium');
                        fileNameDisplay.classList.add('text-gray-500');
                        fileDropArea.classList.remove('border-indigo-500', 'bg-indigo-50');
                        if (currentFileContainer) {
                            currentFileContainer.classList.remove('opacity-50');
                        }
                        showAlert({
                            title: 'File too large',
                            text: 'Supporting document must be 1MB or smaller.',
                            icon: 'error',
                        });
                        return;
                    }

                    const fileName = selectedFile.name;
                    fileNameDisplay.textContent = `Selected file: ${fileName}`;
                    fileNameDisplay.classList.add('text-indigo-600', 'font-medium');
                    fileNameDisplay.classList.remove('text-gray-500');
                    fileDropArea.classList.add('border-indigo-500', 'bg-indigo-50');
                    if (currentFileContainer) {
                        currentFileContainer.classList.add('opacity-50');
                    }
                    return;
                }

                fileNameDisplay.textContent = 'PDF, DOC, DOCX up to 1MB';
                fileNameDisplay.classList.remove('text-indigo-600', 'font-medium');
                fileNameDisplay.classList.remove('text-gray-500');
                fileNameDisplay.classList.add('text-gray-500');
                fileDropArea.classList.remove('border-indigo-500', 'bg-indigo-50');
                if (currentFileContainer) {
                    currentFileContainer.classList.remove('opacity-50');
                }
            });
        }

        if (successMessage) {
            showAlert({
                title: 'Successful!',
                text: successMessage,
                icon: 'success',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        }

        if (errorMessage) {
            showAlert({
                title: 'Update Failed',
                text: errorMessage,
                icon: 'error',
            });
        }

        if (form) {
            form.addEventListener('submit', (e) => {
                const selectedFile = fileInput?.files?.[0];
                if (selectedFile && selectedFile.size > maxWorkFileSizeBytes) {
                    e.preventDefault();
                    showAlert({
                        title: 'File too large',
                        text: 'Supporting document must be 1MB or smaller.',
                        icon: 'error',
                    });
                    return;
                }

                e.preventDefault();
                showConfirm({
                    title: 'Save Changes?',
                    text: 'Are you sure you want to update your profile?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4F46E5',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Save Changes',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    };

    init();
})();
