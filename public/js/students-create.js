(() => {
    const config = document.getElementById('studentsCreateConfig');
    if (!config) return;

    const profilePhotoInput = document.getElementById('profile_photo_input');
    const profilePhotoPreview = document.getElementById('profile-photo-preview');
    const workExperienceInput = document.getElementById('work_experience_file');
    const form = document.querySelector('form[action]');
    const readyToHelp = config.dataset.readyToHelp === 'true';
    const servicesCreateUrl = config.dataset.servicesCreateUrl || '';
    const maxProfilePhotoSizeBytes = 1024 * 1024;
    const maxWorkFileSizeBytes = 1024 * 1024;

    const showFileTooLarge = (text, attempt = 0) => {
        if (window.Swal) {
            Swal.fire({
                title: 'File too large',
                text,
                icon: 'error',
            });
            return;
        }

        if (attempt < 20) {
            setTimeout(() => showFileTooLarge(text, attempt + 1), 50);
            return;
        }

        alert(text);
    };

    if (profilePhotoInput && profilePhotoPreview) {
        profilePhotoInput.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (!file) return;

            if (file.size > maxProfilePhotoSizeBytes) {
                profilePhotoInput.value = '';
                showFileTooLarge('Profile photo must be 1MB or smaller.');
                return;
            }

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
                showFileTooLarge('Resume/CV file must be 1MB or smaller.');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', (event) => {
            const profilePhoto = profilePhotoInput?.files?.[0];
            if (profilePhoto && profilePhoto.size > maxProfilePhotoSizeBytes) {
                event.preventDefault();
                profilePhotoInput.value = '';
                showFileTooLarge('Profile photo must be 1MB or smaller.');
                return;
            }

            const file = workExperienceInput?.files?.[0];
            if (file && file.size > maxWorkFileSizeBytes) {
                event.preventDefault();
                showFileTooLarge('Resume/CV file must be 1MB or smaller.');
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
