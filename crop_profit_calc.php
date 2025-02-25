<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get language from session that was set in dashboard
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations array
$translations = [
    'en' => [
        'results_title' => 'Crop Profit Calculator Results',
        'crop' => 'Crop',
        'total_yield' => 'Total Yield',
        'total_revenue' => 'Total Revenue', 
        'total_costs' => 'Total Costs',
        'net_profit' => 'Net Profit',
        'kg' => 'kg',
        'back' => 'Back to Dashboard',
        'profit_analysis' => 'Profit Analysis',
        'cost_breakdown' => 'Cost Breakdown',
        'revenue_streams' => 'Revenue Streams',
        'yield_metrics' => 'Yield Metrics',
        'land_size' => 'Land Size (Acres)',
        'market_price' => 'Market Price (₹/kg)',
        'cost_per_acre' => 'Cost per Acre (₹)',
        'yield_per_acre' => 'Yield per Acre (kg)',
        'calculate_profit' => 'Calculate Profit'
    ],
    'hi' => [
        'results_title' => 'फसल लाभ कैलकुलेटर परिणाम',
        'crop' => 'फसल',
        'total_yield' => 'कुल उपज',
        'total_revenue' => 'कुल राजस्व',
        'total_costs' => 'कुल लागत', 
        'net_profit' => 'शुद्ध लाभ',
        'kg' => 'किग्रा',
        'back' => 'डैशबोर्ड पर वापस जाएं',
        'profit_analysis' => 'लाभ विश्लेषण',
        'cost_breakdown' => 'लागत विभाजन',
        'revenue_streams' => 'राजस्व धाराएं',
        'yield_metrics' => 'उपज मेट्रिक्स',
        'land_size' => 'भूमि का आकार (एकड़)',
        'market_price' => 'बाजार मूल्य (₹/किग्रा)',
        'cost_per_acre' => 'प्रति एकड़ लागत (₹)',
        'yield_per_acre' => 'प्रति एकड़ उपज (किग्रा)',
        'calculate_profit' => 'लाभ की गणना करें'
    ],
    'gu' => [
        'results_title' => 'પાક નફો કેલ્ક્યુલેટર પરિણામો',
        'crop' => 'પાક',
        'total_yield' => 'કુલ ઉપજ',
        'total_revenue' => 'કુલ આવક',
        'total_costs' => 'કુલ ખર્ચ',
        'net_profit' => 'ચોખ્ખો નફો',
        'kg' => 'કિગ્રા',
        'back' => 'ડેશબોર્ડ પર પાછા જાઓ',
        'profit_analysis' => 'નફા વિશ્લેષણ',
        'cost_breakdown' => 'ખર્ચ વિભાજન',
        'revenue_streams' => 'આવક સ્ત્રોતો',
        'yield_metrics' => 'ઉપજ માપદંડ',
        'land_size' => 'જમીન કદ (એકર)',
        'market_price' => 'બજાર ભાવ (₹/કિગ્રા)',
        'cost_per_acre' => 'એકર દીઠ ખર્ચ (₹)',
        'yield_per_acre' => 'એકર દીઠ ઉપજ (કિગ્રા)',
        'calculate_profit' => 'નફો ગણો'
    ]
];

function calculateCropProfit($cropName, $landSize, $marketPrice, $costPerAcre, $yieldPerAcre) {
    $totalYield = $landSize * $yieldPerAcre;
    $totalRevenue = $totalYield * $marketPrice;
    $totalCosts = $landSize * $costPerAcre;
    $profit = $totalRevenue - $totalCosts;
    
    return array(
        'crop_name' => $cropName,
        'total_yield' => $totalYield,
        'total_revenue' => $totalRevenue,
        'total_costs' => $totalCosts,
        'profit' => $profit
    );
}

function formatINR($number) {
    return '₹' . number_format($number, 2);
}

