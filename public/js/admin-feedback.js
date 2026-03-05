window.UServeAdmin.register('feedback', 'adminModuleFeedbackConfig', () => {
    window.submitFeedbackActionWithReason = function (event, form) {
        event.preventDefault();

        const actionLabel = form.getAttribute('data-action-label') || 'this action';
        const reason = window.prompt(`Please enter reason for ${actionLabel}:`);
        if (reason === null) {
            return false;
        }

        const trimmed = reason.trim();
        if (!trimmed) {
            alert('Reason is required.');
            return false;
        }

        const reasonInput = form.querySelector('input[name="reason"]');
        if (!reasonInput) {
            return false;
        }

        reasonInput.value = trimmed;
        form.submit();
        return true;
    };

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-feedback-reason]');
        if (!form) return;
        event.preventDefault();
        window.submitFeedbackActionWithReason(event, form);
    });
});

window.UServeAdmin.boot('feedback');
