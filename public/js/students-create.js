(() => {
    const config = document.getElementById('studentsCreateConfig');
    if (!config) return;

    const profilePhotoInput = document.getElementById('profile_photo_input');
    const profilePhotoPreview = document.getElementById('profile-photo-preview');
    const readyToHelp = config.dataset.readyToHelp === 'true';
    const servicesCreateUrl = config.dataset.servicesCreateUrl || '';

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
