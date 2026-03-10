(() => {
    function handleDownloadPdf() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'PDF Download',
                text: 'PDF download functionality will be implemented here.',
                icon: 'info',
                confirmButtonColor: '#2563eb',
            });
            return;
        }

        alert('PDF download functionality will be implemented here.');
    }

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-certificate-print]')) {
            window.print();
            return;
        }

        if (event.target.closest('[data-certificate-download]')) {
            handleDownloadPdf();
        }
    });
})();
