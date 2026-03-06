(() => {
    const config = document.getElementById('servicesEditConfig');
    if (!config) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const parseJson = (value, fallback) => {
        try {
            return JSON.parse(value);
        } catch (e) {
            return fallback;
        }
    };

    const isSessionBased = config.dataset.isSessionBased === 'true';
    const isUnavailable = config.dataset.isUnavailable === 'true';
    const currentDuration = parseInt(config.dataset.currentDuration || '60', 10);
    const scheduleData = parseJson(config.dataset.schedule || '{}', {});
    const bookedSlots = parseJson(config.dataset.bookedSlots || '[]', []);
    let blockedSlots = parseJson(config.dataset.blockedSlots || '[]', []);
    if (!Array.isArray(blockedSlots)) blockedSlots = [];
    const unavailableDates = parseJson(config.dataset.unavailableDates || '[]', []);
    const updateUrl = config.dataset.updateUrl || '';
    const manageUrl = config.dataset.manageUrl || '';

    const toolbarOptions = [['bold', 'italic', 'underline'], [{ list: 'bullet' }]];
    function setupQuill(editorId, inputId, placeholder) {
        if (!document.getElementById(editorId)) return;
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
            isSessionBased,
            isUnavailable,
            currentDuration,
            schedule: scheduleData,
            bookedSlots,
            blockedSlots,
            days: [
                { key: 'mon', name: 'Monday' },
                { key: 'tue', name: 'Tuesday' },
                { key: 'wed', name: 'Wednesday' },
                { key: 'thu', name: 'Thursday' },
                { key: 'fri', name: 'Friday' },
                { key: 'sat', name: 'Saturday' },
                { key: 'sun', name: 'Sunday' },
            ],
            showBulk: false,
            bulkStart: '09:00',
            bulkEnd: '17:00',
            previewDate: null,
            previewSlots: [],
            previewMessage: 'Select a date above to manage slots.',
            init() {
                flatpickr('#preview-date-picker', {
                    minDate: 'today',
                    defaultDate: new Date(),
                    onChange: (_selectedDates, dateStr) => {
                        this.previewDate = dateStr;
                        this.generatePreview();
                    },
                });
                this.previewDate = new Date().toISOString().split('T')[0];
                this.$nextTick(() => {
                    this.generatePreview();
                });
            },
            toggleSlotBlock(slot) {
                if (slot.isBooked) return;
                const slotKey = `${this.previewDate} ${slot.start}`;
                if (this.blockedSlots.includes(slotKey)) {
                    this.blockedSlots = this.blockedSlots.filter((s) => s !== slotKey);
                } else {
                    this.blockedSlots.push(slotKey);
                }
                this.generatePreview();
            },
            generatePreview() {
                if (!this.previewDate) return;
                this.previewSlots = [];
                const d = new Date(this.previewDate);
                const dayName = d.toLocaleDateString('en-US', { weekday: 'short' }).toLowerCase();
                const daySettings = this.schedule[dayName];
                if (!daySettings || !daySettings.enabled) {
                    this.previewMessage = `Service is closed on ${dayName}s.`;
                    return;
                }

                const startMinutes = this.timeToMinutes(daySettings.start);
                const endMinutes = this.timeToMinutes(daySettings.end);
                const duration = parseInt(this.currentDuration, 10);
                const now = new Date();
                const todayStr = now.toISOString().split('T')[0];
                const isToday = this.previewDate === todayStr;
                const currentMinutes = now.getHours() * 60 + now.getMinutes();

                for (let time = startMinutes; time + duration <= endMinutes; time += duration) {
                    if (isToday && time + duration <= currentMinutes) continue;
                    const startTimeStr = this.minutesToTime(time);
                    const endTimeStr = this.minutesToTime(time + duration);
                    const slotKey = `${this.previewDate} ${startTimeStr}`;
                    const isBooked = this.bookedSlots.includes(slotKey);
                    const isBlocked = this.blockedSlots.includes(slotKey);
                    this.previewSlots.push({
                        start: startTimeStr,
                        display: `${this.formatAmPm(startTimeStr)} - ${this.formatAmPm(endTimeStr)}`,
                        isBooked,
                        isBlocked,
                    });
                }
                if (this.previewSlots.length === 0) {
                    this.previewMessage = isToday ? 'No remaining slots for today.' : 'No slots available settings.';
                }
            },
            applyBulkTime() {
                for (const dayKey in this.schedule) {
                    if (!this.schedule[dayKey].enabled) continue;
                    this.schedule[dayKey].start = this.bulkStart;
                    this.schedule[dayKey].end = this.bulkEnd;
                }
                this.showBulk = false;
                this.generatePreview();
            },
            timeToMinutes(time) {
                const [h, m] = time.split(':').map(Number);
                return h * 60 + m;
            },
            minutesToTime(minutes) {
                const h = Math.floor(minutes / 60);
                const m = minutes % 60;
                return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
            },
            formatAmPm(time) {
                let [h, m] = time.split(':');
                h = parseInt(h, 10);
                const ampm = h >= 12 ? 'PM' : 'AM';
                h %= 12;
                h = h || 12;
                return `${h}:${m} ${ampm}`;
            },
        };
    };

    let fpInstance;
    const formatDate = (date) => date.toISOString().split('T')[0];

    function quickBlockDates(amount, unit) {
        if (!fpInstance) return;
        const daysToAdd = unit === 'week' ? amount * 7 : amount * 30;
        const today = new Date();
        const newDates = [];
        for (let i = 0; i < daysToAdd; i += 1) {
            const d = new Date(today);
            d.setDate(today.getDate() + i);
            newDates.push(formatDate(d));
        }
        const current = fpInstance.selectedDates.map((d) => formatDate(d));
        fpInstance.setDate([...new Set([...current, ...newDates])], true);
    }

    function clearUnavailableDates() {
        if (fpInstance) fpInstance.clear();
    }

    function switchTab(targetId) {
        document.querySelectorAll('.tab-section').forEach((el) => el.classList.add('hidden'));
        document.getElementById(targetId)?.classList.remove('hidden');
        updateHeader(targetId);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextStep(currentId, nextId) {
        if (currentId === 'overview' && nextId === 'pricing') {
            if (!document.getElementById('title').value || !document.getElementById('category_id').value) {
                Swal.fire({ icon: 'warning', title: 'Required Fields', text: 'Please provide a Title and Category.' });
                return;
            }
        }
        if (currentId === 'pricing' && nextId === 'description') {
            if (!document.getElementById('basic_price').value) {
                Swal.fire({ icon: 'warning', title: 'Required Fields', text: 'Please set a price for the Basic Package.' });
                return;
            }
        }
        switchTab(nextId);
    }

    function updateHeader(activeId) {
        const map = { overview: 0, pricing: 1, description: 2, availability: 3 };
        const activeIndex = map[activeId];
        document.querySelectorAll('.step-link').forEach((link, index) => {
            link.className = 'step-link w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors';
            const circle = link.querySelector('span');
            if (index < activeIndex) {
                link.classList.add('step-completed', 'border-green-500', 'text-green-600');
                circle.className = 'w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs mr-2 font-bold';
                circle.innerHTML = '✓';
            } else if (index === activeIndex) {
                link.classList.add('step-active', 'border-indigo-500', 'text-indigo-600');
                circle.className = 'w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs mr-2 font-bold ring-1 ring-indigo-600';
                circle.innerHTML = index + 1;
            } else {
                link.classList.add('step-inactive', 'border-transparent', 'text-gray-400');
                circle.className = 'w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mr-2 font-bold';
                circle.innerHTML = index + 1;
            }
        });
    }

    async function submitForm() {
        const form = document.getElementById('editServiceForm');
        if (fpInstance) document.getElementById('unavailableDates').value = fpInstance.input.value;
        const formData = new FormData(form);

        Swal.fire({
            title: 'Saving Changes',
            text: 'Updating...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        try {
            const response = await fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: formData,
            });

            const contentType = response.headers.get('content-type');
            if (!(contentType && contentType.indexOf('application/json') !== -1)) {
                const text = await response.text();
                throw new Error(`Server Error (500). ${text ? 'Check console for details.' : ''}`);
            }

            const data = await response.json();
            if (!response.ok) {
                let errorMsg = data.message || 'Validation failed';
                if (data.errors) errorMsg = Object.values(data.errors)[0][0];
                throw new Error(errorMsg);
            }

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated Successfully!',
                    confirmButtonColor: '#10b981',
                }).then(() => {
                    window.location.href = manageUrl;
                });
                return;
            }

            throw new Error(data.error || 'Unknown error');
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message,
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        setupQuill('editor-basic', 'input-basic', 'e.g. 1 hour online consultation...');
        setupQuill('editor-standard', 'input-standard', 'Describe standard package...');
        setupQuill('editor-premium', 'input-premium', 'Describe premium package...');
        setupQuill('editor-main', 'input-main', 'Provide a comprehensive description of your service...');

        document.querySelectorAll('.template-image').forEach((img) => {
            img.addEventListener('click', function onClick() {
                document.querySelectorAll('.template-image').forEach((item) => item.classList.remove('ring-4', 'ring-indigo-300'));
                this.classList.add('ring-4', 'ring-indigo-300');
                document.getElementById('template_image').value = this.dataset.val;
                document.getElementById('image').value = '';
            });
        });

        document.getElementById('offer_packages')?.addEventListener('change', function onChange() {
            const extra = document.getElementById('extraPackages');
            this.checked ? extra.classList.remove('hidden') : extra.classList.add('hidden');
        });

        fpInstance = flatpickr('#unavailableDates', {
            mode: 'multiple',
            dateFormat: 'Y-m-d',
            minDate: 'today',
            conjunction: ', ',
            defaultDate: unavailableDates,
            locale: { firstDayOfWeek: 1 },
        });
    });

    document.addEventListener('click', (event) => {
        const switchTabTrigger = event.target.closest('[data-switch-tab]');
        if (switchTabTrigger) {
            switchTab(switchTabTrigger.dataset.switchTab);
            return;
        }

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
