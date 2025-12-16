document.addEventListener('DOMContentLoaded', function () {
    // Subscriptions Chart
    const subscriptionsCanvas = document.getElementById('subscriptionsChart');
    if (subscriptionsCanvas) {
        const chartData = JSON.parse(subscriptionsCanvas.getAttribute('data-chart-data'));
        new Chart(subscriptionsCanvas, {
            type: 'doughnut',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: chartData.label,
                    data: chartData.data,
                    backgroundColor: chartData.colors || ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }

    // Payments Chart
    const paymentsCanvas = document.getElementById('paymentsChart');
    if (paymentsCanvas) {
        const chartData = JSON.parse(paymentsCanvas.getAttribute('data-chart-data'));
        new Chart(paymentsCanvas, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: chartData.label,
                    data: chartData.data,
                    backgroundColor: chartData.colors || ['#10b981', '#f59e0b'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Resources Chart
    const resourcesCanvas = document.getElementById('resourcesChart');
    if (resourcesCanvas) {
        const chartData = JSON.parse(resourcesCanvas.getAttribute('data-chart-data'));
        new Chart(resourcesCanvas, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: chartData.label,
                    data: chartData.data,
                    backgroundColor: chartData.colors || ['#3b82f6', '#8b5cf6', '#ef4444', '#10b981'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
