window.UServeAdmin.register('dashboard', 'adminDashboardConfig', (config) => {
    if (!config) return;
    if (typeof Chart === 'undefined') return;

    const studentData = JSON.parse(config.dataset.studentsPerMonth || '[]');
    const serviceData = JSON.parse(config.dataset.servicesPerMonth || '[]');
    const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    const studentCtx = document.getElementById('studentChart');
    if (studentCtx) {
        new Chart(studentCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Students',
                    data: studentData,
                    borderColor: '#06B6D4',
                    backgroundColor: 'rgba(6, 182, 212, 0.25)',
                    pointBackgroundColor: '#06B6D4',
                    pointBorderColor: '#0f172a',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#94a3b8' },
                        grid: { color: '#1e293b' },
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { display: false },
                    },
                },
            },
        });
    }

    const serviceCtx = document.getElementById('serviceChart');
    if (serviceCtx) {
        new Chart(serviceCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Services Created',
                    data: serviceData,
                    backgroundColor: [
                        '#06B6D4', '#0891B2', '#0E7490', '#155E75',
                        '#1E40AF', '#1E3A8A', '#312E81', '#4C1D95',
                        '#5B21B6', '#6B21A8', '#7C2D12', '#92400E',
                    ],
                    borderRadius: 10,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#94a3b8' },
                        grid: { color: '#1e293b' },
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { display: false },
                    },
                },
            },
        });
    }
});

window.UServeAdmin.boot('dashboard');
