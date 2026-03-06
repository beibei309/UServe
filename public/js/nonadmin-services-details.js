(() => {
    const config = document.getElementById('servicesDetailsConfig');
    if (!config) return;

    const parseJson = (value, fallback) => {
        try {
            return JSON.parse(value);
        } catch (e) {
            return fallback;
        }
    };

    const detailsConfig = {
        authenticated: config.dataset.authenticated === 'true',
        hasActiveRequest: config.dataset.hasActiveRequest === 'true',
        isSessionBased: config.dataset.isSessionBased === 'true',
        holidays: parseJson(config.dataset.holidays || '[]', []),
        schedule: parseJson(config.dataset.schedule || '{}', {}),
        bookedSlots: parseJson(config.dataset.bookedSlots || '[]', []),
        manualBlocks: parseJson(config.dataset.manualBlocks || '[]', []),
        packages: parseJson(config.dataset.packages || '{}', {}),
        currentPackage: config.dataset.currentPackage || 'basic',
        sessionDuration: parseInt(config.dataset.sessionDuration || '60', 10),
        serviceId: parseInt(config.dataset.serviceId || '0', 10),
        storeRequestUrl: config.dataset.storeRequestUrl || '',
        ordersUrl: config.dataset.ordersUrl || '',
        loginUrl: config.dataset.loginUrl || '',
        favouriteToggleUrl: config.dataset.favouriteToggleUrl || '',
        csrfToken: config.dataset.csrfToken || '',
    };

    if (!Array.isArray(detailsConfig.manualBlocks)) detailsConfig.manualBlocks = [];
    if (!Array.isArray(detailsConfig.holidays)) detailsConfig.holidays = [];

    document.addEventListener('alpine:init', () => {
        Alpine.store('booking', { showCalendarModal: false });
    });

    window.bookingSystem = function bookingSystem() {
        return {
            hasActiveRequest: detailsConfig.hasActiveRequest,
            isSessionBased: detailsConfig.isSessionBased,
            holidays: detailsConfig.holidays,
            schedule: detailsConfig.schedule,
            bookedSlots: detailsConfig.bookedSlots,
            manualBlocks: detailsConfig.manualBlocks,
            packages: detailsConfig.packages,
            currentPackage: detailsConfig.currentPackage,
            selectedDuration: 1,
            selectedDate: null,
            selectedTime: null,
            upcomingDays: [],
            timeSlots: [],
            sessionDuration: detailsConfig.sessionDuration,
            showFullCalendar: false,
            calendarInstance: null,

            get priceColorClass() {
                if (this.currentPackage === 'basic') return 'text-teal-600';
                if (this.currentPackage === 'standard') return 'text-yellow-500';
                if (this.currentPackage === 'premium') return 'text-red-600';
                return 'text-indigo-600';
            },

            formatDuration(minutes) {
                if (minutes < 60) return `${minutes}m`;
                const hours = minutes / 60;
                return `${hours.toFixed(1).replace('.0', '')}h`;
            },

            init() {
                this.generateCalendar();
            },

            calculateTotal() {
                return (this.packages[this.currentPackage].price * this.selectedDuration).toFixed(2);
            },

            selectDuration(hours) {
                this.selectedDuration = hours;
                this.selectedTime = null;
                if (!this.selectedDate) return;
                const dayObj = this.upcomingDays.find((d) => d.dateStr === this.selectedDate);
                if (dayObj) {
                    this.generateTimeSlots(dayObj.dayKey);
                    return;
                }
                const dateObj = new Date(this.selectedDate);
                const jsDays = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                this.generateTimeSlots(jsDays[dateObj.getDay()]);
            },

            generateCalendar() {
                const days = [];
                const today = new Date();
                const jsDays = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

                for (let i = 0; i < 14; i += 1) {
                    const d = new Date(today);
                    d.setDate(today.getDate() + i);
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    const dateStr = `${year}-${month}-${day}`;
                    const dayOfWeekIndex = d.getDay();
                    const dayKey = jsDays[dayOfWeekIndex];

                    let isAvailable = true;
                    if (this.holidays.includes(dateStr)) isAvailable = false;
                    const dayConfig = this.schedule[dayKey];
                    if (!dayConfig || !dayConfig.enabled || dayConfig.enabled === 'false') isAvailable = false;

                    days.push({
                        dateStr,
                        dayName: dayNames[dayOfWeekIndex],
                        dayNumber: day,
                        dayKey,
                        isAvailable,
                    });
                }
                this.upcomingDays = days;
            },

            selectDate(dayObj) {
                if (!dayObj.isAvailable) return;
                this.selectedDate = dayObj.dateStr;
                this.selectedTime = null;
                this.generateTimeSlots(dayObj.dayKey);
            },

            formatTimeOnly(timeStr) {
                if (!timeStr) return '';
                let [h, m] = timeStr.split(':').map(Number);
                const ampm = h >= 12 ? 'PM' : 'AM';
                h %= 12;
                h = h || 12;
                return `${h}:${String(m).padStart(2, '0')} ${ampm}`;
            },

            generateTimeSlots(dayKey) {
                this.timeSlots = [];
                const dayConfig = this.schedule[dayKey];
                if (!dayConfig || !dayConfig.enabled) return;

                const [startH, startM] = dayConfig.start.split(':').map(Number);
                const [endH, endM] = dayConfig.end.split(':').map(Number);
                let currentMinutes = startH * 60 + startM;
                const endMinutes = endH * 60 + endM;
                const stepMinutes = this.sessionDuration;
                const durationMinutes = this.selectedDuration * 60;
                const daysBookings = this.bookedSlots.filter((slot) => slot.date === this.selectedDate);

                const now = new Date();
                const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
                const isToday = this.selectedDate === todayStr;
                const currentRealTimeMinutes = now.getHours() * 60 + now.getMinutes();

                while (currentMinutes + durationMinutes <= endMinutes) {
                    const h = Math.floor(currentMinutes / 60);
                    const m = currentMinutes % 60;
                    const timeStr = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;

                    const proposedStart = currentMinutes;
                    const proposedEnd = currentMinutes + durationMinutes;
                    if (isToday && proposedStart <= currentRealTimeMinutes) {
                        currentMinutes += stepMinutes;
                        continue;
                    }

                    const isBooked = daysBookings.some((booking) => {
                        const [bStartH, bStartM] = booking.start_time.split(':').map(Number);
                        const [bEndH, bEndM] = booking.end_time.split(':').map(Number);
                        const bookingStart = bStartH * 60 + bStartM;
                        const bookingEnd = bEndH * 60 + bEndM;
                        return proposedStart < bookingEnd && proposedEnd > bookingStart;
                    });

                    const blockKey = `${this.selectedDate} ${timeStr}`;
                    let isManuallyBlocked = this.manualBlocks.includes(blockKey);
                    if (!isManuallyBlocked) {
                        isManuallyBlocked = this.manualBlocks.some((blockedKey) => {
                            if (!blockedKey.startsWith(this.selectedDate)) return false;
                            const blockedTime = blockedKey.split(' ')[1];
                            const [blkH, blkM] = blockedTime.split(':').map(Number);
                            const blkMin = blkH * 60 + blkM;
                            const blkEnd = blkMin + this.sessionDuration;
                            return proposedStart < blkEnd && proposedEnd > blkMin;
                        });
                    }

                    this.timeSlots.push({
                        time: timeStr,
                        available: !isBooked && !isManuallyBlocked,
                    });

                    currentMinutes += stepMinutes;
                }
            },

            switchPackage(pkg) {
                this.currentPackage = pkg;
                this.selectedTime = null;
            },

            formatTimeDisplay(timeStr) {
                if (!timeStr) return '';
                const [h, m] = timeStr.split(':').map(Number);
                const startMinutes = h * 60 + m;
                if (!this.isSessionBased) return this.minutesToTime(startMinutes);
                const endMinutes = startMinutes + this.selectedDuration * 60;
                return `${this.minutesToTime(startMinutes)} - ${this.minutesToTime(endMinutes)}`;
            },

            minutesToTime(totalMinutes) {
                let h = Math.floor(totalMinutes / 60);
                const m = totalMinutes % 60;
                const ampm = h >= 12 ? 'PM' : 'AM';
                h %= 12;
                h = h || 12;
                return `${h}:${String(m).padStart(2, '0')} ${ampm}`;
            },

            calculateEndTime(startTime) {
                if (!startTime) return '00:00';
                const [h, m] = startTime.split(':').map(Number);
                let totalMinutes = h * 60 + m + this.selectedDuration * 60;
                let endH = Math.floor(totalMinutes / 60);
                const endM = totalMinutes % 60;
                endH %= 24;
                return `${String(endH).padStart(2, '0')}:${String(endM).padStart(2, '0')}`;
            },

            openCalendar() {
                this.showFullCalendar = true;
                this.$nextTick(() => {
                    if (this.calendarInstance) return;
                    this.calendarInstance = flatpickr('#full-calendar-container', {
                        inline: true,
                        minDate: 'today',
                        disable: [
                            (date) => {
                                const offset = date.getTimezoneOffset();
                                const adjustedDate = new Date(date.getTime() - offset * 60 * 1000);
                                const dateStr = adjustedDate.toISOString().split('T')[0];
                                if (this.holidays.includes(dateStr)) return true;
                                const jsDays = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                                const dayKey = jsDays[date.getDay()];
                                const dayConfig = this.schedule[dayKey];
                                return !dayConfig || (dayConfig.enabled !== true && dayConfig.enabled !== 'true');
                            },
                        ],
                        onChange: (_selectedDates, dateStr) => {
                            this.selectedDate = dateStr;
                            this.selectedTime = null;
                            const d = new Date(dateStr);
                            const jsDays = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                            const dayKey = jsDays[d.getDay()];
                            this.generateTimeSlots(dayKey);
                            this.showFullCalendar = false;
                        },
                    });
                });
            },

            submitBooking() {
                if (!detailsConfig.authenticated) {
                    window.location.href = detailsConfig.loginUrl;
                    return;
                }

                if (this.hasActiveRequest) {
                    Swal.fire('Request Exists', 'You already have an active request for this service.', 'warning');
                    return;
                }

                const sendStartTime = this.isSessionBased ? this.selectedTime : '00:00';
                const sendEndTime = this.isSessionBased ? this.calculateEndTime(this.selectedTime) : '23:59';
                const displayTime = this.formatTimeDisplay(this.selectedTime);

                const detailsHtml = `
                    <div class="text-left bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm mb-4">
                        <p class="mb-1"><strong>Date:</strong> ${this.selectedDate}</p>
                        <p class="mb-1"><strong>Time:</strong> ${displayTime}</p>
                        <p class="mb-1"><strong>Duration:</strong> ${this.selectedDuration} Hours</p>
                        <p class="text-lg font-bold text-indigo-600 mt-2">Total: RM${this.calculateTotal()}</p>
                    </div>
                    <div class="text-left">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Message to Seller</label>
                        <textarea id="swal-message-input" class="w-full border rounded-lg p-3 text-sm" rows="3" placeholder="Describe your task..."></textarea>
                    </div>
                `;

                Swal.fire({
                    title: 'Confirm Booking?',
                    html: detailsHtml,
                    showCancelButton: true,
                    confirmButtonText: 'Confirm Request',
                    preConfirm: () => {
                        const msg = document.getElementById('swal-message-input').value;
                        if (!msg) Swal.showValidationMessage('Please write a message');
                        return msg;
                    },
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we place your order.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    const userNote = result.value;
                    const finalMessage = `BOOKING DETAILS:\nTime: ${displayTime}\nDuration: ${this.selectedDuration}h\n\nNote: ${userNote}`;

                    fetch(detailsConfig.storeRequestUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': detailsConfig.csrfToken,
                        },
                        body: JSON.stringify({
                            student_service_id: detailsConfig.serviceId,
                            selected_dates: this.selectedDate,
                            start_time: sendStartTime,
                            end_time: sendEndTime,
                            message: finalMessage,
                            selected_package: this.currentPackage,
                            offered_price: this.calculateTotal(),
                        }),
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (!data.success) {
                                Swal.fire('Error', data.message, 'error');
                                return;
                            }
                            Swal.fire({
                                title: 'Success!',
                                text: 'Your request has been sent successfully.',
                                icon: 'success',
                                confirmButtonText: 'Go to Orders',
                            }).then(() => {
                                window.location.href = detailsConfig.ordersUrl;
                            });
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                        });
                });
            },
        };
    };

    function handleShare(btn) {
        const modal = document.getElementById('shareModal');
        document.getElementById('shareLinkInput').value = btn.dataset.url;
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    }

    function closeShareModal() {
        const modal = document.getElementById('shareModal');
        modal.querySelector('div').classList.remove('scale-100');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 150);
    }

    function copyShareLink() {
        const input = document.getElementById('shareLinkInput');
        input.select();
        document.execCommand('copy');
        const msg = document.getElementById('copyMessage');
        msg.classList.remove('opacity-0');
        setTimeout(() => msg.classList.add('opacity-0'), 2000);
    }

    function handleFavourite(serviceId, loggedIn) {
        if (!loggedIn) {
            window.location.href = detailsConfig.loginUrl;
            return;
        }

        const icon = document.getElementById(`heart-${serviceId}`);
        const text = document.getElementById(`text-${serviceId}`);
        fetch(detailsConfig.favouriteToggleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': detailsConfig.csrfToken,
            },
            body: JSON.stringify({ service_id: serviceId }),
        })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then((data) => {
                if (!data.success) return;
                if (data.favorited) {
                    icon.className = 'fas fa-heart';
                    icon.parentElement.classList.remove('text-gray-500');
                    icon.parentElement.classList.add('text-red-500');
                    text.innerText = 'Saved';
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Service added to your favourites',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                    });
                    return;
                }
                icon.className = 'far fa-heart';
                icon.parentElement.classList.remove('text-red-500');
                icon.parentElement.classList.add('text-gray-500');
                text.innerText = 'Save';
                Swal.fire({
                    icon: 'info',
                    title: 'Removed',
                    text: 'Service removed from favourites',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });
            })
            .catch((err) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Unable to update favourite',
                });
            });
    }

    document.querySelectorAll('img[data-fallback-src]').forEach((img) => {
        img.addEventListener('error', () => {
            if (img.dataset.fallbackApplied === '1') return;
            img.dataset.fallbackApplied = '1';
            img.src = img.dataset.fallbackSrc;
        });
    });

    document.addEventListener('click', (event) => {
        const shareTrigger = event.target.closest('[data-share-trigger]');
        if (shareTrigger) {
            handleShare(shareTrigger);
            return;
        }

        if (event.target.closest('[data-close-share]')) {
            closeShareModal();
            return;
        }

        if (event.target.closest('[data-copy-share]')) {
            copyShareLink();
            return;
        }

        const favouriteTrigger = event.target.closest('[data-favourite-service]');
        if (favouriteTrigger) {
            handleFavourite(
                favouriteTrigger.dataset.favouriteService,
                favouriteTrigger.dataset.loggedIn === 'true',
            );
            return;
        }

        const requiresLoginLink = event.target.closest('[data-requires-login-confirm]');
        if (requiresLoginLink) {
            const proceed = window.confirm('Please sign in to view the full profile details.');
            if (!proceed) {
                event.preventDefault();
            }
        }
    });
})();
