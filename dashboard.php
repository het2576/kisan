<?php
session_start(); // Start the session

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Set language based on selection or session
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang; // Store language in session
} else if (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = 'en';
    $_SESSION['lang'] = $lang;
}

$translations = [
    'en' => [
        'welcome' => 'Welcome',
        'inventory' => 'Inventory',
        'market' => 'Market Insights', 
        'weather' => 'Weather',
        'tools' => 'Tools',
        'ai' => 'AI Assistant',
        'logout' => 'Logout',
        'profit_calc' => 'Profit Calculator',
        'calculate_profit' => 'Calculate Profit',
        'crop_name' => 'Crop Name',
        'land_size' => 'Land Size (acres)',
        'market_price' => 'Market Price (₹/kg)',
        'cost_per_acre' => 'Cost per Acre (₹)',
        'yield_per_acre' => 'Yield per Acre (kg)'
    ],
    'hi' => [
        'welcome' => 'स्वागत है',
        'inventory' => 'इन्वेंटरी',
        'market' => 'बाजार अंतर्दृष्टि',
        'weather' => 'मौसम',
        'tools' => 'उपकरण',
        'ai' => 'एआई सहायक',
        'logout' => 'लॉग आउट',
        'profit_calc' => 'लाभ कैलकुलेटर',
        'calculate_profit' => 'लाभ की गणना करें',
        'crop_name' => 'फसल का नाम',
        'land_size' => 'भूमि का आकार (एकड़)',
        'market_price' => 'बाजार मूल्य (₹/किग्रा)',
        'cost_per_acre' => 'प्रति एकड़ लागत (₹)',
        'yield_per_acre' => 'प्रति एकड़ उपज (किग्रा)'
    ],
    'gu' => [
        'welcome' => 'સ્વાગત છે',
        'inventory' => 'ઇન્વેન્ટરી',
        'market' => 'બજાર માહિતી',
        'weather' => 'હવામાન',
        'tools' => 'સાધનો',
        'ai' => 'AI સહાયક',
        'logout' => 'લૉગ આઉટ',
        'profit_calc' => 'નફો કેલ્ક્યુલેટર',
        'calculate_profit' => 'નફો ગણો',
        'crop_name' => 'પાક નામ',
        'land_size' => 'જમીન કદ (એકર)',
        'market_price' => 'બજાર ભાવ (₹/કિગ્રા)',
        'cost_per_acre' => 'એકર દીઠ ખર્ચ (₹)',
        'yield_per_acre' => 'એકર દીઠ ઉપજ (કિગ્રા)'
    ]
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: #f8f9fa;
            color: #0d6efd;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        .card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            min-height: 220px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(13,110,253,0.1);
        }

        .card h3 {
            color: #0d6efd;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card p {
            color: #6c757d;
        }

        .btn-primary {
            background: #0d6efd;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
        }

        .welcome-text {
            color: #0d6efd;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .lang-selector {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .lang-btn {
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 4px;
            border: 1px solid #0d6efd;
            background: white;
            color: #0d6efd;
            text-decoration: none;
        }

        .lang-btn:hover {
            background: #0d6efd;
            color: white;
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            background: #0d6efd;
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Language Selector -->
        <div class="lang-selector">
            <a href="?lang=en" class="lang-btn">English</a>
            <a href="?lang=hi" class="lang-btn">हिंदी</a>
            <a href="?lang=gu" class="lang-btn">ગુજરાતી</a>
        </div>

        <!-- Welcome Message -->
        <h1 class="text-center welcome-text"><?php echo $translations[$lang]['welcome']; ?>, <?php echo $_SESSION['name']; ?>!</h1>

        <!-- Dashboard Cards -->
        <div class="row g-4">
            <!-- Inventory Card -->
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h3>📦 <?php echo $translations[$lang]['inventory']; ?></h3>
                    <p>Manage your farm inventory efficiently.</p>
                    <a href="inventory.php" class="btn btn-primary">Manage</a>
                </div>
            </div>

            <!-- Market Insights Card -->
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h3>📈 <?php echo $translations[$lang]['market']; ?></h3>
                    <p>Get real-time market prices and trends.</p>
                    <a href="market.php" class="btn btn-primary">View</a>
                </div>
            </div>

            <!-- Weather Card -->
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h3>🌦️ <?php echo $translations[$lang]['weather']; ?></h3>
                    <p>Check weather forecasts for better planning.</p>
                    <a href="weather.php" class="btn btn-primary">Check</a>
                </div>
            </div>

            <!-- Tools Card -->
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h3>🛠️ <?php echo $translations[$lang]['tools']; ?></h3>
                    <p>Manage and craft farm tools.</p>
                    <a href="tools.php" class="btn btn-primary">Manage</a>
                </div>
            </div>

            <!-- AI Farming Assistant Card -->
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h3>🤖 <?php echo $translations[$lang]['ai']; ?></h3>
                    <p>Get farming recommendations from AI.</p>
                    <a href="ai_assistant.php" class="btn btn-primary">Ask AI</a>
                </div>
            </div>

            <!-- Profit Calculator Card -->
            <div class="col-md-4">
                <div class="card p-4 text-center">
                    <h3>💰 <?php echo $translations[$lang]['profit_calc']; ?></h3>
                    <p>Calculate your crop profits easily.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profitCalcModal">
                        <?php echo $translations[$lang]['calculate_profit']; ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Profit Calculator Modal -->
        <div class="modal fade" id="profitCalcModal" tabindex="-1" aria-labelledby="profitCalcModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profitCalcModalLabel"><?php echo $translations[$lang]['profit_calc']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="crop_profit_calc.php" method="POST">
                            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                            <div class="mb-3">
                                <label for="cropName" class="form-label"><?php echo $translations[$lang]['crop_name']; ?></label>
                                <input type="text" class="form-control" id="cropName" name="cropName" required>
                            </div>
                            <div class="mb-3">
                                <label for="landSize" class="form-label"><?php echo $translations[$lang]['land_size']; ?></label>
                                <input type="number" step="0.01" class="form-control" id="landSize" name="landSize" required>
                            </div>
                            <div class="mb-3">
                                <label for="marketPrice" class="form-label"><?php echo $translations[$lang]['market_price']; ?></label>
                                <input type="number" step="0.01" class="form-control" id="marketPrice" name="marketPrice" required>
                            </div>
                            <div class="mb-3">
                                <label for="costPerAcre" class="form-label"><?php echo $translations[$lang]['cost_per_acre']; ?></label>
                                <input type="number" step="0.01" class="form-control" id="costPerAcre" name="costPerAcre" required>
                            </div>
                            <div class="mb-3">
                                <label for="yieldPerAcre" class="form-label"><?php echo $translations[$lang]['yield_per_acre']; ?></label>
                                <input type="number" step="0.01" class="form-control" id="yieldPerAcre" name="yieldPerAcre" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><?php echo $translations[$lang]['calculate_profit']; ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="text-center mt-5">
            <a href="logout.php" class="btn btn-outline-danger"><?php echo $translations[$lang]['logout']; ?></a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
