<?php include('parts/header.php'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Graphe</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-3" id="chart" style="height: 500px;"></div>
            </div>
        </div>
    </div>

</div>
<?php include('parts/footer.php'); ?>
<script src="assets/vendors/flot/flot.bundle.min.js"></script>
<script>
    function labelFormatter(label, series) {
        return '<div style="background-color: #333; color: white; border-radius: 5px; padding: 5px; text-align: center;">' +
            label + "<br><strong>" + series.data[0][1] + "</strong></div>";
    }

    $(function() {
        $.getJSON("data.php?charts", function(todos) {
            console.log(todos)
            var dataz = [];
            for (let i = 0; i < todos.length; i++) {
                dataz.push({
                    label: todos[i].title,
                    data: todos[i].steps_number,
                    color: getOtherColor()
                });
            }

            $.plot('#chart', dataz, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 3 / 4,
                            formatter: labelFormatter,
                            background: {
                                opacity: 0.5
                            }
                        }
                    }
                },
                legend: {
                    show: true
                }
            });
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
    });
</script>
</body>

</html>