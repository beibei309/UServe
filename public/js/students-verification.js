(() => {
    const config = document.getElementById('studentsVerificationConfig');
    if (!config) return;

    const saveLocationUrl = config.dataset.saveLocationUrl || '';
    const uploadPhotoUrl = config.dataset.uploadPhotoUrl || '';
    const uploadSelfieUrl = config.dataset.uploadSelfieUrl || '';
    const csrfToken = config.dataset.csrfToken || '';
    const upsiLat = Number(config.dataset.upsiLat || 0);
    const upsiLng = Number(config.dataset.upsiLng || 0);
    const radiusKm = Number(config.dataset.radiusKm || 25);

    const profilePhotoInput = document.getElementById('profile_photo_input');
    const profilePreview = document.getElementById('profile-preview');
    const photoPickerTrigger = document.querySelector('[data-photo-picker-trigger]');
    const detectLocationBtn = document.getElementById('detect_location_btn');
    const locationStatus = document.getElementById('location_status');
    const uploadPhotoForm = document.getElementById('upload_photo_form');
    const video = document.getElementById('camera_preview');
    const canvas = document.getElementById('snapshot_canvas');
    const startCameraBtn = document.getElementById('start_camera');
    const takeSnapshotBtn = document.getElementById('take_snapshot');
    const retakeSnapshotBtn = document.getElementById('retake_snapshot');
    const confirmSnapshotBtn = document.getElementById('confirm_snapshot');
    const cameraControls = document.getElementById('camera_controls');
    const cameraPlaceholder = document.getElementById('camera_placeholder');

    let stream = null;
    let selfieDataUrl = null;

    const goToStep = (stepNumber) => {
        document.querySelectorAll('.step-panel').forEach((el) => el.classList.add('hidden'));
        if (stepNumber === 'success') {
            const panel = document.getElementById('panel-success');
            if (panel) panel.classList.remove('hidden');
            updateSidebar(4);
            return;
        }
        const panel = document.getElementById(`panel-${stepNumber}`);
        if (panel) panel.classList.remove('hidden');
        updateSidebar(Number(stepNumber));
    };

    const updateSidebar = (activeStep) => {
        for (let i = 1; i <= 3; i++) {
            const el = document.getElementById(`ind-${i}`);
            if (!el) continue;
            const circle = el.querySelector('div:first-child');
            if (!circle) continue;

            if (activeStep > 3) {
                circle.classList.add('bg-green-500', 'border-green-500', 'text-white');
                circle.innerHTML = '✓';
                continue;
            }
            if (i === activeStep) {
                el.classList.add('opacity-100');
                circle.classList.add('bg-white', 'border-white', 'text-indigo-600');
                circle.innerHTML = `${i}`;
                continue;
            }
            if (i < activeStep) {
                circle.classList.add('bg-indigo-800', 'border-indigo-800', 'text-indigo-300');
                circle.innerHTML = '✓';
                continue;
            }
            el.classList.remove('opacity-100');
            circle.classList.remove('bg-white', 'border-white', 'text-indigo-600');
            circle.innerHTML = `${i}`;
        }
    };

    const addressVerified = (lat, lng, addr) => {
        if (!locationStatus) return;
        locationStatus.innerHTML = '<div class="flex items-center gap-2 text-indigo-600 bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-100"><span class="font-bold">Verifying...</span></div>';

        if (!lat || !lng) {
            locationStatus.innerHTML = '';
            return;
        }

        fetch(saveLocationUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ latitude: lat, longitude: lng, address: addr }),
        })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false) {
                    throw new Error(data.message || 'Location verification failed.');
                }
                locationStatus.innerHTML = '<div class="flex items-center gap-2 text-green-600 bg-green-50 px-4 py-2 rounded-lg border border-green-100"><span class="font-bold">Verified!</span></div>';
                setTimeout(() => goToStep(2), 1000);
            })
            .catch((err) => {
                locationStatus.innerHTML = '';
                Swal.fire({ icon: 'error', text: err.message || 'Unable to verify location. Please try again.' });
            });
    };

    document.querySelectorAll('[data-go-step]').forEach((btn) => {
        btn.addEventListener('click', () => {
            goToStep(btn.dataset.goStep || '1');
        });
    });

    if (photoPickerTrigger && profilePhotoInput) {
        photoPickerTrigger.addEventListener('click', () => {
            profilePhotoInput.click();
        });
    }

    if (profilePhotoInput && profilePreview) {
        profilePhotoInput.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                profilePreview.src = e.target?.result || profilePreview.src;
            };
            reader.readAsDataURL(file);
        });
    }

    if (detectLocationBtn) {
        detectLocationBtn.addEventListener('click', () => {
            const original = detectLocationBtn.innerHTML;
            detectLocationBtn.innerHTML = '<span>Detecting...</span>';
            detectLocationBtn.disabled = true;

            if (!navigator.geolocation) {
                detectLocationBtn.innerHTML = original;
                detectLocationBtn.disabled = false;
                Swal.fire({ icon: 'error', text: 'Location is not supported on this browser.' });
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    const r = 6371;
                    const dLat = ((lat - upsiLat) * Math.PI) / 180;
                    const dLon = ((lng - upsiLng) * Math.PI) / 180;
                    const a = Math.sin(dLat / 2) ** 2 + Math.cos((upsiLat * Math.PI) / 180) * Math.cos((lat * Math.PI) / 180) * Math.sin(dLon / 2) ** 2;
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    const dist = r * c;
                    detectLocationBtn.innerHTML = original;
                    detectLocationBtn.disabled = false;
                    if (dist <= radiusKm) {
                        addressVerified(lat, lng, `GPS: ${lat}, ${lng}`);
                        return;
                    }
                    Swal.fire({ icon: 'error', text: 'You must be in Muallim District.' });
                },
                () => {
                    detectLocationBtn.innerHTML = original;
                    detectLocationBtn.disabled = false;
                    Swal.fire({ icon: 'error', text: 'Please allow location access.' });
                }
            );
        });
    }

    if (uploadPhotoForm) {
        uploadPhotoForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(uploadPhotoForm);
            Swal.fire({
                title: 'Uploading...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });
            fetch(uploadPhotoUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        Swal.fire({ icon: 'error', title: 'Upload Failed', text: data.message || 'Something went wrong' });
                        return;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Photo Uploaded!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                    });
                    goToStep(3);
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error occurred.' });
                });
        });
    }

    if (startCameraBtn && video && cameraPlaceholder && cameraControls) {
        startCameraBtn.addEventListener('click', async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                video.classList.remove('hidden');
                cameraPlaceholder.classList.add('hidden');
                startCameraBtn.classList.add('hidden');
                cameraControls.classList.remove('hidden');
            } catch (e) {
                Swal.fire({ icon: 'error', text: 'Camera error.' });
            }
        });
    }

    if (takeSnapshotBtn && canvas && video && confirmSnapshotBtn && retakeSnapshotBtn) {
        takeSnapshotBtn.addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            selfieDataUrl = canvas.toDataURL('image/png');
            video.classList.add('hidden');
            canvas.classList.remove('hidden');
            takeSnapshotBtn.classList.add('hidden');
            confirmSnapshotBtn.classList.remove('hidden');
            retakeSnapshotBtn.classList.remove('hidden');
        });
    }

    if (retakeSnapshotBtn && canvas && video && takeSnapshotBtn && confirmSnapshotBtn) {
        retakeSnapshotBtn.addEventListener('click', () => {
            canvas.classList.add('hidden');
            video.classList.remove('hidden');
            takeSnapshotBtn.classList.remove('hidden');
            confirmSnapshotBtn.classList.add('hidden');
            retakeSnapshotBtn.classList.add('hidden');
        });
    }

    if (confirmSnapshotBtn) {
        confirmSnapshotBtn.addEventListener('click', () => {
            Swal.fire({ title: 'Verifying...', didOpen: () => Swal.showLoading() });
            fetch(uploadSelfieUrl, {
                method: 'POST',
                body: JSON.stringify({ selfie_image: selfieDataUrl }),
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || data.success === false) {
                        throw new Error(data.message || 'Verification failed. Please try again.');
                    }

                    Swal.close();
                    goToStep('success');
                })
                .catch((error) => {
                    Swal.close();
                    Swal.fire({ icon: 'error', text: error.message || 'Verification failed. Please try again.' });
                });
        });
    }
})();
