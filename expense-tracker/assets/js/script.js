// assets/js/script.js

async function fetchChartData() {
    try {
        const response = await fetch('api_charts.php');
        const data = await response.json();

        // Bar Chart (Monthly)
        const ctxBar = document.getElementById('expenseBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: data.bar.labels,
                datasets: [{
                    label: 'Monthly Expenses',
                    data: data.bar.data,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });

        // Pie Chart (Categories)
        const ctxPie = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: data.pie.map(item => item.name),
                datasets: [{
                    data: data.pie.map(item => item.total),
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                }]
            }
        });

    } catch (error) {
        console.error('Error loading charts:', error);
    }
}