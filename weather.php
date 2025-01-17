<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$sql = "SELECT * FROM WeatherData WHERE region = '{$_SESSION['region']}'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Weather Forecast</h1>
        <div class="row">
            <div class="col-md-12 glassmorphism p-4 fade-in">
                <h3>Weather Data for <?php echo $_SESSION['region']; ?></h3>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Temperature (°C)</th>
                            <th>Rainfall (mm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['forecast_date']; ?></td>
                            <td><?php echo $row['temperature']; ?></td>
                            <td><?php echo $row['rainfall']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>