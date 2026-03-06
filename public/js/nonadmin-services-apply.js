(() => {
    const config = document.getElementById('servicesApplyConfig');
    const form = document.getElementById('applyServiceForm');
    if (!config || !form) return;

    const canApply = config.dataset.canApply === 'true';
    const storeUrl = config.dataset.storeUrl || '';
    const applicationsUrl = config.dataset.applicationsUrl || '';
    const blockedMessage = config.dataset.blockedMessage || 'Please complete your account verification first.';

    function showMessage(message, type) {
        const messageContainer = document.getElementById('messageContainer');
        const messageDiv = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        messageDiv.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg mb-4 transform transition-all duration-300 translate-x-full max-w-md`;
        messageDiv.textContent = message;
        messageContainer.appendChild(messageDiv);

        setTimeout(() => {
            messageDiv.classList.remove('translate-x-full');
        }, 100);

        setTimeout(() => {
            messageDiv.classList.add('translate-x-full');
            setTimeout(() => {
                if (messageContainer.contains(messageDiv)) {
                    messageContainer.removeChild(messageDiv);
                }
            }, 300);
        }, 5000);
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!canApply) {
            showMessage(blockedMessage, 'error');
            return;
        }

        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Submitting...
        `;

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                    Accept: 'application/json',
                },
                body: formData,
            });

            const data = await response.json();
            if (response.ok) {
                showMessage(
                    data.message || 'Service application submitted successfully! Students will be notified and can contact you soon.',
                    'success',
                );
                setTimeout(() => {
                    window.location.href = applicationsUrl;
                }, 2000);
                return;
            }

            showMessage(data.error || data.message || 'An error occurred. Please try again.', 'error');
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        } catch (error) {
            showMessage('An error occurred. Please try again.', 'error');
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    });
})();
