(() => {
    const config = document.getElementById('studentsIndexConfig');
    if (!config) return;

    const parseJson = (value, fallback) => {
        try {
            return JSON.parse(value);
        } catch (e) {
            return fallback;
        }
    };

    const chartLabels = parseJson(config.dataset.chartLabels || '[]', []);
    const chartSales = parseJson(config.dataset.chartSales || '[]', []);
    const chartCancelled = parseJson(config.dataset.chartCancelled || '[]', []);
    const chartCompleted = parseJson(config.dataset.chartCompleted || '[]', []);
    const chartNewOrders = parseJson(config.dataset.chartNewOrders || '[]', []);
    const isAvailableInitial = config.dataset.isAvailable === 'true';
    const startDateInitial = config.dataset.startDate || '';
    const endDateInitial = config.dataset.endDate || '';
    const availabilityUpdateUrl = config.dataset.availabilityUpdateUrl || '';

    function renderOverviewChart() {
        const canvas = document.getElementById('overviewChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const ctx = canvas.getContext('2d');
        const gradientSales = ctx.createLinearGradient(0, 0, 0, 300);
        gradientSales.addColorStop(0, 'rgba(90, 219, 232, 0.2)');
        gradientSales.addColorStop(1, 'rgba(90, 219, 232, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Sales ($)',
                        data: chartSales,
                        borderWidth: 3,
                        borderColor: '#0EA5E9',
                        backgroundColor: gradientSales,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0EA5E9',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'New Orders',
                        data: chartNewOrders,
                        borderWidth: 2,
                        borderColor: '#10B981',
                        pointBackgroundColor: '#10B981',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.4,
                    },
                    {
                        label: 'Completed',
                        data: chartCompleted,
                        borderWidth: 2,
                        borderColor: '#6366F1',
                        pointBackgroundColor: '#6366F1',
                        fill: false,
                        tension: 0.4,
                        hidden: true,
                    },
                    {
                        label: 'Cancelled',
                        data: chartCancelled,
                        borderWidth: 2,
                        borderColor: '#EF4444',
                        pointBackgroundColor: '#EF4444',
                        fill: false,
                        tension: 0.4,
                        hidden: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 12,
                            },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 13,
                        },
                        bodyFont: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 12,
                        },
                        cornerRadius: 8,
                        displayColors: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            borderDash: [4, 4],
                        },
                        ticks: {
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                            },
                            color: '#64748b',
                        },
                        border: {
                            display: false,
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                            },
                            color: '#64748b',
                        },
                        border: {
                            display: false,
                        },
                    },
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            },
        });
    }

    window.studentsIndexAvailability = function studentsIndexAvailability() {
        return {
            isAvailable: isAvailableInitial,
            isSaving: false,
            showModal: false,
            startDate: startDateInitial,
            endDate: endDateInitial,

            openModal() {
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            formatDate(dateString) {
                if (!dateString) return '';
                return new Date(dateString).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                });
            },

            quickToggle() {
                if (this.isSaving) return;
                if (this.isAvailable) {
                    this.isAvailable = false;
                    this.openModal();
                    return;
                }
                this.isAvailable = true;
                this.startDate = '';
                this.endDate = '';
                this.doSave(true, null, null);
            },

            addDuration(daysToAdd, monthsToAdd) {
                if (!this.startDate) {
                    this.startDate = new Date().toISOString().split('T')[0];
                }

                const baseString = this.endDate || this.startDate;
                const dateObj = new Date(`${baseString}T12:00:00`);
                if (daysToAdd > 0) dateObj.setDate(dateObj.getDate() + daysToAdd);
                if (monthsToAdd > 0) dateObj.setMonth(dateObj.getMonth() + monthsToAdd);
                this.endDate = dateObj.toISOString().split('T')[0];
            },

            deleteDates() {
                this.startDate = '';
                this.endDate = '';
                this.isAvailable = true;
            },

            saveChanges() {
                let finalStartDate = this.startDate;
                let finalEndDate = this.endDate;

                if (!this.isAvailable) {
                    if (!finalStartDate || !finalEndDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Date Required',
                            text: 'Please select both start and end dates.',
                            confirmButtonColor: '#3085d6',
                        });
                        return;
                    }
                    if (new Date(finalStartDate) > new Date(finalEndDate)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Dates',
                            text: 'Start date cannot be after end date.',
                            confirmButtonColor: '#d33',
                        });
                        return;
                    }
                } else {
                    finalStartDate = null;
                    finalEndDate = null;
                }

                this.doSave(this.isAvailable, finalStartDate, finalEndDate);
            },

            doSave(isAvailable, startDate, endDate) {
                this.isSaving = true;
                fetch(availabilityUpdateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        is_available: isAvailable,
                        start_date: startDate,
                        end_date: endDate,
                    }),
                })
                    .then((res) => {
                        if (!res.ok) {
                            return res.json().then((e) => {
                                throw new Error(e.message || 'Server error');
                            });
                        }
                        return res.json();
                    })
                    .then((data) => {
                        this.isAvailable = data.is_available;
                        this.startDate = data.start_date || '';
                        this.endDate = data.end_date || '';
                        this.isSaving = false;
                        this.closeModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    })
                    .catch((err) => {
                        this.isSaving = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: err.message,
                            confirmButtonColor: '#d33',
                        });
                    });
            },
        };
    };

    document.querySelectorAll('select[data-auto-submit]').forEach((select) => {
        select.addEventListener('change', () => {
            select.form?.submit();
        });
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderOverviewChart);
    } else {
        renderOverviewChart();
    }
})();
