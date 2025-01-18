<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to get commodity prices from Data.gov.in API
function getCommodityPrices($apiKey, $commodity = '') {
    $apiUrl = 'https://api.data.gov.in/resource/9ef84268-d588-465a-a308-a864a43d0070';
    $apiUrl .= '?api-key=' . $apiKey;
    $apiUrl .= '&format=json';
    $apiUrl .= '&limit=100'; // Increased limit to get more records
    if ($commodity) {
        // Allow partial matching of commodity names
        $apiUrl .= '&filters[commodity]=*' . urlencode($commodity) . '*';
    }
    // Add Gujarat filter to get data only from Gujarat state
    $apiUrl .= '&filters[state]=Gujarat';
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Added to handle SSL issues
    
    $response = curl_exec($ch);
    
    // Get cURL error if any
    $error = curl_error($ch);
    curl_close($ch);

    // Check for cURL errors
    if ($error) {
        return ["error" => "Error fetching data: " . $error];
    }

    // Check for empty response
    if (empty($response)) {
        return ["error" => "Empty response from API"];
    }

    // Decode JSON response
    $data = json_decode($response, true);
    
    if (!$data || !is_array($data)) {
        return ["error" => "Error parsing data"];
    }

    // Format data for display
    $formattedData = [];
    if (isset($data['records']) && is_array($data['records'])) {
        $count = 0;
        foreach ($data['records'] as $record) {
            // Limit to 30 records
            if ($count >= 30) break;
            
            if (isset($record['commodity']) && isset($record['modal_price'])) {
                // Price is per quintal (100 kg), convert to per kg
                $price_per_quintal = $record['modal_price'];
                $price_per_kg = $price_per_quintal / 100;
                
                $formattedData[] = [
                    "market" => isset($record['market']) ? $record['market'] : "Market",
                    "commodity" => ucfirst($record['commodity']),
                    "price_inr" => "₹" . number_format($price_per_kg, 2),
                    "price_5kg" => "₹" . number_format($price_per_kg * 5, 2),
                    "price_10kg" => "₹" . number_format($price_per_kg * 10, 2),
                    "date" => isset($record['arrival_date']) ? $record['arrival_date'] : date("Y-m-d"),
                ];
                $count++;
            }
        }
    } else {
        return ["error" => "Invalid data format from API"];
    }

    return empty($formattedData) ? ["error" => "No data found for the specified commodity"] : $formattedData;
}

// API Key for Data.gov.in
$apiKey = '579b464db66ec23bdd000001d5d7b38558604fd467d25a11b06fc1d9';

// Get commodity data
$searchCommodity = isset($_GET['search']) && !empty($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$cropPrices = getCommodityPrices($apiKey, $searchCommodity);
?>
<!DOCTYPE html>
<html lang="en">
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
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="custom-container">
            <div class="title-section">
                <h1><i class="fas fa-chart-line"></i> Market Insights</h1
                <p class="subtitle">Real-time commodity prices from Gujarat Markets</p>
            </div>

            <form class="search-box" method="GET">
                <input type="text" name="search" placeholder="Search for a specific commodity in Gujarat markets" 
                       value="<?php echo htmlspecialchars($searchCommodity); ?>" 
                       class="form-control">
                <button type="submit" class="search-btn">Search</button>
            </form>

            <div class="text-end mb-4">
                <button type="button" class="refresh-btn" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh Prices
                </button>
            </div>

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
                                            <strong>Per KG</strong><br>
                                            <?php echo htmlspecialchars($price['price_inr']); ?>
                                        </div>
                                        <div class="price-item">
                                            <strong>5 KG</strong><br>
                                            <?php echo htmlspecialchars($price['price_5kg']); ?>
                                        </div>
                                        <div class="price-item">
                                            <strong>10 KG</strong><br>
                                            <?php echo htmlspecialchars($price['price_10kg']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt"></i> 
                                        <?php echo htmlspecialchars($price['date']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No data available for this commodity. Please try another commodity like wheat, rice, or potato.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
