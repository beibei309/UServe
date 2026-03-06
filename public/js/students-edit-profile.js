(() => {
    const config = document.getElementById('studentsEditProfileConfig');
    if (!config) return;

    const form = document.querySelector('form[action]');
    const deleteFileForm = document.getElementById('delete-file-form');
    const deleteFileTrigger = document.querySelector('[data-delete-file-trigger]');
    const fileInput = document.getElementById('work_experience_file');
    const fileNameDisplay = document.getElementById('file-name-display');
    const fileDropArea = document.getElementById('file-drop-area');
    const currentFileContainer = document.getElementById('current-file-container');
    const successMessage = config.dataset.successMessage || '';

    if (deleteFileTrigger && deleteFileForm) {
        deleteFileTrigger.addEventListener('click', () => {
            Swal.fire({
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
                const fileName = fileInput.files[0].name;
                fileNameDisplay.textContent = `Selected file: ${fileName}`;
                fileNameDisplay.classList.add('text-indigo-600', 'font-medium');
                fileNameDisplay.classList.remove('text-gray-500');
                fileDropArea.classList.add('border-indigo-500', 'bg-indigo-50');
                if (currentFileContainer) {
                    currentFileContainer.classList.add('opacity-50');
                }
                return;
            }

            fileNameDisplay.textContent = 'PDF, DOC, DOCX up to 10MB';
            fileNameDisplay.classList.remove('text-indigo-600', 'font-medium');
            fileNameDisplay.classList.add('text-gray-500');
            fileDropArea.classList.remove('border-indigo-500', 'bg-indigo-50');
            if (currentFileContainer) {
                currentFileContainer.classList.remove('opacity-50');
            }
        });
    }

    if (successMessage) {
        Swal.fire({
            title: 'Successfull!',
            text: successMessage,
            icon: 'success',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    }

    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            Swal.fire({
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
})();
