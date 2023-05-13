$(function () {
  let data,
    backgroundColor,
    datax,
    type,
    ctx = document.getElementById("chart").getContext("2d");

  $.getJSON("data.php?charts", function (todos) {
    labels = [];
    data = [];
    backgroundColor = [];
    todos.forEach((item) => {
      labels.push(item.title);
      data.push(item.steps_number);
    });
    for (let i = 0; i < todos.length; ++i) {
      backgroundColor.push(getOtherColor());
    }
    type = "bar";
    datax = display(type, labels, backgroundColor, data);
    new Chart(ctx, datax);
  });

  function getOtherColor() {
    let color = "#";
    let chain = "0123456789abcdef";
    for (let indice, i = 0; i < 6; ++i) {
      indice = Math.floor(16 * Math.random(chain));
      color = color + chain[indice];
    }
    return color;
  }

  function display(type, labels, backgroundColor, data) {
    return {
      type,
      data: {
        labels,
        datasets: [
          {
            backgroundColor,
            data,
          },
        ],
      },
      options: {
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
              },
            },
          ],
        },
        legend: {
          display: false,
          labels: {
            fontColor: "rgb(132, 0, 132)",
          },
        },
      },
    };
  }
});