$results = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required_fields = ['cropName', 'landSize', 'marketPrice', 'costPerAcre', 'yieldPerAcre'];
    $missing_fields = false;
    
    foreach($required_fields as $field) {
        if(!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields = true;
            break;
        }
    }
    
    if(!$missing_fields) {
        $cropName = $_POST['cropName'];
        $landSize = floatval($_POST['landSize']);
        $marketPrice = floatval($_POST['marketPrice']);
        $costPerAcre = floatval($_POST['costPerAcre']);
        $yieldPerAcre = floatval($_POST['yieldPerAcre']);

        $results = calculateCropProfit($cropName, $landSize, $marketPrice, $costPerAcre, $yieldPerAcre);
    } else {
        $error = "Please fill all required fields";
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Profit Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #27ae60;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .calculator-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .results-card {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .chart-container {
            margin-top: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        @media (max-width: 768px) {
            .calculator-container {
                margin: 1rem;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="calculator-container">
        <h2 class="text-center mb-4"><?php echo $translations[$lang]['results_title']; ?></h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cropName" class="form-label"><?php echo $translations[$lang]['crop']; ?></label>
                    <input type="text" class="form-control" id="cropName" name="cropName" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="landSize" class="form-label"><?php echo $translations[$lang]['land_size']; ?></label>
                    <input type="number" step="0.01" class="form-control" id="landSize" name="landSize" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="marketPrice" class="form-label"><?php echo $translations[$lang]['market_price']; ?></label>
                    <input type="number" step="0.01" class="form-control" id="marketPrice" name="marketPrice" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="costPerAcre" class="form-label"><?php echo $translations[$lang]['cost_per_acre']; ?></label>
                    <input type="number" step="0.01" class="form-control" id="costPerAcre" name="costPerAcre" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="yieldPerAcre" class="form-label"><?php echo $translations[$lang]['yield_per_acre']; ?></label>
                    <input type="number" step="0.01" class="form-control" id="yieldPerAcre" name="yieldPerAcre" required>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg"><?php echo $translations[$lang]['calculate_profit']; ?></button>
            </div>
        </form>

        <?php if ($results): ?>
        <div class="results-card card">
            <div class="card-body">
                <h5 class="card-title mb-4"><?php echo $translations[$lang]['profit_analysis']; ?></h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <p><strong><?php echo $translations[$lang]['crop']; ?>:</strong> <?php echo htmlspecialchars($results['crop_name']); ?></p>
                            <p><strong><?php echo $translations[$lang]['total_yield']; ?>:</strong> <?php echo number_format($results['total_yield'], 2) . ' ' . $translations[$lang]['kg']; ?></p>
                            <p><strong><?php echo $translations[$lang]['total_revenue']; ?>:</strong> <?php echo formatINR($results['total_revenue']); ?></p>
                            <p><strong><?php echo $translations[$lang]['total_costs']; ?>:</strong> <?php echo formatINR($results['total_costs']); ?></p>
                            <p class="h4"><strong><?php echo $translations[$lang]['net_profit']; ?>:</strong> <?php echo formatINR($results['profit']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="costBreakdownChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            // Profit Overview Chart
            new Chart(document.getElementById('profitChart'), {
                type: 'bar',
                data: {
                    labels: ['Revenue', 'Costs', 'Profit'],
                    datasets: [{
                        data: [<?php echo $results['total_revenue']; ?>, 
                               <?php echo $results['total_costs']; ?>, 
                               <?php echo $results['profit']; ?>],
                        backgroundColor: ['#2ecc71', '#e74c3c', '#3498db']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Financial Overview'
                        }
                    }
                }
            });
            // Cost Breakdown Doughnut Chart
            new Chart(document.getElementById('costBreakdownChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Seeds & Materials', 'Labor', 'Equipment', 'Other Costs'],
                    datasets: [{
                        data: [
                            <?php echo $results['total_costs'] * 0.3; ?>, // Seeds & Materials (30%)
                            <?php echo $results['total_costs'] * 0.4; ?>, // Labor (40%) 
                            <?php echo $results['total_costs'] * 0.2; ?>, // Equipment (20%)
                            <?php echo $results['total_costs'] * 0.1; ?>  // Other (10%)
                        ],
                        backgroundColor: ['#e74c3c', '#3498db', '#f1c40f', '#95a5a6']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Cost Breakdown Analysis'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Revenue Line Chart
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                    datasets: [{
                        label: 'Projected Revenue',
                        data: [
                            <?php echo $results['total_revenue'] * 0.2; ?>, // Q1 (20%)
                            <?php echo $results['total_revenue'] * 0.3; ?>, // Q2 (30%)
                            <?php echo $results['total_revenue'] * 0.3; ?>, // Q3 (30%)
                            <?php echo $results['total_revenue'] * 0.2; ?>  // Q4 (20%)
                        ],
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Quarterly Revenue Projection'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (₹)'
                            }
                        }
                    }
                }
            });
        </script>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><?php echo $translations[$lang]['back']; ?></a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>