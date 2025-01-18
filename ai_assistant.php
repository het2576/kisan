<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to call the WideCanvas AI API
function callWideCanvasAPI($soilType, $season, $landArea, $cropType) {
    $apiUrl = "https://r0c8kgwocscg8gsokogwwsw4.zetaverse.one/ai"; // WideCanvas API endpoint
    $apiToken = "efcNrkdR7OVnAwFzqITfdECW7WM2"; // WideCanvas API token

    // Prepare the API payload
    $data = [
        "messages" => [
            [
                "role" => "user",
                "content" => [
                    [
                        "type" => "text",
                        "text" => "Please provide detailed farming recommendations for the following conditions:
                            Soil Type: $soilType
                            Season: $season
                            Land Area: $landArea acres
                            Preferred Crop Type: $cropType
                            
                            Include information about:
                            1. Recommended crops
                            2. Soil preparation
                            3. Irrigation requirements
                            4. Fertilizer recommendations
                            5. Pest management
                            6. Expected yield
                            7. Best farming practices"
                    ]
                ]
            ]
        ]
    ];

    // Setup the request headers
    $headers = [
        "Authorization: Bearer $apiToken",
        "Content-Type: application/json",
    ];

    // Initialize CURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Handle errors
    if ($response === false || $httpCode !== 200) {
        $errorMessage = curl_error($ch) ?: "HTTP $httpCode: Unable to fetch recommendations.";
        curl_close($ch);
        return ["error" => $errorMessage];
    }

    curl_close($ch);

    // Decode and return the response
    return json_decode($response, true);
}

// Handle form submission
$aiResponse = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $soilType = $_POST['soilType'];
    $season = $_POST['season'];
    $landArea = $_POST['landArea'];
    $cropType = $_POST['cropType'];

    // Call the WideCanvas API
    $apiResult = callWideCanvasAPI($soilType, $season, $landArea, $cropType);

    // Process the API response
    if (isset($apiResult['message'])) {
        $aiResponse = nl2br(htmlspecialchars($apiResult['message']));
    } elseif (isset($apiResult['error'])) {
        $aiResponse = "Error: " . htmlspecialchars($apiResult['error']);
    } else {
        $aiResponse = "No recommendations available. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Farm Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .clay-morphism {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 16px;
            box-shadow: 
                35px 35px 68px 0px rgba(145, 192, 255, 0.5),
                inset -8px -8px 16px 0px rgba(145, 192, 255, 0.6),
                inset 0px 11px 28px 0px rgb(255, 255, 255);
            backdrop-filter: blur(5px);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #EBF5FB;
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <div class="clay-morphism p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-center text-gray-800 mb-6">
                <i class="bi bi-flower1 text-green-600"></i> AI Farm Assistant
            </h1>

            <form action="ai_assistant.php" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Soil Type</label>
                        <select name="soilType" class="w-full p-3 border rounded-lg bg-white" required>
                            <option value="">Select Soil Type</option>
                            <option value="clay">Clay Soil</option>
                            <option value="sandy">Sandy Soil</option>
                            <option value="loamy">Loamy Soil</option>
                            <option value="silty">Silty Soil</option>
                            <option value="peaty">Peaty Soil</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Season</label>
                        <select name="season" class="w-full p-3 border rounded-lg bg-white" required>
                            <option value="">Select Season</option>
                            <option value="summer">Summer</option>
                            <option value="winter">Winter</option>
                            <option value="monsoon">Monsoon</option>
                            <option value="spring">Spring</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Land Area (in acres)</label>
                        <input type="number" name="landArea" min="0.1" step="0.1" class="w-full p-3 border rounded-lg bg-white" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Preferred Crop Type</label>
                        <select name="cropType" class="w-full p-3 border rounded-lg bg-white" required>
                            <option value="">Select Crop Type</option>
                            <option value="cereals">Cereals</option>
                            <option value="pulses">Pulses</option>
                            <option value="vegetables">Vegetables</option>
                            <option value="fruits">Fruits</option>
                            <option value="commercial">Commercial Crops</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 px-6 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Get Recommendations
                </button>
            </form>

            <?php if (!empty($aiResponse)): ?>
                <div id="recommendations" class="mt-8 clay-morphism p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="bi bi-lightbulb text-yellow-500"></i> Recommendations
                    </h2>
                    <div id="recommendationText" class="text-gray-700 space-y-4">
                        <?php echo $aiResponse; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
