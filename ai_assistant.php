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
        .glass-container {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .glass-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.2);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #f6f9fc 0%, #edf2f7 100%);
            min-height: 100vh;
        }

        .form-input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
            border-color: #63b3ed;
            outline: none;
        }

        .submit-button {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
            transform: translateY(-2px);
        }

        .recommendation-section {
            line-height: 1.8;
            font-size: 1.05rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 2rem;
        }

        .recommendation-section h3 {
            color: #2d3748;
            font-size: 1.25rem;
            font-weight: 600;
            margin: 1.5rem 0 0.75rem 0;
        }

        .recommendation-section p {
            color: #4a5568;
            margin-bottom: 1rem;
        }

        .recommendation-section ul {
            margin-left: 1.5rem;
            list-style-type: disc;
            color: #4a5568;
        }

        .page-title {
            background: linear-gradient(135deg, #1a5f7a 0%, #2d3748 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="max-w-5xl mx-auto">
        <div class="glass-container p-8 md:p-12">
            <h1 class="text-4xl md:text-5xl font-bold text-center page-title mb-12">
                <i class="bi bi-flower1 text-green-600"></i> AI Farm Assistant
            </h1>

            <form action="ai_assistant.php" method="POST" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium">Soil Type</label>
                        <select name="soilType" class="form-input w-full" required>
                            <option value="">Select Soil Type</option>
                            <option value="clay">Clay Soil</option>
                            <option value="sandy">Sandy Soil</option>
                            <option value="loamy">Loamy Soil</option>
                            <option value="silty">Silty Soil</option>
                            <option value="peaty">Peaty Soil</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium">Season</label>
                        <select name="season" class="form-input w-full" required>
                            <option value="">Select Season</option>
                            <option value="summer">Summer</option>
                            <option value="winter">Winter</option>
                            <option value="monsoon">Monsoon</option>
                            <option value="spring">Spring</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium">Land Area (in acres)</label>
                        <input type="number" name="landArea" min="0.1" step="0.1" class="form-input w-full" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium">Preferred Crop Type</label>
                        <select name="cropType" class="form-input w-full" required>
                            <option value="">Select Crop Type</option>
                            <option value="cereals">Cereals</option>
                            <option value="pulses">Pulses</option>
                            <option value="vegetables">Vegetables</option>
                            <option value="fruits">Fruits</option>
                            <option value="commercial">Commercial Crops</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="submit-button w-full py-4 px-6 text-white rounded-xl font-medium text-lg shadow-lg">
                    Get Recommendations
                </button>
            </form>

            <?php if (!empty($aiResponse)): ?>
                <div id="recommendations" class="mt-12 recommendation-section">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-3">
                        <i class="bi bi-lightbulb text-yellow-400"></i> Your Personalized Farming Recommendations
                    </h2>
                    <div id="recommendationText">
                        <?php echo $aiResponse; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
