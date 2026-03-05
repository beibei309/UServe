window.UServeAdmin.register('studentStatusIndex', 'adminStudentStatusIndexConfig', () => {
    function triggerReminder(studentId, studentName) {
        if (!studentId) return;
        Swal.fire({
            title: 'Send Graduation Reminder?',
            text: `Are you sure you want to send an email reminder to ${studentName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#eab308',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, send email',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            borderRadius: '0.5rem',
        }).then((result) => {
            if (!result.isConfirmed) return;
            Swal.fire({
                title: 'Sending...',
                text: 'Please wait while we send the email.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            document.getElementById(`reminder-form-${studentId}`).submit();
        });
    }

    window.confirmSendReminder = function (studentId, studentName) {
        triggerReminder(studentId, studentName);
    };

    document.addEventListener('click', (event) => {
        const reminderTrigger = event.target.closest('[data-reminder-send]');
        if (!reminderTrigger) return;
        triggerReminder(reminderTrigger.dataset.studentId, reminderTrigger.dataset.studentName);
    });

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-confirm-message]');
        if (!form) return;
        const message = form.dataset.confirmMessage || 'Are you sure?';
        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });

    document.querySelectorAll('[data-auto-submit-filter]').forEach((select) => {
        select.addEventListener('change', () => {
            const form = select.closest('form');
            if (form) form.submit();
        });
    });
});

window.UServeAdmin.boot('studentStatusIndex');
