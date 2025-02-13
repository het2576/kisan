<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get language from session that was set in dashboard
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations array
$translations = [
    'en' => [
        'market_insights' => 'Market Insights',
        'realtime_prices' => 'Real-time commodity prices from Gujarat Markets',
        'search_placeholder' => 'Search for a specific commodity in Gujarat markets',
        'search' => 'Search',
        'refresh_prices' => 'Refresh Prices',
        'per_kg' => 'Per KG',
        'kg' => 'KG',
        'no_data' => 'No data available for this commodity.',
        'last_updated' => 'Last Updated',
        'showing_results' => 'Showing results for',
        'load_more' => 'Load More',
        'dashboard' => 'Dashboard'
    ],
    'hi' => [
        'market_insights' => 'बाजार अंतर्दृष्टि',
        'realtime_prices' => 'गुजरात बाजारों से वास्तविक समय की वस्तु कीमतें',
        'search_placeholder' => 'गुजरात बाजारों में किसी विशिष्ट वस्तु की खोज करें',
        'search' => 'खोज',
        'refresh_prices' => 'कीमतें रिफ्रेश करें',
        'per_kg' => 'प्रति किलो',
        'kg' => 'किलो',
        'no_data' => 'इस वस्तु के लिए कोई डेटा उपलब्ध नहीं है।',
        'last_updated' => 'अंतिम अपडेट',
        'showing_results' => 'परिणाम दिखाए जा रहे हैं',
        'load_more' => 'और लोड करें',
        'dashboard' => 'डैशबोर्ड'
    ],
    'gu' => [
        'market_insights' => 'બજાર માહિતી',
        'realtime_prices' => 'ગુજરાત બજારોમાંથી રીયલ-ટાઈમ કોમોડિટી ભાવો',
        'search_placeholder' => 'ગુજરાત બજારોમાં કોઈ ચોક્કસ કોમોડિટી શોધો',
        'search' => 'શોધો',
        'refresh_prices' => 'ભાવો રિફ્રેશ કરો',
        'per_kg' => 'પ્રતિ કિલો',
        'kg' => 'કિલો',
        'no_data' => 'આ કોમોડિટી માટે કોઈ માહિતી ઉપલબ્ધ નથી.',
        'last_updated' => 'છેલ્લે અપડેટ કર્યું',
        'showing_results' => 'પરિણામો',
        'load_more' => 'વધુ લોડ કરો',
        'dashboard' => 'ડેશબોર્ડ'
    ]
];

