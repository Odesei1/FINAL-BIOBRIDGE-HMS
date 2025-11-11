document.addEventListener("DOMContentLoaded", function() {
  const ctx = document.getElementById("myAreaChart");

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: areaData.map(item => item.date),
      datasets: [{
        label: "Appointments per Day",
        lineTension: 0.3,
        backgroundColor: "rgba(2,117,216,0.2)",
        borderColor: "rgba(2,117,216,1)",
        data: areaData.map(item => item.total),
      }],
    },
    options: {
      scales: {
        xAxes: [{ gridLines: { display: false }}],
        yAxes: [{
          ticks: {
            beginAtZero: true,
            min: 0,   // ✅ start at 0
            max: 50,  // ✅ area chart max limit
            stepSize: 10,
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
