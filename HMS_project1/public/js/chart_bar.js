document.addEventListener("DOMContentLoaded", function() {
  const ctx = document.getElementById("myBarChart");

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: barData.map(item => item.month),
      datasets: [{
        label: "Appointments per Month",
        backgroundColor: "rgba(2,117,216,1)",
        borderColor: "rgba(2,117,216,1)",
        data: barData.map(item => item.total),
      }],
    },
    options: {
      scales: {
        xAxes: [{ gridLines: { display: false }}],
        yAxes: [{
          ticks: {
            beginAtZero: true,
            min: 0,     // ✅ start at 0
            max: 50,   // ✅ bar chart max limit
            stepSize: 10, //✅ increments of 3
            callback: function(value) {
              return Number.isInteger(value) ? value : null;
            }
          }
        }]
      },
      legend: { display: false }
    }
  });
});
