<?php
require 'config.php';

$sql = "SELECT * FROM tbl_temperature ORDER BY id DESC LIMIT 30";
$result = $db->query($sql);

if (!$result) {
    echo "Error: " . $sql . "<br>" . $db->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SAM's Air Quality Monitor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .chart {
            width: 100%;
            min-height: 450px;
        }

        .row {
            margin: 0 !important;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Air Quality Monitor</h1>
        </div>
        <div class="clearfix"></div>

        <div class="col-md-6">
            <div id="chart_temperature" class="chart"></div>
        </div>

        <div class="col-md-6">
            <div id="chart_humidity" class="chart"></div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Temperature</th>
                    <th scope="col">Humidity</th>
                    <th scope="col">Gas Level</th>
                    <th scope="col">Air Quality</th>
                    <th scope="col">Date Time</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()) {?>
                    <tr>
                        <th scope="row"><?php echo $i++;?></th>
                        <td><?php echo $row['temperature'];?></td>
                        <td><?php echo $row['humidity'];?></td>
                        <td><?php echo $row['gas_level'];?></td>
                        <td><?php echo $row['air_quality'];?></td>
                        <td><?php echo date("Y-m-d h:i: A", strtotime($row['created_date']));?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {'packages':['gauge']});
    google.charts.setOnLoadCallback(drawTemperatureChart);
    google.charts.setOnLoadCallback(drawHumidityChart);

    function drawTemperatureChart() {
        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Temperature', 0],
        ]);

        var options = {
            width: 1600,
            height: 480,
            redFrom: 70,
            redTo: 100,
            yellowFrom: 40,
            yellowTo: 70,
            greenFrom: 0,
            greenTo: 40,
            minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_temperature'));
        chart.draw(data, options);

        function refreshData() {
            $.ajax({
                url: 'getdata.php',
                dataType: 'json',
                success: function (responseText) {
                    var temperature = parseFloat(responseText.temperature).toFixed(2);
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['Temperature', eval(temperature)],
                    ]);
                    chart.draw(data, options);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown + ': ' + textStatus);
                }
            });
        }

        setInterval(refreshData, 1000);
    }

    function drawHumidityChart() {
        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Humidity', 0],
        ]);

        var options = {
            width: 1600,
            height: 480,
            redFrom: 70,
            redTo: 100,
            yellowFrom: 40,
            yellowTo: 70,
            greenFrom: 0,
            greenTo: 40,
            minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_humidity'));
        chart.draw(data, options);

        function refreshData() {
            $.ajax({
                url: 'getdata.php',
                dataType: 'json',
                success: function (responseText) {
                    var humidity = parseFloat(responseText.humidity).toFixed(2);
                    var data = google.visualization.arrayToDataTable([
                        ['Label', 'Value'],
                        ['Humidity', eval(humidity)],
                    ]);
                    chart.draw(data, options);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown + ': ' + textStatus);
                }
            });
        }

        setInterval(refreshData, 1000);
    }

    $(window).resize(function(){
        drawTemperatureChart();
        drawHumidityChart();
    });
</script>
</body>
</html>
