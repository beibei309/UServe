(() => {
    const config = document.getElementById('communityVerificationConfig');
    if (!config) return;

    const saveLocationUrl = config.dataset.saveLocationUrl || '';
    const uploadSelfieUrl = config.dataset.uploadSelfieUrl || '';
    const csrfToken = config.dataset.csrfToken || '';
    const upsiLat = Number(config.dataset.upsiLat || 0);
    const upsiLng = Number(config.dataset.upsiLng || 0);
    const radiusKm = Number(config.dataset.radiusKm || 25);
    const challenges = ['Peace Sign ✌️', 'Thumbs Up 👍', 'Touch Your Ear 👂', 'Cover One Eye 👁️', 'Open Mouth 😮', 'Hand on Head 🙆', 'Look Left ⬅️', 'Look Right ➡️'];

    const step2 = document.getElementById('step2');
    const locationMsg = document.getElementById('location_status_msg');
    const detectBtn = document.getElementById('detect_location_btn');
    const profileInput = document.getElementById('profile_photo_input');
    const profilePreview = document.getElementById('profile-preview');
    const video = document.getElementById('camera_preview');
    const canvas = document.getElementById('snapshot_canvas');
    const placeholder = document.getElementById('camera_placeholder');
    const startBtn = document.getElementById('start_camera');
    const takeBtn = document.getElementById('take_snapshot');
    const retakeBtn = document.getElementById('retake_snapshot');
    const confirmBtn = document.getElementById('confirm_snapshot');
    const challengeBanner = document.getElementById('challenge_banner');
    const challengeText = document.getElementById('challenge_text');
    const faceGuide = document.getElementById('face_guide');
    const mainForm = document.getElementById('verificationForm');

    let stream = null;
    let selfieDataUrl = null;
    let currentChallenge = '';

    const markLocationVerified = (lat, lng, addr) => {
        if (!locationMsg) return;
        locationMsg.innerHTML = '<div class="text-indigo-600 bg-indigo-50 px-4 py-3 rounded-lg border border-indigo-100 mt-2 font-medium">Verifying location...</div>';
        if (detectBtn) detectBtn.disabled = true;

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
                locationMsg.innerHTML = `<div class="flex items-center justify-center gap-2 text-green-600 bg-green-50 px-4 py-3 rounded-lg border border-green-100 mt-2"><span class="font-bold">✓ Verified: ${addr}</span></div>`;
                Swal.fire({ icon: 'success', title: 'Location Verified', text: 'You can now proceed to photo upload.', timer: 1500, showConfirmButton: false });
                step2?.classList.remove('opacity-50', 'pointer-events-none');
            })
            .catch((err) => {
                locationMsg.innerHTML = '';
                Swal.fire({ icon: 'error', text: err.message || 'Unable to verify location. Please try again.' });
            })
            .finally(() => {
                if (detectBtn) detectBtn.disabled = false;
            });
    };

    if (detectBtn) {
        detectBtn.addEventListener('click', () => {
            const original = detectBtn.innerHTML;
            detectBtn.innerHTML = '<span>Detecting...</span>';
            detectBtn.disabled = true;

            if (!navigator.geolocation) {
                detectBtn.innerHTML = original;
                detectBtn.disabled = false;
                Swal.fire({ icon: 'error', text: 'Geolocation is not supported by your browser.' });
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

                    detectBtn.innerHTML = original;
                    detectBtn.disabled = false;
                    if (dist <= radiusKm) {
                        markLocationVerified(lat, lng, `GPS: ${lat.toFixed(4)}, ${lng.toFixed(4)}`);
                        return;
                    }
                    Swal.fire({ icon: 'error', text: 'You seem to be outside the Muallim District area.' });
                },
                () => {
                    detectBtn.innerHTML = original;
                    detectBtn.disabled = false;
                    Swal.fire({ icon: 'error', text: 'Please allow location access to continue.' });
                }
            );
        });
    }

    if (profileInput && profilePreview) {
        profileInput.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                profilePreview.src = ev.target?.result || profilePreview.src;
            };
            reader.readAsDataURL(file);
        });
    }

    const startChallenge = () => {
        const randomIndex = Math.floor(Math.random() * challenges.length);
        currentChallenge = challenges[randomIndex];
        if (challengeText) challengeText.textContent = currentChallenge;
        challengeBanner?.classList.remove('hidden');
    };

    startBtn?.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            if (video) video.srcObject = stream;
            video?.classList.remove('hidden');
            placeholder?.classList.add('hidden');
            startBtn.classList.add('hidden');
            takeBtn?.classList.remove('hidden');
            faceGuide?.classList.remove('hidden');
            startChallenge();
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Camera Error', text: 'Unable to access camera. Please allow permissions.' });
        }
    });

    takeBtn?.addEventListener('click', () => {
        if (!video || !canvas) return;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        selfieDataUrl = canvas.toDataURL('image/jpeg', 0.8);
        video.classList.add('hidden');
        faceGuide?.classList.add('hidden');
        canvas.classList.remove('hidden');
        takeBtn.classList.add('hidden');
        retakeBtn?.classList.remove('hidden');
        confirmBtn?.classList.remove('hidden');
    });

    retakeBtn?.addEventListener('click', () => {
        canvas?.classList.add('hidden');
        video?.classList.remove('hidden');
        faceGuide?.classList.remove('hidden');
        retakeBtn.classList.add('hidden');
        confirmBtn?.classList.add('hidden');
        takeBtn?.classList.remove('hidden');
    });

    confirmBtn?.addEventListener('click', () => {
        if (!selfieDataUrl) {
            Swal.fire({ icon: 'warning', title: 'No Image', text: 'Please take a photo first!' });
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Uploading...';
        Swal.fire({ title: 'Uploading Selfie...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000);

        fetch(uploadSelfieUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                selfie_image: selfieDataUrl,
                verification_note: currentChallenge,
            }),
            signal: controller.signal,
        })
            .then(async (res) => {
                clearTimeout(timeoutId);
                const contentType = res.headers.get('content-type');
                if (!res.ok) {
                    if (res.status === 413) throw new Error('Image too large. Please retry.');
                    throw new Error(`Server Error (${res.status})`);
                }
                if (contentType && contentType.includes('application/json')) {
                    return res.json();
                }
                throw new Error('Invalid server response.');
            })
            .then((data) => {
                if (!data.success) {
                    throw new Error(data.message || 'Unknown server error');
                }
                Swal.fire({ icon: 'success', title: 'Verified!', text: 'Selfie with gesture uploaded.', timer: 1500, showConfirmButton: false }).then(() => {
                    window.location.reload();
                });
            })
            .catch((err) => {
                confirmBtn.disabled = false;
                confirmBtn.innerText = 'Confirm & Upload';
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: err.message,
                    footer: '<small>Common fix: Try taking the photo in better lighting.</small>',
                });
            });
    });

    if (mainForm) {
        mainForm.addEventListener('submit', (e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Confirm Submission',
                text: 'Are you sure you want to submit your final verification?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, submit it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (!result.isConfirmed) return;
                Swal.fire({
                    title: 'Success!',
                    text: 'Your verification has been submitted.',
                    icon: 'success',
                    confirmButtonColor: '#0f172a',
                    timer: 2000,
                    showConfirmButton: false,
                });
                setTimeout(() => {
                    mainForm.submit();
                }, 1500);
            });
        });
    }
})();
