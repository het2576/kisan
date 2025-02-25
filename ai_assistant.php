<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to call the WideCanvas AI API
function callWideCanvasAPI($soilType, $season, $landArea, $cropType, $language) {
    $apiUrl = "https://r0c8kgwocscg8gsokogwwsw4.zetaverse.one/ai"; // WideCanvas API endpoint
    $apiToken = "efcNrkdR7OVnAwFzqITfdECW7WM2"; // WideCanvas API token

    // Prepare prompts in different languages
    $prompts = [
        'en' => "I am Krishi, your personal farming advisor. Let me provide detailed farming recommendations for your conditions:
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
                7. Best farming practices",
        'hi' => "मैं कृषि हूं, आपका व्यक्तिगत कृषि सलाहकार। कृपया निम्नलिखित स्थितियों के लिए विस्तृत कृषि सिफारिशें प्रदान करें:
                मिट्टी का प्रकार: $soilType
                मौसम: $season
                भूमि क्षेत्र: $landArea एकड़
                पसंदीदा फसल प्रकार: $cropType
                
                इन बिंदुओं की जानकारी शामिल करें:
                1. अनुशंसित फसलें
                2. मिट्टी की तैयारी
                3. सिंचाई आवश्यकताएं
                4. उर्वरक सिफारिशें
                5. कीट प्रबंधन
                6. अपेक्षित उपज
                7. सर्वोत्तम कृषि प्रथाएं",
        'gu' => "હું કૃષિ છું, તમારો વ્યક્તિગત ખેતી સલાહકાર. કૃપા કરીને નીચેની પરિસ્થિતિઓ માટે વિગતવાર ખેતી ભલામણો પ્રદાન કરો:
                જમીનનો પ્રકાર: $soilType
                ઋતુ: $season
                જમીન વિસ્તાર: $landArea એકર
                પસંદગીનો પાક પ્રકાર: $cropType
                
                આ માહિતી શામેલ કરો:
                1. ભલામણ કરેલા પાકો
                2. જમીનની તૈયારી
                3. સિંચાઈની જરૂરિયાતો
                4. ખાતરની ભલામણો
                5. જીવાત વ્યવસ્થાપન
                6. અપેક્ષિત ઉપજ
                7. શ્રેષ્ઠ ખેતી પદ્ધતિઓ"
    ];

    // Add language instruction to system message
    $systemMessages = [
        'en' => "I am Krishi, a farming expert with years of experience. I will provide all responses in English only.",
        'hi' => "मैं कृषि हूं, वर्षों के अनुभव वाला कृषि विशेषज्ञ। कृपया सभी उत्तर केवल हिंदी में प्रदान करें।",
        'gu' => "હું કૃષિ છું, વર્ષોના અનુભવ સાથે ખેતી નિષ્ણાત છું. કૃપા કરીને બધા જવાબો માત્ર ગુજરાતીમાં જ આપો."
    ];

    // Prepare the API payload with system message
    $data = [
        "messages" => [
            [
                "role" => "system",
                "content" => $systemMessages[$language]
            ],
            [
                "role" => "user",
                "content" => $prompts[$language]
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
        $errorMessages = [
            'en' => "Error: Unable to fetch recommendations from Krishi.",
            'hi' => "त्रुटि: कृषि से सिफारिशें प्राप्त करने में असमर्थ।",
            'gu' => "ભૂલ: કૃષિ પાસેથી ભલામણો મેળવવામાં અસમર્થ."
        ];
        $errorMessage = curl_error($ch) ?: "HTTP $httpCode: " . $errorMessages[$language];
        curl_close($ch);
        return ["error" => $errorMessage];
    }

    curl_close($ch);

    // Decode and return the response
    $decodedResponse = json_decode($response, true);
    
    // Extract the message from the response structure
    if (isset($decodedResponse['choices'][0]['message']['content'])) {
        return ["message" => $decodedResponse['choices'][0]['message']['content']];
    }
    
    return $decodedResponse;
}

// Handle form submission
$aiResponse = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables with default empty values
    $soilType = isset($_POST['soilType']) ? $_POST['soilType'] : '';
    $season = isset($_POST['season']) ? $_POST['season'] : '';
    $landArea = isset($_POST['landArea']) ? $_POST['landArea'] : '';
    $cropType = isset($_POST['cropType']) ? $_POST['cropType'] : '';
    $language = isset($_POST['language']) ? $_POST['language'] : 'en';

    // Only call API if all required fields are filled
    if (!empty($soilType) && !empty($season) && !empty($landArea) && !empty($cropType)) {
        // Call the WideCanvas API
        $apiResult = callWideCanvasAPI($soilType, $season, $landArea, $cropType, $language);

        // Process the API response
        if (isset($apiResult['message'])) {
            $aiResponse = nl2br(htmlspecialchars($apiResult['message']));
        } elseif (isset($apiResult['error'])) {
            $aiResponse = htmlspecialchars($apiResult['error']);
        } else {
            $noRecommendations = [
                'en' => "Krishi has no recommendations available at this time. Please try again.",
                'hi' => "कृषि के पास इस समय कोई सिफारिश उपलब्ध नहीं है। कृपया पुनः प्रयास करें।",
                'gu' => "કૃષિ પાસે આ સમયે કોઈ ભલામણો ઉપલબ્ધ નથી. કૃપા કરીને ફરી પ્રયાસ કરો."
            ];
            $aiResponse = $noRecommendations[$language];
        }
    }
}

// Language-specific labels and options
$labels = [
    'en' => [
        'title' => 'Krishi - Your Farm Advisor',
        'soilType' => 'Soil Type',
        'season' => 'Season',
        'landArea' => 'Land Area (in acres)',
        'cropType' => 'Preferred Crop Type',
        'submit' => 'Get Recommendations',
        'recommendations' => 'Your Personalized Farming Recommendations from Krishi',
        'selectLanguage' => 'Select Language',
        'soilTypes' => [
            'clay' => 'Clay Soil',
            'sandy' => 'Sandy Soil',
            'loamy' => 'Loamy Soil',
            'silty' => 'Silty Soil',
            'peaty' => 'Peaty Soil'
        ],
        'seasons' => [
            'summer' => 'Summer',
            'winter' => 'Winter',
            'monsoon' => 'Monsoon',
            'spring' => 'Spring'
        ],
        'cropTypes' => [
            'cereals' => 'Cereals',
            'pulses' => 'Pulses',
            'vegetables' => 'Vegetables',
            'fruits' => 'Fruits',
            'commercial' => 'Commercial Crops'
        ]
    ],
    'hi' => [
        'title' => 'कृषि - आपका कृषि सलाहकार',
        'soilType' => 'मिट्टी का प्रकार',
        'season' => 'मौसम',
        'landArea' => 'भूमि क्षेत्र (एकड़ में)',
        'cropType' => 'पसंदीदा फसल प्रकार',
        'submit' => 'सिफारिशें प्राप्त करें',
        'recommendations' => 'कृषि से आपकी व्यक्तिगत कृषि सिफारिशें',
        'selectLanguage' => 'भाषा चुनें',
        'soilTypes' => [
            'clay' => 'चिकनी मिट्टी',
            'sandy' => 'बलुई मिट्टी',
            'loamy' => 'दोमट मिट्टी',
            'silty' => 'गाद मिट्टी',
            'peaty' => 'पीट मिट्टी'
        ],
        'seasons' => [
            'summer' => 'गर्मी',
            'winter' => 'सर्दी',
            'monsoon' => 'बरसात',
            'spring' => 'वसंत'
        ],
        'cropTypes' => [
            'cereals' => 'अनाज',
            'pulses' => 'दालें',
            'vegetables' => 'सब्जियां',
            'fruits' => 'फल',
            'commercial' => 'व्यावसायिक फसलें'
        ]
    ],
    'gu' => [
        'title' => 'કૃષિ - તમારો ખેતી સલાહકાર',
        'soilType' => 'જમીનનો પ્રકાર',
        'season' => 'ઋતુ',
        'landArea' => 'જમીન વિસ્તાર (એકરમાં)',
        'cropType' => 'પસંદગીનો પાક પ્રકાર',
        'submit' => 'ભલામણો મેળવો',
        'recommendations' => 'કૃષિ તરફથી તમારી વ્યક્તિગત ખેતી ભલામણો',
        'selectLanguage' => 'ભાષા પસંદ કરો',
        'soilTypes' => [
            'clay' => 'માટીવાળી જમીન',
            'sandy' => 'રેતાળ જમીન',
            'loamy' => 'દોમટ જમીન',
            'silty' => 'કાંપવાળી જમીન',
            'peaty' => 'પીટ જમીન'
        ],
        'seasons' => [
            'summer' => 'ઉનાળો',
            'winter' => 'શિયાળો',
            'monsoon' => 'ચોમાસું',
            'spring' => 'વસંત'
        ],
        'cropTypes' => [
            'cereals' => 'અનાજ',
            'pulses' => 'કઠોળ',
            'vegetables' => 'શાકભાજી',
            'fruits' => 'ફળો',
            'commercial' => 'વ્યાપારી પાકો'
        ]
    ]
];

$currentLang = isset($_POST['language']) ? $_POST['language'] : 'en';
$l = $labels[$currentLang];
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $l['title']; ?></title>
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
    <!-- Add Back to Dashboard Button -->
    <div class="max-w-5xl mx-auto mb-8">
        <a href="dashboard.php" class="inline-flex items-center px-4 py-2 bg-white text-green-600 rounded-lg shadow hover:bg-green-50 transition-colors">
            <i class="bi bi-arrow-left me-2"></i>
            <?php 
            $backText = [
                'en' => 'Back to Dashboard',
                'hi' => 'डैशबोर्ड पर वापस जाएं',
                'gu' => 'ડેશબોર્ડ પર પાછા જાઓ'
            ];
            echo $backText[$currentLang];
            ?>
        </a>
    </div>
    <div class="max-w-5xl mx-auto">
        <div class="glass-container p-8 md:p-12">
            <div class="flex justify-end mb-4">
                <form id="languageForm" method="POST" class="space-y-2">
                    <label class="block text-gray-700 font-medium"><?php echo $l['selectLanguage']; ?></label>
                    <select name="language" class="form-input" onchange="document.getElementById('languageForm').submit()">
                        <option value="en" <?php echo $currentLang == 'en' ? 'selected' : ''; ?>>English</option>
                        <option value="hi" <?php echo $currentLang == 'hi' ? 'selected' : ''; ?>>हिंदी</option>
                        <option value="gu" <?php echo $currentLang == 'gu' ? 'selected' : ''; ?>>ગુજરાતી</option>
                    </select>
                </form>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold text-center page-title mb-12">
                <i class="bi bi-flower1 text-green-600"></i> <?php echo $l['title']; ?>
            </h1>

            <form id="farmForm" action="ai_assistant.php" method="POST" class="space-y-8">
                <input type="hidden" name="language" value="<?php echo $currentLang; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium"><?php echo $l['soilType']; ?></label>
                        <select name="soilType" class="form-input w-full" required>
                            <option value=""><?php echo $l['soilType']; ?></option>
                            <?php foreach ($l['soilTypes'] as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium"><?php echo $l['season']; ?></label>
                        <select name="season" class="form-input w-full" required>
                            <option value=""><?php echo $l['season']; ?></option>
                            <?php foreach ($l['seasons'] as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium"><?php echo $l['landArea']; ?></label>
                        <input type="number" name="landArea" min="0.1" step="0.1" class="form-input w-full" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium"><?php echo $l['cropType']; ?></label>
                        <select name="cropType" class="form-input w-full" required>
                            <option value=""><?php echo $l['cropType']; ?></option>
                            <?php foreach ($l['cropTypes'] as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="submit-button w-full py-4 px-6 text-white rounded-xl font-medium text-lg shadow-lg">
                    <?php echo $l['submit']; ?>
                </button>
            </form>

            <?php if (!empty($aiResponse)): ?>
                <div id="recommendations" class="mt-12 recommendation-section">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-3">
                        <i class="bi bi-lightbulb text-yellow-400"></i> <?php echo $l['recommendations']; ?>
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
