<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to call Google Cloud Natural Language API
function getGoogleNLResponse($text) {
    $apiKey = "AIzaSyD-CtXW7S_VtRycQspcjNufxyGBPeCqOc8"; // Replace with your Google Cloud API key
    $url = "https://language.googleapis.com/v1/documents:analyzeEntities?key=$apiKey";

    $data = [
        "document" => [
            "type" => "PLAIN_TEXT",
            "content" => $text,
        ],
        "encodingType" => "UTF8",
    ];

    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\n",
            "method" => "POST",
            "content" => json_encode($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return "Error connecting to the AI service.";
    }

    $responseData = json_decode($response, true);
    return $responseData;
}

// Handle form submission
$aiResponse = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cropType = $_POST['crop_type'];
    $soilType = $_POST['soil_type'];
    $landArea = $_POST['land_area'];
    $season = $_POST['season'];

    // Prepare input for Google Cloud Natural Language API
    $input = "Crop Type: $cropType, Soil Type: $soilType, Land Area: $landArea, Season: $season. Suggest fertilizers, pesticides, and plant care tips.";
    $nlResponse = getGoogleNLResponse($input);

    // Process the response
    if (isset($nlResponse['entities'])) {
        $entities = $nlResponse['entities'];
        $aiResponse = "Based on your input, here are the key entities detected:<br>";
        foreach ($entities as $entity) {
            $aiResponse .= "- " . $entity['name'] . " (Type: " . $entity['type'] . ")<br>";
        }
    } else {
        $aiResponse = "No entities detected. Please provide more details.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Farming Assistant - Kisan.ai</title>
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
        <h1 class="text-center mb-4 fade-in">AI Farming Assistant</h1>
        <div class="row justify-content-center fade-in">
            <div class="col-md-8 glassmorphism p-4">
                <form action="ai_assistant.php" method="POST">
                    <div class="mb-3">
                        <label for="crop_type" class="form-label">Crop Type</label>
                        <input type="text" name="crop_type" class="form-control" placeholder="e.g., Wheat, Rice, Corn" required>
                    </div>
                    <div class="mb-3">
                        <label for="soil_type" class="form-label">Soil Type</label>
                        <input type="text" name="soil_type" class="form-control" placeholder="e.g., Loamy, Sandy, Clay" required>
                    </div>
                    <div class="mb-3">
                        <label for="land_area" class="form-label">Land Area (in acres)</label>
                        <input type="number" name="land_area" class="form-control" placeholder="e.g., 5" required>
                    </div>
                    <div class="mb-3">
                        <label for="season" class="form-label">Season</label>
                        <input type="text" name="season" class="form-control" placeholder="e.g., Winter, Summer, Monsoon" required>
                    </div>
                    <button type="submit" class="btn btn-light w-100">Get Recommendations</button>
                </form>

                <?php if (!empty($aiResponse)): ?>
                    <div class="mt-4">
                        <h3>AI Recommendations</h3>
                        <div class="card glassmorphism p-3">
                            <p><?php echo nl2br($aiResponse); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>