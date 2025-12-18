// AI Analytics Chart Initialization
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('performanceTrendsChart');

    if (!canvas) {
        return;
    }

    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        return;
    }

    // Get chart data from data attributes using dataset API
    const chartDataStr = canvas.dataset.chartData || canvas.getAttribute('data-chart-data');
    const averageTimeLabel = canvas.dataset.averageTimeLabel || canvas.getAttribute('data-average-time-label') || 'Average Time (seconds)';
    const successRateLabel = canvas.dataset.successRateLabel || canvas.getAttribute('data-success-rate-label') || 'Success Rate (%)';

    if (!chartDataStr) {
        return;
    }

    let chartData;
    try {
        chartData = JSON.parse(chartDataStr);
    } catch (e) {
        return;
    }

    const dates = chartData.dates || [];
    const averageTimeData = chartData.averageTimeData || [];
    const successRateData = chartData.successRateData || [];

    // Create the chart
    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: averageTimeLabel,
                    data: averageTimeData,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.4
                },
                {
                    label: successRateLabel,
                    data: successRateData,
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
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                x: {
                    display: true
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: successRateLabel
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: averageTimeLabel
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
});
