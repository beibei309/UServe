(() => {
    const config = document.getElementById('serviceRequestsShowConfig');
    if (!config) return;

    const reviewStoreUrl = config.dataset.reviewStoreUrl || '/reviews';
    const actionUrlTemplate = config.dataset.requestActionUrlTemplate || '';
    const reviewModal = document.getElementById('reviewModal');
    const reviewForm = document.getElementById('reviewForm');
    const ratingInput = document.getElementById('rating');
    const reviewServiceRequestId = document.getElementById('reviewServiceRequestId');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function resolveActionUrl(requestId, action) {
        const actionMap = {
            accept: 'accept',
            reject: 'reject',
            'in-progress': 'mark-in-progress',
            complete: 'mark-completed',
            cancel: 'cancel',
        };
        return actionUrlTemplate.replace('__ID__', requestId).replace('__ACTION__', actionMap[action] || action);
    }

    function closeReviewModal() {
        if (!reviewModal) return;
        reviewModal.classList.add('hidden');
        if (reviewForm) reviewForm.reset();
        setRating(0);
    }

    function setRating(rating) {
        if (ratingInput) ratingInput.value = rating;
        document.querySelectorAll('.star-button').forEach((star, index) => {
            const active = index < rating;
            star.classList.toggle('text-yellow-400', active);
            star.classList.toggle('text-gray-300', !active);
        });
    }

    async function updateRequestStatus(requestId, action) {
        let bodyData = {};
        if (action === 'reject') {
            const result = await Swal.fire({
                title: 'Reject Request',
                text: 'Please provide a reason for the student:',
                input: 'textarea',
                inputPlaceholder: 'e.g. Fully booked on this date...',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, Reject it',
                inputValidator: (value) => (!value ? 'You need to write something!' : undefined),
            });
            if (!result.value) return;
            bodyData = { rejection_reason: result.value };
        } else if (action === 'cancel') {
            const result = await Swal.fire({
                title: 'Cancel Request?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel it',
            });
            if (!result.isConfirmed) return;
        } else {
            const result = await Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to proceed?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Yes, proceed',
            });
            if (!result.isConfirmed) return;
        }

        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = resolveActionUrl(requestId, action);

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrfToken;
        form.appendChild(token);

        Object.entries(bodyData).forEach(([key, value]) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    if (reviewForm) {
        reviewForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(reviewForm);
            try {
                const response = await fetch(reviewStoreUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
                    },
                });
                const payload = await response.json();
                if (!payload.success) {
                    Swal.fire('Error', payload.error || 'Unable to submit review.', 'error');
                    return;
                }
                closeReviewModal();
                Swal.fire('Thank You!', 'Review submitted.', 'success').then(() => location.reload());
            } catch (error) {
                Swal.fire('Error', 'System error', 'error');
            }
        });
    }

    document.querySelectorAll('img[data-fallback-src]').forEach((img) => {
        img.addEventListener('error', () => {
            const fallback = img.dataset.fallbackSrc;
            if (!fallback || img.dataset.fallbackApplied === '1') return;
            img.dataset.fallbackApplied = '1';
            img.src = fallback;
        });
    });

    document.addEventListener('click', (event) => {
        const actionBtn = event.target.closest('[data-request-action]');
        if (actionBtn) {
            updateRequestStatus(actionBtn.dataset.requestId, actionBtn.dataset.requestAction);
            return;
        }

        const openReviewBtn = event.target.closest('[data-open-review]');
        if (openReviewBtn) {
            if (reviewServiceRequestId) reviewServiceRequestId.value = openReviewBtn.dataset.openReview;
            if (reviewModal) reviewModal.classList.remove('hidden');
            return;
        }

        if (event.target.closest('[data-close-review]')) {
            closeReviewModal();
            return;
        }

        const setRatingBtn = event.target.closest('[data-set-rating]');
        if (setRatingBtn) {
            setRating(parseInt(setRatingBtn.dataset.setRating, 10));
        }
    });
})();
