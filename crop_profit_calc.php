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
        'back' => 'Back to Dashboard'
    ],
    'hi' => [
        'results_title' => 'फसल लाभ कैलकुलेटर परिणाम',
        'crop' => 'फसल',
        'total_yield' => 'कुल उपज',
        'total_revenue' => 'कुल राजस्व',
        'total_costs' => 'कुल लागत', 
        'net_profit' => 'शुद्ध लाभ',
        'kg' => 'किग्रा',
        'back' => 'डैशबोर्ड पर वापस जाएं'
    ],
    'gu' => [
        'results_title' => 'પાક નફો કેલ્ક્યુલેટર પરિણામો',
        'crop' => 'પાક',
        'total_yield' => 'કુલ ઉપજ',
        'total_revenue' => 'કુલ આવક',
        'total_costs' => 'કુલ ખર્ચ',
        'net_profit' => 'ચોખ્ખો નફો',
        'kg' => 'કિગ્રા',
        'back' => 'ડેશબોર્ડ પર પાછા જાઓ'
    ]
];

/**
 * Calculate profit metrics for a crop based on input parameters
 */
function calculateCropProfit($cropName, $landSize, $marketPrice, $costPerAcre, $yieldPerAcre) {
    // Calculate total yield (kg)
    $totalYield = $landSize * $yieldPerAcre;
    
    // Calculate total revenue (INR)
    $totalRevenue = $totalYield * $marketPrice;
    
    // Calculate total costs (INR) 
    $totalCosts = $landSize * $costPerAcre;
    
    // Calculate net profit (INR)
    $profit = $totalRevenue - $totalCosts;
    
    return array(
        'crop_name' => $cropName,
        'total_yield' => $totalYield,
        'total_revenue' => $totalRevenue,
        'total_costs' => $totalCosts,
        'profit' => $profit
    );
}

// Format currency values
function formatINR($number) {
    return '₹' . number_format($number, 2);
}

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['results_title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .results-container {
            max-width: 600px;
            width: 100%;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(13, 110, 253, 0.1);
            background-color: #fff;
            margin: 20px;
        }
        .results-header {
            background-color: #0d6efd;
            color: #fff;
            padding: 1.5rem;
            margin: -2rem -2rem 2rem -2rem;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        .result-row {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .result-row:last-child {
            border-bottom: none;
        }
        .result-label {
            color: #0d6efd;
            font-weight: 600;
        }
        .result-value {
            color: #212529;
            font-weight: 500;
        }
        .back-btn {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cropName = $_POST['cropName'];
    $landSize = floatval($_POST['landSize']);
    $marketPrice = floatval($_POST['marketPrice']);
    $costPerAcre = floatval($_POST['costPerAcre']);
    $yieldPerAcre = floatval($_POST['yieldPerAcre']);

    // Calculate results
    $results = calculateCropProfit($cropName, $landSize, $marketPrice, $costPerAcre, $yieldPerAcre);
?>
    <div class="results-container">
        <div class="results-header">
            <h2><?php echo $translations[$lang]['results_title']; ?></h2>
        </div>
        
        <div class="result-row">
            <span class="result-label"><?php echo $translations[$lang]['crop']; ?></span>
            <span class="result-value"><?php echo $results['crop_name']; ?></span>
        </div>
        
        <div class="result-row">
            <span class="result-label"><?php echo $translations[$lang]['total_yield']; ?></span>
            <span class="result-value"><?php echo number_format($results['total_yield'], 2) . " " . $translations[$lang]['kg']; ?></span>
        </div>
        
        <div class="result-row">
            <span class="result-label"><?php echo $translations[$lang]['total_revenue']; ?></span>
            <span class="result-value"><?php echo formatINR($results['total_revenue']); ?></span>
        </div>
        
        <div class="result-row">
            <span class="result-label"><?php echo $translations[$lang]['total_costs']; ?></span>
            <span class="result-value"><?php echo formatINR($results['total_costs']); ?></span>
        </div>
        
        <div class="result-row">
            <span class="result-label"><?php echo $translations[$lang]['net_profit']; ?></span>
            <span class="result-value"><?php echo formatINR($results['profit']); ?></span>
        </div>

        <a href="dashboard.php?lang=<?php echo $lang; ?>" class="back-btn">
            <?php echo $translations[$lang]['back']; ?>
        </a>
    </div>
<?php
}
?>
</body>
</html>
