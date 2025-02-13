<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Get language from session that was set in dashboard
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations array
$translations = [
    'en' => [
        'weather_forecast' => 'Weather Forecast',
        'search_location' => 'Search location...',
        'search' => 'Search',
        'weather_for' => 'Weather Forecast for',
        'current_conditions' => 'Current Weather Conditions:',
        'temperature' => 'Temperature',
        'humidity' => 'Humidity',
        'conditions' => 'Conditions',
        'wind_speed' => 'Wind Speed',
        'date' => 'Date',
        'rainfall' => 'Rainfall',
        'description' => 'Description',
        'temp_rainfall_forecast' => 'Temperature and Rainfall Forecast',
        'dashboard' => 'Dashboard'
    ],
    'hi' => [
        'weather_forecast' => 'मौसम का पूर्वानुमान',
        'search_location' => 'स्थान खोजें...',
        'search' => 'खोज',
        'weather_for' => 'के लिए मौसम का पूर्वानुमान',
        'current_conditions' => 'वर्तमान मौसम की स्थिति:',
        'temperature' => 'तापमान',
        'humidity' => 'आर्द्रता',
        'conditions' => 'स्थिति',
        'wind_speed' => 'हवा की गति',
        'date' => 'तारीख',
        'rainfall' => 'वर्षा',
        'description' => 'विवरण',
        'temp_rainfall_forecast' => 'तापमान और वर्षा का पूर्वानुमान',
        'dashboard' => 'डैशबोर्ड'
    ],
    'gu' => [
        'weather_forecast' => 'હવામાન આગાહી',
        'search_location' => 'સ્થાન શોધો...',
        'search' => 'શોધ',
        'weather_for' => 'માટે હવામાન આગાહી',
        'current_conditions' => 'વર્તમાન હવામાન સ્થિતિ:',
        'temperature' => 'તાપમાન',
        'humidity' => 'ભેજ',
        'conditions' => 'સ્થિતિ',
        'wind_speed' => 'પવનની ગતિ',
        'date' => 'તારીખ',
        'rainfall' => 'વરસાદ',
        'description' => 'વર્ણન',
        'temp_rainfall_forecast' => 'તાપમાન અને વરસાદની આગાહી',
        'dashboard' => 'ડેશબોર્ડ'
    ]
];

// OpenWeatherMap API configuration
$apiKey = "0b9c5f5d4485861396ea55abc7a91f3e"; // Replace with your actual OpenWeatherMap API key
$region = isset($_GET['search']) ? $_GET['search'] : ($_SESSION['region'] ?? 'Delhi');
$apiUrl = "http://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($region) . "&appid=" . $apiKey . "&units=metric";

// Fetch weather data from API
$response = file_get_contents($apiUrl);
$weatherData = json_decode($response, true);

// Process API data
$processedData = [];
if ($weatherData && isset($weatherData['list'])) {
    foreach ($weatherData['list'] as $forecast) {
        $date = date('Y-m-d', strtotime($forecast['dt_txt']));
        if (!isset($processedData[$date])) {
            $processedData[$date] = [
                'date' => $date,
                'temperature' => $forecast['main']['temp'],
                'humidity' => $forecast['main']['humidity'],
                'description' => ucfirst($forecast['weather'][0]['description']),
                'wind_speed' => $forecast['wind']['speed'],
                'rainfall' => isset($forecast['rain']['3h']) ? $forecast['rain']['3h'] : 0
            ];
        }
    }
}

// Store API data in database
foreach ($processedData as $data) {
    $date = $data['date'];
    $temp = $data['temperature'];
    $rainfall = $data['rainfall'];
    
    $sql = "INSERT INTO WeatherData (region, forecast_date, temperature, rainfall) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE temperature = ?, rainfall = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdddd", $region, $date, $temp, $rainfall, $temp, $rainfall);
    $stmt->execute();
}

