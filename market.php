<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to scrape crop prices from Agmarknet
function scrapeAgmarknetPrices($url) {
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (optional, for HTTPS)
    
    $html = curl_exec($ch);
    curl_close($ch);

    // Check for errors
    if ($html === false) {
        return "Error fetching data.";
    }

    // Load HTML into DOMDocument
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Suppress warnings for invalid HTML
    $dom->loadHTML($html);
    libxml_clear_errors();

    // Parse data using DOMXPath
    $xpath = new DOMXPath($dom);
    $rows = $xpath->query("//table[@class='High']/tr"); // Adjust XPath to target the table rows

    // Extract data
    $data = [];
    foreach ($rows as $row) {
        $cols = $row->getElementsByTagName("td");
        if ($cols->length > 0) {
            $data[] = [
                "market" => trim($cols->item(0)->nodeValue),
                "commodity" => trim($cols->item(1)->nodeValue),
                "price" => trim($cols->item(2)->nodeValue),
                "date" => trim($cols->item(3)->nodeValue),
            ];
        }
    }

    return $data;
}

// URL of the Agmarknet search results page
$url = "https://www.agriwatch.com/market-prices"; // Replace with the appropriate URL

// Scrape data
$cropPrices = scrapeAgmarknetPrices($url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Insights - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        }

        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .card {
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4 fade-in">Market Insights</h1>
        <div class="row fade-in">
            <div class="col-md-12 glassmorphism p-4">
                <h3>Real-Time Crop Prices</h3>
                <table class="table table-hover text-light">
                    <thead>
                        <tr>
                            <th>Market</th>
                            <th>Commodity</th>
                            <th>Price</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cropPrices) && is_array($cropPrices)): ?>
                            <?php foreach ($cropPrices as $price): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($price['market']); ?></td>
                                    <td><?php echo htmlspecialchars($price['commodity']); ?></td>
                                    <td><?php echo htmlspecialchars($price['price']); ?></td>
                                    <td><?php echo htmlspecialchars($price['date']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No data available or error fetching data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
