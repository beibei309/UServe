(() => {
    const config = document.getElementById('servicesCreateConfig');
    if (!config) return;

    const defaultSchedule = JSON.parse(config.dataset.defaultSchedule || '{}');
    const storeUrl = config.dataset.storeUrl || '';
    const manageUrl = config.dataset.manageUrl || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

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
            link.className = 'step-link w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors pointer-events-none';
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
        });
    }

    function nextStep(currentId, nextId) {
        if (currentId === 'overview' && nextId === 'pricing' && (!document.getElementById('title').value || !document.getElementById('category_id').value)) {
            Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'Please enter a Title and Category.' });
            return;
        }
        if (currentId === 'pricing' && nextId === 'description' && !document.getElementById('basic_price').value) {
            Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'Please set a price for the Basic Package.' });
            return;
        }
        if (currentId === 'description' && nextId === 'availability' && !document.getElementById('input-main').value) {
            Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'Please provide a service description.' });
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
            minDate: 'today',
            conjunction: ', ',
            locale: { firstDayOfWeek: 1 },
        });
        document.getElementById('offer_packages')?.addEventListener('change', function onToggle() {
            const extra = document.getElementById('extraPackages');
            this.checked ? extra.classList.remove('hidden') : extra.classList.add('hidden');
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