// Fetch stored data
$sql = "SELECT * FROM WeatherData WHERE region = ? ORDER BY forecast_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $region);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['weather_forecast']; ?> - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #2c3e50;
        }
        .container {
            max-width: 1200px;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }
        h1 {
            color: #1a237e;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        h3 {
            color: #303f9f;
            font-weight: 600;
        }
        .alert-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: none;
            border-radius: 15px;
            color: #1565c0;
        }
        .btn-primary {
            background: #303f9f;
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #1a237e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(48, 63, 159, 0.3);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e3f2fd;
            padding: 10px 15px;
        }
        .form-control:focus {
            border-color: #303f9f;
            box-shadow: 0 0 0 0.2rem rgba(48, 63, 159, 0.25);
        }
        .table {
            border-radius: 15px;
            overflow: hidden;
        }
        .table thead th {
            background: #303f9f;
            color: white;
            font-weight: 500;
            border: none;
        }
        .table tbody tr:hover {
            background-color: #e3f2fd;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .back-button {
            background: transparent;
            border: 2px solid #303f9f;
            color: #303f9f;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .back-button:hover {
            background: #303f9f;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(48, 63, 159, 0.15);
        }

        @media (max-width: 768px) {
            .back-button {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Back to Dashboard Button -->
    <div class="container mt-3">
        <a href="dashboard.php" class="btn btn-outline-primary back-button">
            <i class="fas fa-arrow-left me-2"></i>
            <?php echo $translations[$lang]['dashboard']; ?>
        </a>
    </div>

    <div class="container py-5">
        <h1 class="text-center mb-4"><?php echo $translations[$lang]['weather_forecast']; ?></h1>
        
        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <form class="d-flex" method="GET">
                    <input type="text" name="search" class="form-control me-2" placeholder="<?php echo $translations[$lang]['search_location']; ?>" value="<?php echo htmlspecialchars($region); ?>">
                    <button type="submit" class="btn btn-primary"><?php echo $translations[$lang]['search']; ?></button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 glassmorphism p-4 fade-in">
                <h3><?php echo $translations[$lang]['weather_for']; ?> <?php echo htmlspecialchars($region); ?></h3>
                
                <!-- Current Weather Summary -->
                <?php if (!empty($processedData)): 
                    $current = reset($processedData); ?>
                <div class="alert alert-info mb-4">
                    <h4 class="mb-3"><?php echo $translations[$lang]['current_conditions']; ?></h4>
                    <p class="mb-2"><strong><?php echo $translations[$lang]['temperature']; ?>:</strong> <?php echo round($current['temperature']); ?>°C</p>
                    <p class="mb-2"><strong><?php echo $translations[$lang]['humidity']; ?>:</strong> <?php echo $current['humidity']; ?>%</p>
                    <p class="mb-2"><strong><?php echo $translations[$lang]['conditions']; ?>:</strong> <?php echo $current['description']; ?></p>
                    <p class="mb-0"><strong><?php echo $translations[$lang]['wind_speed']; ?>:</strong> <?php echo $current['wind_speed']; ?> m/s</p>
                </div>
                <?php endif; ?>

                <!-- Weather Chart -->
                <canvas id="weatherChart" class="mb-4"></canvas>

                <!-- Detailed Forecast Table -->
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo $translations[$lang]['date']; ?></th>
                            <th><?php echo $translations[$lang]['temperature']; ?> (°C)</th>
                            <th><?php echo $translations[$lang]['rainfall']; ?> (mm)</th>
                            <th><?php echo $translations[$lang]['description']; ?></th>
                            <th><?php echo $translations[$lang]['wind_speed']; ?> (m/s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($processedData as $data): ?>
                        <tr>
                            <td><?php echo $data['date']; ?></td>
                            <td><?php echo round($data['temperature']); ?></td>
                            <td><?php echo round($data['rainfall'], 2); ?></td>
                            <td><?php echo $data['description']; ?></td>
                            <td><?php echo $data['wind_speed']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Create weather chart
        const ctx = document.getElementById('weatherChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($processedData, 'date')); ?>,
                datasets: [{
                    label: '<?php echo $translations[$lang]['temperature']; ?> (°C)',
                    data: <?php echo json_encode(array_column($processedData, 'temperature')); ?>,
                    borderColor: '#303f9f',
                    backgroundColor: 'rgba(48, 63, 159, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: '<?php echo $translations[$lang]['rainfall']; ?> (mm)',
                    data: <?php echo json_encode(array_column($processedData, 'rainfall')); ?>,
                    borderColor: '#1565c0',
                    backgroundColor: 'rgba(21, 101, 192, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Poppins',
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: '<?php echo $translations[$lang]['temp_rainfall_forecast']; ?>',
                        font: {
                            family: 'Poppins',
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                family: 'Poppins'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                family: 'Poppins'
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
</body>
</html>
