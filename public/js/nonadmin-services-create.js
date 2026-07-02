(() => {
    const config = document.getElementById('servicesCreateConfig');
    if (!config) return;

    const defaultSchedule = JSON.parse(config.dataset.defaultSchedule || '{}');
    const storeUrl = config.dataset.storeUrl || '';
    const manageUrl = config.dataset.manageUrl || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const MAX_IMAGE_SIZE_BYTES = 1024 * 1024;
    const MAX_TITLE_CHARS = 255;
    const MAX_DESC_CHARS = 5000;
    const MAX_PACKAGE_DESC_CHARS = 1500;

    function plainTextLengthFromHtml(html) {
        if (!html) return 0;
        const temp = document.createElement('div');
        temp.innerHTML = html;
        return (temp.textContent || temp.innerText || '').trim().length;
    }

    function validateClientLimitsBeforeSubmit() {
        const title = (document.getElementById('title')?.value || '').trim();
        if (title.length > MAX_TITLE_CHARS) {
            return `Service title cannot exceed ${MAX_TITLE_CHARS} characters.`;
        }

        const imageFile = document.getElementById('image')?.files?.[0];
        if (imageFile && imageFile.size > MAX_IMAGE_SIZE_BYTES) {
            return 'Service image must not be larger than 1 MB.';
        }

        const mainDescLength = plainTextLengthFromHtml(document.getElementById('input-main')?.value || '');
        if (mainDescLength > MAX_DESC_CHARS) {
            return `Service description cannot exceed ${MAX_DESC_CHARS} characters.`;
        }

        const basicDescLength = plainTextLengthFromHtml(document.getElementById('input-basic')?.value || '');
        if (basicDescLength > MAX_PACKAGE_DESC_CHARS) {
            return `Basic package description cannot exceed ${MAX_PACKAGE_DESC_CHARS} characters.`;
        }

        const standardDescLength = plainTextLengthFromHtml(document.getElementById('input-standard')?.value || '');
        if (standardDescLength > MAX_PACKAGE_DESC_CHARS) {
            return `Standard package description cannot exceed ${MAX_PACKAGE_DESC_CHARS} characters.`;
        }

        const premiumDescLength = plainTextLengthFromHtml(document.getElementById('input-premium')?.value || '');
        if (premiumDescLength > MAX_PACKAGE_DESC_CHARS) {
            return `Premium package description cannot exceed ${MAX_PACKAGE_DESC_CHARS} characters.`;
        }

        return null;
    }

    const toolbarOptions = [['bold', 'italic', 'underline'], [{ list: 'bullet' }]];
    function setupQuill(editorId, inputId, placeholder) {
        const quill = new Quill(`#${editorId}`, {
            theme: 'snow',
            modules: { toolbar: toolbarOptions },
            placeholder,
        });
        quill.on('text-change', () => {
            document.getElementById(inputId).value = quill.root.innerHTML;
        });
    }

    window.scheduleHandler = function scheduleHandler() {
        return {
            isSessionBased: true,
            days: [
                { key: 'mon', name: 'Monday' },
                { key: 'tue', name: 'Tuesday' },
                { key: 'wed', name: 'Wednesday' },
                { key: 'thu', name: 'Thursday' },
                { key: 'fri', name: 'Friday' },
                { key: 'sat', name: 'Saturday' },
                { key: 'sun', name: 'Sunday' },
            ],
            schedule: defaultSchedule,
            showBulk: false,
            bulkStart: '09:00',
            bulkEnd: '17:00',
            applyBulkTime() {
                for (const dayKey in this.schedule) {
                    if (!this.schedule[dayKey].enabled) continue;
                    this.schedule[dayKey].start = this.bulkStart;
                    this.schedule[dayKey].end = this.bulkEnd;
                }
                this.showBulk = false;
            },
        };
    };

    let fpInstance;
    function quickBlockDates(amount, unit) {
        if (!fpInstance) return;
        const daysToAdd = unit === 'week' ? amount * 7 : amount * 30;
        const today = new Date();
        const newDates = [];
        for (let i = 0; i < daysToAdd; i += 1) {
            const d = new Date(today);
            d.setDate(today.getDate() + i);
            newDates.push(d.toISOString().split('T')[0]);
        }
        const current = fpInstance.selectedDates.map((d) => d.toISOString().split('T')[0]);
        fpInstance.setDate([...new Set([...current, ...newDates])], true);
    }

    function clearUnavailableDates() {
        if (fpInstance) fpInstance.clear();
    }

    function updateHeader(activeId) {
        const map = { overview: 0, pricing: 1, description: 2, availability: 3 };
        const activeIndex = map[activeId];
        document.querySelectorAll('.step-link').forEach((link, index) => {
            const circle = link.querySelector('span');
            link.className = 'step-link min-w-28 sm:min-w-0 sm:flex-1 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm flex items-center justify-center transition-colors pointer-events-none';
            circle.className = 'w-6 h-6 rounded-full flex items-center justify-center text-xs mr-2 font-bold';
            if (index < activeIndex) {
                link.classList.add('step-completed', 'border-green-500', 'text-green-600');
                circle.classList.add('bg-green-100', 'text-green-600');
                circle.innerHTML = '✓';
            } else if (index === activeIndex) {
                link.classList.add('step-active', 'border-indigo-500', 'text-indigo-600');
                circle.classList.add('bg-indigo-100', 'text-indigo-600', 'ring-1', 'ring-indigo-600');
                circle.innerHTML = index + 1;
            } else {
                link.classList.add('step-inactive', 'border-transparent', 'text-gray-400');
                circle.classList.add('bg-gray-100', 'text-gray-500');
                circle.innerHTML = index + 1;
            }

            if (index === activeIndex && window.innerWidth < 640) {
                link.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        });
    }

    function nextStep(currentId, nextId) {
        if (currentId === 'overview' && nextId === 'pricing' && (!document.getElementById('title').value || !document.getElementById('category_id').value)) {
            Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'Please enter a Title and Category.' });
            return;
        }

        if (currentId === 'overview' && nextId === 'pricing') {
            const titleLength = (document.getElementById('title')?.value || '').trim().length;
            if (titleLength > MAX_TITLE_CHARS) {
                Swal.fire({ icon: 'warning', title: 'Title Too Long', text: `Service title cannot exceed ${MAX_TITLE_CHARS} characters.` });
                return;
            }
        }

        if (currentId === 'pricing' && nextId === 'description') {
            const basicPrice = document.getElementById('basic_price').value;
            const basicDuration = document.querySelector('input[name="packages[0][duration]"]').value;
            const basicDesc = document.getElementById('input-basic').value;

            if (!basicPrice) {
                Swal.fire({ icon: 'warning', title: 'Missing Price', text: 'Please set a price (RM) for the Basic Package.' });
                return;
            }
            if (!basicDuration) {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Missing Duration', 
                    html: `Please tell students how long this package takes.<br><br><span class="text-sm text-gray-500">Examples: <b>"1 Hour"</b>, <b>"2 Days"</b>, or <b>"1 Week"</b></span>` 
                });
                return;
            }
            if (!basicDesc || basicDesc === '<p><br></p>') {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Missing Details', 
                    html: `Please describe what's included in the Basic Package so students know what they're paying for.` 
                });
                return;
            }
        }

        if (currentId === 'description' && nextId === 'availability' && (!document.getElementById('input-main').value || document.getElementById('input-main').value === '<p><br></p>')) {
            Swal.fire({ icon: 'warning', title: 'Missing Description', text: 'Please provide a full description for your service.' });
            return;
        }
        
        document.querySelectorAll('.tab-section').forEach((el) => el.classList.add('hidden'));
        document.getElementById(nextId).classList.remove('hidden');
        updateHeader(nextId);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    async function submitForm() {
        const form = document.getElementById('createServiceForm');
        if (fpInstance) document.getElementById('unavailableDates').value = fpInstance.input.value;

        const booksATime = form.querySelector('input[name="is_session_based"]')?.value === '1';
        form.querySelectorAll('[data-package-frequency]').forEach((input) => {
            input.value = booksATime ? 'Per Session' : 'Per Task';
        });

        const validationError = validateClientLimitsBeforeSubmit();
        if (validationError) {
            Swal.fire({ icon: 'warning', title: 'Validation Error', text: validationError });
            return;
        }

        const formData = new FormData(form);
        Swal.fire({ title: 'Publishing...', didOpen: () => Swal.showLoading() });

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: formData,
            });
            const data = await response.json();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Service published successfully.',
                    confirmButtonText: 'Go to Dashboard',
                    confirmButtonColor: '#10b981',
                }).then(() => {
                    window.location.href = manageUrl;
                });
                return;
            }

            let msg = data.message || 'Please check your inputs.';
            if (data.errors) msg = Object.values(data.errors).flat().join('\n');
            else if (data.error) msg = data.error;
            Swal.fire({ icon: 'error', title: 'Publication Failed', text: msg });
        } catch (error) {
            Swal.fire('System Error', 'Please check your connection.', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        setupQuill('editor-basic', 'input-basic', 'e.g. 1 hour online consultation...');
        setupQuill('editor-standard', 'input-standard', 'Describe standard package...');
        setupQuill('editor-premium', 'input-premium', 'Describe premium package...');
        setupQuill('editor-main', 'input-main', 'Provide a comprehensive description...');
        fpInstance = flatpickr('#unavailableDates', {
            mode: 'multiple',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: false,
            disableMobile: true,
            minDate: 'today',
            conjunction: ', ',
            locale: { firstDayOfWeek: 1 },
        });
        document.getElementById('offer_packages')?.addEventListener('change', function onToggle() {
            const extra = document.getElementById('extraPackages');
            this.checked ? extra.classList.remove('hidden') : extra.classList.add('hidden');
        });

        const imageInput = document.getElementById('image');
        imageInput?.addEventListener('change', () => {
            const selectedFile = imageInput.files?.[0];
            if (!selectedFile) return;
            if (selectedFile.size > MAX_IMAGE_SIZE_BYTES) {
                imageInput.value = '';
                Swal.fire({
                    icon: 'warning',
                    title: 'Image Too Large',
                    text: 'Service image must not be larger than 1 MB.',
                });
            }
        });
    });

    document.addEventListener('click', (event) => {
        const stepTrigger = event.target.closest('[data-next-step]');
        if (stepTrigger) {
            const [from, to] = stepTrigger.dataset.nextStep.split('|');
            nextStep(from, to);
            return;
        }

        const quickBlockTrigger = event.target.closest('[data-quick-block]');
        if (quickBlockTrigger) {
            const [amount, unit] = quickBlockTrigger.dataset.quickBlock.split('|');
            quickBlockDates(parseInt(amount, 10), unit);
            return;
        }

        if (event.target.closest('[data-clear-unavailable]')) {
            clearUnavailableDates();
            return;
        }

        if (event.target.closest('[data-submit-form]')) {
            submitForm();
        }
    });
})();
