(() => {
    function handleDownloadPdf() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Download Certificate',
                text: 'Your browser print dialog will open. Select "Save as PDF" to download this certificate in A4 format.',
                icon: 'info',
                confirmButtonColor: '#2563eb',
            }).then(() => {
                window.print();
            });
            return;
        }

        window.print();
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
