(() => {
    const config = document.getElementById('serviceRequestsIndexConfig');
    if (!config) return;

    const defaultStatusTab = config.dataset.defaultStatusTab || 'pending';
    const reviewUrl = config.dataset.reviewUrl || '/reviews';
    const reportUrlTemplate = config.dataset.reportUrlTemplate || '';
    const paymentUrlTemplate = config.dataset.paymentUrlTemplate || '';
    const successMessage = config.dataset.successMessage || '';
    const errorMessage = config.dataset.errorMessage || '';

    const paymentModal = document.getElementById('paymentModal');
    const paymentProofForm = document.getElementById('paymentProofForm');
    const paymentModalPrice = document.getElementById('paymentModalPrice');
    const fileInput = document.getElementById('dropzone-file');
    const fileNamePreview = document.getElementById('fileNamePreview');
    const reviewModal = document.getElementById('reviewModal');
    const reviewForm = document.getElementById('reviewForm');
    const reviewServiceRequestId = document.getElementById('reviewServiceRequestId');
    const ratingInput = document.getElementById('rating');
    const requestSearch = document.getElementById('request-search');
    const categoryFilter = document.getElementById('category-filter');

    function showStatusTab(status) {
        document.querySelectorAll('.sr-status-tab-content').forEach((content) => content.classList.add('hidden'));
        document.querySelectorAll('.sr-status-tab-button').forEach((button) => {
            button.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
            button.classList.add('text-gray-500', 'hover:text-custom-teal');
        });

        const targetContent = document.getElementById(`${status}-content`);
        const targetTab = document.getElementById(`${status}-tab`);
        if (targetContent) targetContent.classList.remove('hidden');
        if (targetTab) {
            targetTab.classList.remove('text-gray-500', 'hover:text-custom-teal');
            targetTab.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
        }
    }

    function resetStars() {
        document.querySelectorAll('.star-button').forEach((button) => {
            button.classList.remove('text-yellow-400');
            button.classList.add('text-gray-300');
        });
    }

    function setRating(rating) {
        ratingInput.value = rating;
        document.querySelectorAll('.star-button').forEach((button, index) => {
            const active = index < rating;
            button.classList.toggle('text-yellow-400', active);
            button.classList.toggle('text-gray-300', !active);
        });
    }

    function closePaymentModal() {
        if (!paymentModal) return;
        paymentModal.classList.add('hidden');
        if (paymentProofForm) paymentProofForm.reset();
        if (fileNamePreview) {
            fileNamePreview.textContent = '';
            fileNamePreview.classList.add('hidden');
        }
    }

    function closeReviewModal() {
        if (!reviewModal) return;
        reviewModal.classList.add('hidden');
        if (reviewForm) reviewForm.reset();
        resetStars();
    }

    function filterItems() {
        if (!requestSearch || !categoryFilter) return;
        const query = requestSearch.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const activeContent = document.querySelector('.sr-status-tab-content:not(.hidden)');
        if (!activeContent) return;

        activeContent.querySelectorAll('.sr-request-item').forEach((item) => {
            const text = item.textContent.toLowerCase();
            const itemCategory = item.getAttribute('data-category');
            const matchesSearch = text.includes(query);
            const matchesCategory = selectedCategory === '' || selectedCategory === itemCategory;
            item.style.display = matchesSearch && matchesCategory ? '' : 'none';
        });
    }

    showStatusTab(defaultStatusTab);
    filterItems();

    if (successMessage) {
        Swal.fire({
            title: 'Success!',
            text: successMessage,
            icon: 'success',
            confirmButtonColor: '#4F46E5',
            confirmButtonText: 'OK',
        });
    }

    if (errorMessage) {
        Swal.fire({
            title: 'Error!',
            text: errorMessage,
            icon: 'error',
            confirmButtonColor: '#d33',
        });
    }

    if (requestSearch) requestSearch.addEventListener('input', filterItems);
    if (categoryFilter) categoryFilter.addEventListener('change', filterItems);
    if (fileInput) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0];
            if (!file || !fileNamePreview) return;
            fileNamePreview.textContent = `Selected: ${file.name}`;
            fileNamePreview.classList.remove('hidden');
        });
    }

    if (reviewForm) {
        reviewForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const submitBtn = reviewForm.querySelector('button[type="submit"]');
            const formData = new FormData(reviewForm);
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            try {
                const response = await fetch(reviewUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                });
                const payload = await response.json();
                if (!payload.success) {
                    throw new Error(payload.error || 'Failed');
                }
                closeReviewModal();
                Swal.fire('Thank You!', 'Your review has been submitted.', 'success').then(() => location.reload());
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit';
            }
        });
    }

    document.addEventListener('click', (event) => {
        const tabTrigger = event.target.closest('[data-status-tab]');
        if (tabTrigger) {
            showStatusTab(tabTrigger.dataset.statusTab);
            filterItems();
            return;
        }

        const cancelTrigger = event.target.closest('[data-cancel-request]');
        if (cancelTrigger) {
            const requestId = cancelTrigger.dataset.cancelRequest;
            Swal.fire({
                title: 'Cancel Request?',
                text: 'Are you sure you want to cancel this request?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, cancel it',
            }).then((result) => {
                if (!result.isConfirmed) return;
                const form = document.getElementById(`cancel-form-${requestId}`);
                if (!form) return;
                Swal.fire({
                    title: 'Cancelling...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });
                form.submit();
            });
            return;
        }

        const reportTrigger = event.target.closest('[data-open-report]');
        if (reportTrigger) {
            const requestId = reportTrigger.dataset.openReport;
            Swal.fire({
                title: 'Report Issue',
                input: 'textarea',
                inputPlaceholder: 'Explain what happened...',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                confirmButtonText: 'Submit Report',
                preConfirm: (value) => {
                    if (!value || !value.trim()) {
                        Swal.showValidationMessage('Reason is required');
                    }
                    return value;
                },
            }).then((result) => {
                if (!result.isConfirmed) return;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = reportUrlTemplate.replace('__ID__', requestId);

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(token);

                const reason = document.createElement('input');
                reason.type = 'hidden';
                reason.name = 'dispute_reason';
                reason.value = result.value.trim();
                form.appendChild(reason);

                document.body.appendChild(form);
                form.submit();
            });
            return;
        }

        const paymentTrigger = event.target.closest('[data-open-payment]');
        if (paymentTrigger) {
            const requestId = paymentTrigger.dataset.openPayment;
            const price = paymentTrigger.dataset.paymentPrice || '0.00';
            if (paymentProofForm) {
                paymentProofForm.action = paymentUrlTemplate.replace('__ID__', requestId);
            }
            if (paymentModalPrice) paymentModalPrice.textContent = price;
            if (paymentModal) paymentModal.classList.remove('hidden');
            return;
        }

        if (event.target.closest('[data-close-payment]')) {
            closePaymentModal();
            return;
        }

        const openReview = event.target.closest('[data-open-review]');
        if (openReview) {
            if (reviewServiceRequestId) reviewServiceRequestId.value = openReview.dataset.openReview;
            if (reviewModal) reviewModal.classList.remove('hidden');
            return;
        }

        if (event.target.closest('[data-close-review]')) {
            closeReviewModal();
            return;
        }

        const setRatingTrigger = event.target.closest('[data-set-rating]');
        if (setRatingTrigger) {
            setRating(parseInt(setRatingTrigger.dataset.setRating, 10));
        }
    });

    window.addEventListener('click', (event) => {
        if (event.target === paymentModal) closePaymentModal();
    });
})();
