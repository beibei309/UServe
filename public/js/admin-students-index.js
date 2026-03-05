window.UServeAdmin.register('studentsIndex', 'adminModuleStudentsIndexConfig', (config) => {
    if (!config) return;

    const banRouteTemplate = config.dataset.banRouteTemplate || '';
    const successMessage = config.dataset.successMessage || '';
    let selectedStudentId = null;

    window.openBanModal = function (id) {
        selectedStudentId = id;
        document.getElementById('banModal').classList.remove('hidden');
    };

    window.closeBanModal = function () {
        document.getElementById('banModal').classList.add('hidden');
        document.getElementById('banReason').value = '';
    };

    window.submitBan = function () {
        const reason = document.getElementById('banReason').value.trim();
        if (!reason) {
            alert('Please enter a ban reason.');
            return;
        }

        const form = document.getElementById('banForm');
        const csrfValue = form.querySelector('input[name="_token"]')?.value || '';
        form.action = banRouteTemplate.replace(':id', selectedStudentId);
        form.innerHTML = `<input type="hidden" name="_token" value="${csrfValue}"><input type="hidden" name="blacklist_reason" value="${reason}">`;
        form.submit();
    };

    document.addEventListener('click', (event) => {
        const banOpen = event.target.closest('[data-ban-open]');
        if (banOpen) {
            window.openBanModal(banOpen.dataset.studentId);
            return;
        }

        if (event.target.closest('[data-ban-close]')) {
            window.closeBanModal();
            return;
        }

        if (event.target.closest('[data-ban-submit]')) {
            window.submitBan();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-student-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const form = button.closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This student record will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        document.querySelectorAll('.unban-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const form = button.closest('form');
                Swal.fire({
                    title: 'Reactivate student account?',
                    text: 'This student will regain access to the system immediately.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Reactivate',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: successMessage,
                showConfirmButton: false,
                timer: 2000,
                iconColor: '#10b981',
            });
        }
    });
});

window.UServeAdmin.boot('studentsIndex');