// Function to get commodity prices from Data.gov.in API with caching
function getCommodityPrices($apiKey, $commodity = '', $page = 1) {
    $cacheFile = 'cache/market_prices.json';
    $cacheTime = 3600; // Cache for 1 hour
    $perPage = 10; // Items per page

    // Check if cache exists and is fresh
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
        $cachedData = json_decode(file_get_contents($cacheFile), true);
        if ($commodity) {
            $filteredData = array_filter($cachedData, function($item) use ($commodity) {
                return stripos($item['commodity'], $commodity) !== false;
            });
            return array_slice($filteredData, ($page - 1) * $perPage, $perPage);
        }
        return array_slice($cachedData, ($page - 1) * $perPage, $perPage);
    }

    $apiUrl = 'https://api.data.gov.in/resource/9ef84268-d588-465a-a308-a864a43d0070';
    $apiUrl .= '?api-key=' . $apiKey;
    $apiUrl .= '&format=json';
    $apiUrl .= '&limit=1000'; // Increased limit to get more data
    $apiUrl .= '&filters[state]=Gujarat'; // Filter for Gujarat state only
    
    // Add current date filter
    $today = date('d/m/Y');
    $apiUrl .= '&filters[arrival_date]=' . urlencode($today);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ["error" => "Error fetching data: " . $error];
    }

    if (empty($response)) {
        // Fallback data when API fails - now includes multiple Gujarat markets
        $fallbackData = [
            ["market" => "Ahmedabad", "commodity" => "Rice", "modal_price" => "3500"],
            ["market" => "Surat", "commodity" => "Wheat", "modal_price" => "2800"],
            ["market" => "Vadodara", "commodity" => "Cotton", "modal_price" => "5500"],
            ["market" => "Rajkot", "commodity" => "Groundnut", "modal_price" => "4200"],
            ["market" => "Bhavnagar", "commodity" => "Soybean", "modal_price" => "3800"],
            ["market" => "Jamnagar", "commodity" => "Mustard", "modal_price" => "4500"],
            ["market" => "Junagadh", "commodity" => "Cumin", "modal_price" => "15000"],
            ["market" => "Gandhinagar", "commodity" => "Chickpea", "modal_price" => "4800"],
            ["market" => "Anand", "commodity" => "Maize", "modal_price" => "2200"],
            ["market" => "Bharuch", "commodity" => "Sugarcane", "modal_price" => "300"],
            ["market" => "Mehsana", "commodity" => "Potato", "modal_price" => "1500"],
            ["market" => "Patan", "commodity" => "Onion", "modal_price" => "1800"],
            ["market" => "Kheda", "commodity" => "Tomato", "modal_price" => "2000"],
            ["market" => "Amreli", "commodity" => "Garlic", "modal_price" => "8000"],
            ["market" => "Porbandar", "commodity" => "Chili", "modal_price" => "7000"],
            ["market" => "Gondal", "commodity" => "Cotton", "modal_price" => "5200"],
            ["market" => "Morbi", "commodity" => "Groundnut", "modal_price" => "4100"],
            ["market" => "Surendranagar", "commodity" => "Wheat", "modal_price" => "2700"],
            ["market" => "Veraval", "commodity" => "Rice", "modal_price" => "3600"],
            ["market" => "Nadiad", "commodity" => "Maize", "modal_price" => "2300"]
        ];
        $data = ["records" => $fallbackData];
    } else {
        $data = json_decode($response, true);
    }
    
    if (!$data || !is_array($data)) {
        return ["error" => "Error parsing data"];
    }

    $formattedData = [];
    if (isset($data['records']) && is_array($data['records'])) {
        foreach ($data['records'] as $record) {
            if (isset($record['commodity']) && isset($record['modal_price'])) {
                $price_per_quintal = floatval($record['modal_price']);
                $price_per_kg = $price_per_quintal / 100;
                
                if ($price_per_kg > 0) {
                    $formattedData[] = [
                        "market" => isset($record['market']) ? $record['market'] : "Market",
                        "commodity" => ucfirst(strtolower($record['commodity'])),
                        "price_inr" => "₹" . number_format($price_per_kg, 2),
                        "price_5kg" => "₹" . number_format($price_per_kg * 5, 2),
                        "price_10kg" => "₹" . number_format($price_per_kg * 10, 2),
                        "date" => date("Y-m-d"),
                        "last_updated" => date("d/m/Y")
                    ];
                }
            }
        }
    }

    // Cache the results
    if (!empty($formattedData)) {
        if (!is_dir('cache')) {
            mkdir('cache', 0777, true);
        }
        file_put_contents($cacheFile, json_encode($formattedData));
    }

    if ($commodity) {
        $filteredData = array_filter($formattedData, function($item) use ($commodity) {
            return stripos($item['commodity'], $commodity) !== false;
        });
        return array_slice($filteredData, ($page - 1) * $perPage, $perPage);
    }

    return empty($formattedData) ? ["error" => "No data found for the specified commodity"] : array_slice($formattedData, ($page - 1) * $perPage, $perPage);
}

// API Key for Data.gov.in
$apiKey = '579b464db66ec23bdd000001d5d7b38558604fd467d25a11b06fc1d9';

