window.UServeAdmin.register('studentStatusIndex', 'adminStudentStatusIndexConfig', (config) => {
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

    // SweetAlert2 confirmation for delete
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form');
        if (!form) return;
        if (form.method === 'post' && form.querySelector('input[name="_method"][value="DELETE"]')) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'This status record will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });

    // Show success notification if present in config
    if (config && config.dataset.successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: config.dataset.successMessage,
            showConfirmButton: false,
            timer: 1800
        });
    }

    document.querySelectorAll('[data-auto-submit-filter]').forEach((select) => {
        select.addEventListener('change', () => {
            const form = select.closest('form');
            if (form) form.submit();
        });
    });
});

window.UServeAdmin.boot('studentStatusIndex');
