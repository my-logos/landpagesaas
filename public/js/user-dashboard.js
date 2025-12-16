document.addEventListener('DOMContentLoaded', function() {
    // Orders by Product Chart
    const ordersByProductCtx = document.getElementById('ordersByProductChart');
    if (ordersByProductCtx && typeof Chart !== 'undefined') {
        const chartData = JSON.parse(ordersByProductCtx.dataset.chartData || '{}');
        new Chart(ordersByProductCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: chartData.label || 'Orders',
                    data: chartData.data || [],
                    backgroundColor: 'rgba(43, 96, 255, 0.6)',
                    borderColor: 'rgba(43, 96, 255, 1)',
                    borderWidth: 1
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

    // Orders by Status Chart
    const ordersByStatusCtx = document.getElementById('ordersByStatusChart');
    if (ordersByStatusCtx && typeof Chart !== 'undefined') {
        const chartData = JSON.parse(ordersByStatusCtx.dataset.chartData || '{}');
        new Chart(ordersByStatusCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    data: chartData.data || [],
                    backgroundColor: [
                        '#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6', '#6366f1', '#ec4899'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Orders by Page Chart
    const ordersByPageCtx = document.getElementById('ordersByPageChart');
    if (ordersByPageCtx && typeof Chart !== 'undefined') {
        const chartData = JSON.parse(ordersByPageCtx.dataset.chartData || '{}');
        new Chart(ordersByPageCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: chartData.label || 'Orders',
                    data: chartData.data || [],
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
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

    // AI Analytics Performance Trends Chart
    const performanceTrendsCtx = document.getElementById('performanceTrendsChart');
    if (performanceTrendsCtx && typeof Chart !== 'undefined') {
        const chartData = JSON.parse(performanceTrendsCtx.dataset.chartData || '{}');
        
        new Chart(performanceTrendsCtx, {
            type: 'line',
            data: {
                labels: chartData.dates || [],
                datasets: [
                    {
                        label: performanceTrendsCtx.dataset.averageTimeLabel || 'Average Time (seconds)',
                        data: chartData.averageTimeData || [],
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        yAxisID: 'y1',
                        tension: 0.4
                    },
                    {
                        label: performanceTrendsCtx.dataset.successRateLabel || 'Success Rate (%)',
                        data: chartData.successRateData || [],
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        yAxisID: 'y',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: performanceTrendsCtx.dataset.successRateLabel || 'Success Rate (%)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: performanceTrendsCtx.dataset.averageTimeLabel || 'Average Time (seconds)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    }
});