// Get page number from GET parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get commodity data
$searchCommodity = isset($_GET['search']) && !empty($_GET['search']) ? trim($_GET['search']) : '';
$cropPrices = getCommodityPrices($apiKey, $searchCommodity, $page);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Insights - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #003366;
            --accent-color: #e6f0ff;
        }

        .custom-container {
            background: linear-gradient(135deg, var(--accent-color), #ffffff);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        body {
            background-color: #f8f9fa;
            color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 2rem;
        }

        .search-box input {
            padding: 1rem;
            border-radius: 10px;
            border: 2px solid var(--primary-color);
            width: 100%;
            font-size: 1.1rem;
        }

        .search-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-btn:hover {
            background-color: var(--secondary-color);
        }

        .price-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .price-card:hover {
            transform: translateY(-5px);
        }

        .currency-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            margin: 0.5rem;
            display: inline-block;
        }

        .refresh-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        .title-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .title-section h1 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .subtitle {
            color: var(--secondary-color);
            font-size: 1.2rem;
        }

        .price-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .price-item {
            text-align: center;
            padding: 0.5rem;
            background: var(--accent-color);
            border-radius: 8px;
        }

        .last-updated {
            font-size: 0.9rem;
            color: #666;
            font-style: italic;
        }

        .search-results {
            margin: 1rem 0;
            padding: 0.5rem 1rem;
            background-color: var(--accent-color);
            border-radius: 8px;
            font-weight: bold;
        }

        .load-more-container {
            text-align: center;
            margin-top: 2rem;
        }

        .load-more-btn {
            padding: 1rem 3rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .load-more-btn:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <!-- Back to Dashboard Button -->
    <div class="container mt-3">
        <a href="dashboard.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i><?php echo $translations[$lang]['dashboard']; ?>
        </a>
    </div>

    <div class="container py-5">
        <div class="custom-container">
            <div class="title-section">
                <h1><i class="fas fa-chart-line"></i> <?php echo $translations[$lang]['market_insights']; ?></h1>
                <p class="subtitle"><?php echo $translations[$lang]['realtime_prices']; ?></p>
            </div>

            <form class="search-box" method="GET">
                <input type="text" name="search" placeholder="<?php echo $translations[$lang]['search_placeholder']; ?>" 
                       value="<?php echo htmlspecialchars($searchCommodity); ?>" 
                       class="form-control">
                <button type="submit" class="search-btn"><?php echo $translations[$lang]['search']; ?></button>
            </form>

            <div class="text-end mb-4">
                <button type="button" class="refresh-btn" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> <?php echo $translations[$lang]['refresh_prices']; ?>
                </button>
            </div>

            <?php if (!empty($searchCommodity)): ?>
                <div class="search-results">
                    <?php echo $translations[$lang]['showing_results']; ?>: <?php echo htmlspecialchars($searchCommodity); ?> 
                    (<?php echo count($cropPrices); ?> results)
                </div>
            <?php endif; ?>

            <?php if (!empty($cropPrices) && is_array($cropPrices)): ?>
                <?php if (isset($cropPrices['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($cropPrices['error']); ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($cropPrices as $price): ?>
                        <div class="price-card">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h4><i class="fas fa-box"></i> <?php echo htmlspecialchars($price['commodity']); ?></h4>
                                    <small class="text-muted"><?php echo htmlspecialchars($price['market']); ?></small>
                                </div>
                                <div class="col-md-6">
                                    <div class="price-grid">
                                        <div class="price-item">
                                            <strong><?php echo $translations[$lang]['per_kg']; ?></strong><br>
                                            <?php echo htmlspecialchars($price['price_inr']); ?>
                                        </div>
                                        <div class="price-item">
                                            <strong>5 <?php echo $translations[$lang]['kg']; ?></strong><br>
                                            <?php echo htmlspecialchars($price['price_5kg']); ?>
                                        </div>
                                        <div class="price-item">
                                            <strong>10 <?php echo $translations[$lang]['kg']; ?></strong><br>
                                            <?php echo htmlspecialchars($price['price_10kg']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> 
                                        <?php echo $translations[$lang]['last_updated']; ?>: <?php echo date('d/m/Y'); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="load-more-container">
                        <form method="GET">
                            <?php if (!empty($searchCommodity)): ?>
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchCommodity); ?>">
                            <?php endif; ?>
                            <input type="hidden" name="page" value="<?php echo $page + 1; ?>">
                            <button type="submit" class="load-more-btn">
                                <?php echo $translations[$lang]['load_more']; ?>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <?php echo $translations[$lang]['no_data']; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>
