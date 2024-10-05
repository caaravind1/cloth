<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cloth";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch sales data
$sales_data = [];
$sql = "SELECT p.name, SUM(s.quantity) AS total_quantity, SUM(s.total_price) AS total_sales 
        FROM sales s 
        JOIN products p ON s.product_id = p.id 
        GROUP BY p.name";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sales_data[] = [$row['name'], (int)$row['total_quantity'], (float)$row['total_sales']];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
    </style>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Product', 'Total Quantity', 'Total Sales'],
                <?php
                foreach ($sales_data as $data) {
                    echo "['".$data[0]."', ".$data[1].", ".$data[2]."],";
                }
                ?>
            ]);

            var options = {
                title: 'Sales Report',
                hAxis: {title: 'Product', titleTextStyle: {color: '#333'}},
                vAxis: {minValue: 0},
                chartArea: {width: '70%', height: '70%'}
            };

            var chart = new google.visualization.AreaChart(document.getElementById('sales_chart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Sales Reports</h2>
        <div id="sales_chart" style="width: 100%; height: 500px;"></div>
        <button onclick="window.location.href='admin_home.php';" class="btn btn-secondary mt-3">Back to Admin Home</button>
    </div>
</body>
</html>
