window.UServeAdmin.register('legalPages', 'adminLegalPagesConfig', () => {
    function initLegalEditor(editorId, inputId) {
        const editorElement = document.getElementById(editorId);
        const hiddenInput = document.getElementById(inputId);
        if (!editorElement || !hiddenInput || typeof Quill === 'undefined') {
            return;
        }

        if (editorElement.dataset.quillInitialized === '1') {
            return;
        }

        const quill = new Quill(editorElement, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });

        quill.root.innerHTML = hiddenInput.value || '';

        const form = editorElement.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                hiddenInput.value = quill.root.innerHTML;
            });
        }

        editorElement.dataset.quillInitialized = '1';
    }

    let attempts = 0;
    const maxAttempts = 40;
    const waitMs = 120;

    const bootEditors = () => {
        initLegalEditor('terms-editor', 'terms-content-input');
        initLegalEditor('privacy-editor', 'privacy-content-input');

        const termsReady = document.getElementById('terms-editor')?.dataset.quillInitialized === '1';
        const privacyReady = document.getElementById('privacy-editor')?.dataset.quillInitialized === '1';
        if ((termsReady || !document.getElementById('terms-editor')) && (privacyReady || !document.getElementById('privacy-editor'))) {
            return;
        }

        attempts += 1;
        if (attempts < maxAttempts) {
            window.setTimeout(bootEditors, waitMs);
        }
    };

    window.addEventListener('upsi2u:editor-tools-ready', bootEditors, { once: true });
    bootEditors();
});

window.UServeAdmin.boot('legalPages');
