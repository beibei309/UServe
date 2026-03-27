(() => {
    const config = document.getElementById('studentsCreateConfig');
    if (!config) return;

    const profilePhotoInput = document.getElementById('profile_photo_input');
    const profilePhotoPreview = document.getElementById('profile-photo-preview');
    const workExperienceInput = document.getElementById('work_experience_file');
    const form = document.querySelector('form[action]');
    const readyToHelp = config.dataset.readyToHelp === 'true';
    const servicesCreateUrl = config.dataset.servicesCreateUrl || '';
    const maxWorkFileSizeBytes = 1024 * 1024;

    if (profilePhotoInput && profilePhotoPreview) {
        profilePhotoInput.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                profilePhotoPreview.src = e.target?.result || profilePhotoPreview.src;
            };
            reader.readAsDataURL(file);
        });
    }

    if (workExperienceInput) {
        workExperienceInput.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (!file) {
                return;
            }

            if (file.size > maxWorkFileSizeBytes) {
                workExperienceInput.value = '';
                Swal.fire({
                    title: 'File too large',
                    text: 'Resume/CV file must be 1MB or smaller.',
                    icon: 'error',
                });
            }
        });
    }

    if (form) {
        form.addEventListener('submit', (event) => {
            const file = workExperienceInput?.files?.[0];
            if (!file) {
                return;
            }

            if (file.size > maxWorkFileSizeBytes) {
                event.preventDefault();
                Swal.fire({
                    title: 'File too large',
                    text: 'Resume/CV file must be 1MB or smaller.',
                    icon: 'error',
                });
            }
        });
    }

    if (readyToHelp) {
        Swal.fire({
            title: 'Profile Updated!',
            text: 'You are now ready to help others.',
            icon: 'success',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            backdrop: 'rgba(0,0,123,0.4) left top no-repeat',
            willClose: () => {
                if (servicesCreateUrl) {
                    window.location.href = servicesCreateUrl;
                }
            },
        });
    }
})();
