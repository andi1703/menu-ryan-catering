const ctx = document.getElementById('materialChart').getContext('2d');

const materialChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [], // Labels for the chart will be populated dynamically
        datasets: [{
            label: 'Material Usage',
            data: [], // Data for the chart will be populated dynamically
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Function to update the chart with new data
function updateChart(labels, data) {
    materialChart.data.labels = labels;
    materialChart.data.datasets[0].data = data;
    materialChart.update();
}

// Example of how to call updateChart with fetched data
// updateChart(['Material A', 'Material B'], [10, 20]);