<?php include('parts/header.php'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Bar Chart</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('parts/footer.php'); ?>

<script src="assets/vendors/chart/Chart.bundle.min.js"></script>
<script>
    $(function() {
        let labels,
            data,
            backgroundColor,
            datax,
            title,
            type,
            ctx = document.getElementById("chart").getContext("2d");

        $.getJSON("data.php?charts", function(todos) {
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
            title = "Nombre d'étapes par todo".toLocaleUpperCase();
            datax = display(type, labels, backgroundColor, data, title);
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

        function display(type, labels, backgroundColor, data, title) {
            return {
                type,
                data: {
                    labels,
                    datasets: [{
                        backgroundColor,
                        data,
                    }, ],
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            },
                        }, ],
                    },
                    legend: {
                        display: false,
                        labels: {
                            fontColor: "rgb(132, 0, 132)",
                        },
                    },
                    title: {
                        display: true,
                        text: title,
                    },
                },
            };
        }
    });
</script>
</body>

</html>