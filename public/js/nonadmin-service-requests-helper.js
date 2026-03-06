(() => {
    const config = document.getElementById('serviceRequestsHelperConfig');
    if (!config) return;

    const defaultStatusTab = config.dataset.defaultStatusTab || 'pending';
    const finalizeUrlTemplate = config.dataset.finalizeUrlTemplate || '';
    const reviewsStoreUrl = config.dataset.reviewsStoreUrl || '/reviews';
    const reviewsReplyUrlTemplate = config.dataset.reviewsReplyUrlTemplate || '';

    const proofModal = document.getElementById('proofModal');
    const proofImage = document.getElementById('proofImage');
    const proofPdf = document.getElementById('proofPdf');
    const proofFallback = document.getElementById('proofFallback');
    const proofLink = document.getElementById('proofLink');
    const finalizeOrderForm = document.getElementById('finalizeOrderForm');
    const finalizeOutcome = document.getElementById('finalizeOutcome');
    const reviewModal = document.getElementById('reviewModal');
    const sellerReviewModal = document.getElementById('sellerReviewModal');
    const sellerReviewForm = document.getElementById('sellerReviewForm');
    const searchInput = document.getElementById('request-search');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function showStatusTab(status) {
        document.querySelectorAll('.sr-status-tab-content').forEach((content) => content.classList.add('hidden'));
        document.querySelectorAll('.sr-status-tab-button').forEach((button) => {
            button.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
            button.classList.add('text-gray-500', 'hover:text-custom-teal');
        });

        const targetContent = document.getElementById(`${status}-content`);
        if (targetContent) targetContent.classList.remove('hidden');
        const targetButton = document.getElementById(`${status}-tab`);
        if (targetButton) {
            targetButton.classList.remove('text-gray-500', 'hover:text-custom-teal');
            targetButton.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
        }
    }

    function filterBySearch() {
        if (!searchInput) return;
        const query = searchInput.value.toLowerCase().trim();
        document.querySelectorAll('.sr-request-item').forEach((item) => {
            const matches = item.textContent.toLowerCase().includes(query);
            item.style.display = matches ? '' : 'none';
        });
    }

    function closeProofModal() {
        if (!proofModal) return;
        proofModal.classList.add('hidden');
        if (proofImage) proofImage.src = '';
        if (proofPdf) proofPdf.src = '';
    }

    function openProofModal(fileUrl, requestId) {
        if (!proofModal || !finalizeOrderForm) return;
        finalizeOrderForm.action = finalizeUrlTemplate.replace('__ID__', requestId);

        proofImage.classList.add('hidden');
        proofPdf.classList.add('hidden');
        proofFallback.classList.add('hidden');
        proofImage.src = '';
        proofPdf.src = '';
        proofLink.href = fileUrl;

        let extension = '';
        try {
            const cleanPath = new URL(fileUrl, window.location.origin).pathname;
            const parts = cleanPath.split('.');
            extension = parts.length > 1 ? parts.pop().toLowerCase() : '';
        } catch (error) {
            extension = (fileUrl.split('?')[0].split('.').pop() || '').toLowerCase();
        }

        const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        if (imageTypes.includes(extension)) {
            proofImage.onerror = () => {
                proofImage.classList.add('hidden');
                proofFallback.classList.remove('hidden');
            };
            proofImage.src = fileUrl;
            proofImage.classList.remove('hidden');
        } else if (extension === 'pdf') {
            proofPdf.src = fileUrl;
            proofPdf.classList.remove('hidden');
        } else {
            proofFallback.classList.remove('hidden');
        }

        proofModal.classList.remove('hidden');
    }

    function submitDecision(outcome) {
        if (!finalizeOrderForm || !finalizeOutcome) return;
        finalizeOutcome.value = outcome;
        const title = outcome === 'paid' ? 'Confirm Payment?' : 'Report Issue?';
        const text = outcome === 'paid' ? 'This will complete the order.' : 'This will flag the order as unpaid. Are you sure?';
        const color = outcome === 'paid' ? '#16a34a' : '#dc2626';

        Swal.fire({
            title,
            text,
            icon: outcome === 'paid' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: color,
            confirmButtonText: 'Yes, Proceed',
        }).then((result) => {
            if (result.isConfirmed) finalizeOrderForm.submit();
        });
    }

    function openBuyerReviewModal(reviewJson, requesterName) {
        const review = typeof reviewJson === 'string' ? JSON.parse(reviewJson) : reviewJson;
        if (!review || !reviewModal) return;

        document.getElementById('modalRequesterName').innerText = requesterName;
        document.getElementById('modalComment').innerText = review.hr_comment || review.comment || 'No textual comment provided.';
        const reviewCreatedAt = review.created_at || review.hr_created_at;
        document.getElementById('modalDate').innerText = reviewCreatedAt
            ? new Date(reviewCreatedAt).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' })
            : 'Unknown date';

        let starsHtml = '';
        for (let i = 1; i <= 5; i += 1) {
            starsHtml += `<i class="${i <= (review.hr_rating || review.rating || 0) ? 'fas' : 'far'} fa-star"></i>`;
        }
        document.getElementById('modalStars').innerHTML = starsHtml;

        const replyForm = document.getElementById('replyForm');
        const viewReplyContainer = document.getElementById('viewReplyContainer');
        if (review.hr_reply || review.reply) {
            replyForm.classList.add('hidden');
            viewReplyContainer.classList.remove('hidden');
            document.getElementById('modalReplyText').innerText = review.hr_reply || review.reply;
            const repliedAt = review.hr_replied_at || review.replied_at;
            document.getElementById('modalRepliedAt').innerText = repliedAt ? new Date(repliedAt).toLocaleDateString() : 'Unknown date';
        } else {
            viewReplyContainer.classList.add('hidden');
            replyForm.classList.remove('hidden');
            const reviewId = review.hr_id || review.id;
            if (!reviewId) {
                Swal.fire({ icon: 'error', title: 'Invalid review', text: 'Unable to find review ID for reply.' });
                return;
            }
            replyForm.action = reviewsReplyUrlTemplate.replace('__ID__', reviewId);
        }

        reviewModal.classList.remove('hidden');
    }

    function closeBuyerReviewModal() {
        if (reviewModal) reviewModal.classList.add('hidden');
    }

    function openSellerReviewModal(requestId) {
        if (!sellerReviewModal || !sellerReviewForm) return;
        sellerReviewForm.reset();
        document.getElementById('sellerReviewRequestId').value = requestId;
        document.getElementById('sellerReviewRating').value = '';
        document.getElementById('ratingError').classList.add('hidden');
        document.querySelectorAll('.seller-star-input').forEach((star) => {
            star.classList.remove('fas', 'text-yellow-400');
            star.classList.add('far', 'text-gray-300');
        });
        sellerReviewModal.classList.remove('hidden');
    }

    function closeSellerReviewModal() {
        if (sellerReviewModal) sellerReviewModal.classList.add('hidden');
    }

    function setSellerRating(rating) {
        document.getElementById('sellerReviewRating').value = rating;
        document.getElementById('ratingError').classList.add('hidden');
        document.querySelectorAll('.seller-star-input').forEach((star) => {
            const val = parseInt(star.dataset.value, 10);
            const active = val <= rating;
            star.classList.toggle('fas', active);
            star.classList.toggle('text-yellow-400', active);
            star.classList.toggle('far', !active);
            star.classList.toggle('text-gray-300', !active);
        });
    }

    async function submitSellerReview(event) {
        event.preventDefault();
        const rating = document.getElementById('sellerReviewRating').value;
        const requestId = document.getElementById('sellerReviewRequestId').value;
        const comment = document.getElementById('sellerComment').value;

        if (!rating) {
            document.getElementById('ratingError').classList.remove('hidden');
            return;
        }

        Swal.fire({
            title: 'Submitting Review...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        try {
            const response = await fetch(reviewsStoreUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    service_request_id: requestId,
                    rating,
                    comment,
                }),
            });
            const payload = await response.json();
            if (!payload.success) {
                Swal.fire('Error', payload.message || 'Could not submit review', 'error');
                return;
            }
            closeSellerReviewModal();
            Swal.fire({
                title: 'Review Submitted!',
                text: 'Thank you for rating the Buyer.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
            }).then(() => location.reload());
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        }
    }

    function confirmAction(id, type, title, text, confirmColor, confirmText) {
        Swal.fire({
            title,
            text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        }).then((result) => {
            if (!result.isConfirmed) return;
            const form = document.getElementById(`${type}-form-${id}`);
            if (!form) {
                Swal.fire('Error', 'Form not found for this action.', 'error');
                return;
            }
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });
            form.submit();
        });
    }

    function openRejectModal(requestId) {
        Swal.fire({
            title: 'Reject Request?',
            text: 'Please provide a reason for the requester.',
            input: 'textarea',
            inputPlaceholder: 'e.g. I am fully booked on this date...',
            inputAttributes: { 'aria-label': 'Type your rejection reason here' },
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Reject it',
            preConfirm: (reason) => {
                if (!reason) Swal.showValidationMessage('You must provide a reason!');
                return reason;
            },
        }).then((result) => {
            if (!result.isConfirmed) return;
            const form = document.getElementById(`reject-form-${requestId}`);
            if (!form) return;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'rejection_reason';
            input.value = result.value;
            form.appendChild(input);
            form.submit();
        });
    }

    function openDisputeModal(requestId) {
        Swal.fire({
            title: 'Report / Dispute Transaction',
            html: `
                <div class="text-left text-sm text-gray-600 mb-4">Please select a reason for reporting this buyer:</div>
                <div class="flex flex-col gap-3 text-left">
                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200"><input type="radio" name="dispute_reason" value="Buyer did not confirm payment after services complete" class="mt-1"><span>Buyer did not confirm payment after services complete</span></label>
                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200"><input type="radio" name="dispute_reason" value="Buyer is unresponsive (Ghosting)" class="mt-1"><span>Buyer is unresponsive (Ghosting)</span></label>
                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200"><input type="radio" name="dispute_reason" value="Buyer refuses to pay the agreed amount" class="mt-1"><span>Buyer refuses to pay the agreed amount</span></label>
                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200"><input type="radio" name="dispute_reason" value="Buyer is demanding extra work not in agreement" class="mt-1"><span>Buyer is demanding extra work not in agreement</span></label>
                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200"><input type="radio" name="dispute_reason" value="Inappropriate behavior from buyer" class="mt-1"><span>Inappropriate behavior from buyer</span></label>
                    <label class="flex items-start gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200"><input type="radio" name="dispute_reason" value="other" class="mt-1"><span class="font-semibold">Other (Specify below)</span></label>
                </div>
                <textarea id="swal-other-reason" class="swal2-textarea hidden" placeholder="Please describe the issue in detail..." style="display:none; margin-top:15px; font-size:0.9em;"></textarea>
            `,
            showCancelButton: true,
            confirmButtonText: 'Submit Report',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            focusConfirm: false,
            didOpen: () => {
                const radios = document.querySelectorAll('input[name="dispute_reason"]');
                radios.forEach((radio) => {
                    radio.addEventListener('change', () => {
                        const other = document.getElementById('swal-other-reason');
                        if (!other) return;
                        other.style.display = radio.value === 'other' && radio.checked ? 'block' : 'none';
                        if (radio.value === 'other' && radio.checked) other.focus();
                    });
                });
            },
            preConfirm: () => {
                const selectedOption = document.querySelector('input[name="dispute_reason"]:checked');
                const otherText = document.getElementById('swal-other-reason')?.value || '';
                if (!selectedOption) {
                    Swal.showValidationMessage('Please select a reason');
                    return false;
                }
                if (selectedOption.value === 'other' && !otherText.trim()) {
                    Swal.showValidationMessage('Please specify the reason for "Other"');
                    return false;
                }
                return selectedOption.value === 'other' ? otherText : selectedOption.value;
            },
        }).then((result) => {
            if (!result.isConfirmed) return;
            const reasonInput = document.getElementById(`dispute-reason-${requestId}`);
            const form = document.getElementById(`dispute-form-${requestId}`);
            if (!reasonInput || !form) return;
            reasonInput.value = result.value;
            form.submit();
        });
    }

    function confirmCancelDispute(requestId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will withdraw your report and immediately mark the order as Completed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Complete Order',
            cancelButtonText: 'No',
        }).then((result) => {
            if (!result.isConfirmed) return;
            const form = document.getElementById(`cancel-dispute-form-${requestId}`);
            if (form) form.submit();
        });
    }

    showStatusTab(defaultStatusTab);
    filterBySearch();

    if (searchInput) searchInput.addEventListener('input', filterBySearch);
    document.querySelectorAll('[data-auto-submit-filter]').forEach((select) => {
        select.addEventListener('change', () => {
            const form = select.closest('form');
            if (form) form.submit();
        });
    });
    if (sellerReviewForm) sellerReviewForm.addEventListener('submit', submitSellerReview);

    document.addEventListener('click', (event) => {
        const tab = event.target.closest('[data-status-tab]');
        if (tab) {
            showStatusTab(tab.dataset.statusTab);
            return;
        }

        const openReject = event.target.closest('[data-open-reject]');
        if (openReject) {
            openRejectModal(openReject.dataset.openReject);
            return;
        }

        const accept = event.target.closest('[data-accept-request]');
        if (accept) {
            confirmAction(accept.dataset.acceptRequest, 'accept', 'Accept Request?', 'You can start working on this service once accepted.', '#16a34a', 'Yes, Accept');
            return;
        }

        const start = event.target.closest('[data-mark-progress]');
        if (start) {
            confirmAction(start.dataset.markProgress, 'progress', 'Start Work?', 'The status will change to In Progress.', '#2563eb', 'Yes, Start');
            return;
        }

        const finish = event.target.closest('[data-mark-finished]');
        if (finish) {
            confirmAction(finish.dataset.markFinished, 'finish-work', 'Finish Work?', 'This will notify Buyer the work is done and waiting for payment.', '#2563eb', 'Yes, Finish');
            return;
        }

        const openProof = event.target.closest('[data-open-proof]');
        if (openProof) {
            openProofModal(openProof.dataset.proofUrl, openProof.dataset.openProof);
            return;
        }

        if (event.target.closest('[data-close-proof]')) {
            closeProofModal();
            return;
        }

        const decision = event.target.closest('[data-submit-decision]');
        if (decision) {
            submitDecision(decision.dataset.submitDecision);
            return;
        }

        const dispute = event.target.closest('[data-open-dispute]');
        if (dispute) {
            openDisputeModal(dispute.dataset.openDispute);
            return;
        }

        const cancelDispute = event.target.closest('[data-cancel-dispute]');
        if (cancelDispute) {
            confirmCancelDispute(cancelDispute.dataset.cancelDispute);
            return;
        }

        const openBuyerReview = event.target.closest('[data-open-buyer-review]');
        if (openBuyerReview) {
            openBuyerReviewModal(openBuyerReview.dataset.openBuyerReview, openBuyerReview.dataset.reviewerName || '');
            return;
        }

        if (event.target.closest('[data-close-review]')) {
            closeBuyerReviewModal();
            return;
        }

        const openSellerReview = event.target.closest('[data-open-seller-review]');
        if (openSellerReview) {
            openSellerReviewModal(openSellerReview.dataset.openSellerReview);
            return;
        }

        if (event.target.closest('[data-close-seller-review]')) {
            closeSellerReviewModal();
            return;
        }

        const setSellerRatingTrigger = event.target.closest('[data-set-seller-rating]');
        if (setSellerRatingTrigger) {
            setSellerRating(parseInt(setSellerRatingTrigger.dataset.setSellerRating, 10));
        }
    });
})();
