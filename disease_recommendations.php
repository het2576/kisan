<?php
// Disease recommendations database with detailed information
$disease_recommendations = [
    'bacterial_leaf_blight' => [
        'name' => [
            'en' => 'Bacterial Leaf Blight',
            'hi' => 'जीवाणु पत्ती झुलसा',
            'gu' => 'બેક્ટેરિયલ લીફ બ્લાઇટ'
        ],
        'symptoms' => [
            'en' => [
                'Water-soaked lesions on leaves',
                'Yellow to brown discoloration',
                'Wilting of leaves',
                'Dark brown to black streaks'
            ],
            'hi' => [
                'पत्तियों पर पानी से भरे घाव',
                'पीला से भूरा रंग परिवर्तन',
                'पत्तियों का मुरझाना',
                'गहरे भूरे से काले धब्बे'
            ]
        ],
        'treatments' => [
            'en' => [
                'Remove and destroy infected plants',
                'Apply copper-based bactericides',
                'Use disease-resistant varieties',
                'Improve field drainage',
                'Practice crop rotation'
            ],
            'hi' => [
                'संक्रमित पौधों को हटाएं और नष्ट करें',
                'कॉपर-आधारित बैक्टीरीसाइड का प्रयोग करें',
                'रोग प्रतिरोधी किस्मों का उपयोग करें',
                'खेत की जल निकासी में सुधार करें',
                'फसल चक्र का पालन करें'
            ]
        ],
        'preventive_measures' => [
            'en' => [
                'Use certified disease-free seeds',
                'Maintain proper plant spacing',
                'Avoid overhead irrigation',
                'Keep fields clean and weed-free',
                'Monitor crops regularly'
            ],
            'hi' => [
                'प्रमाणित रोग मुक्त बीजों का उपयोग करें',
                'उचित पौध दूरी बनाए रखें',
                'ऊपरी सिंचाई से बचें',
                'खेतों को साफ और खरपतवार मुक्त रखें',
                'नियमित रूप से फसलों की निगरानी करें'
            ]
        ],
        'severity_level' => 'high',
        'recovery_time' => '14-21 days',
        'organic_solutions' => [
            'en' => [
                'Neem oil spray',
                'Garlic extract solution',
                'Cow urine spray'
            ],
            'hi' => [
                'नीम तेल स्प्रे',
                'लहसुन अर्क घोल',
                'गोमूत्र स्प्रे'
            ]
        ]
    ],
    'leaf_blast' => [
        // Similar structure for other diseases...
    ]
];

// This is the only place where getDetailedRecommendations should be defined
function getDetailedRecommendations($disease, $lang = 'en') {
    global $disease_recommendations;
    
    if (!isset($disease_recommendations[$disease])) {
        return [
            'name' => ucfirst(str_replace('_', ' ', $disease)),
            'symptoms' => ['Please consult an agricultural expert'],
            'treatments' => ['Seek professional advice'],
            'preventive_measures' => ['Regular monitoring'],
            'severity_level' => 'unknown',
            'recovery_time' => 'varies',
            'organic_solutions' => ['Consult local agricultural experts']
        ];
    }
    
    $recommendations = $disease_recommendations[$disease];
    
    return [
        'name' => $recommendations['name'][$lang] ?? $recommendations['name']['en'],
        'symptoms' => $recommendations['symptoms'][$lang] ?? $recommendations['symptoms']['en'],
        'treatments' => $recommendations['treatments'][$lang] ?? $recommendations['treatments']['en'],
        'preventive_measures' => $recommendations['preventive_measures'][$lang] ?? $recommendations['preventive_measures']['en'],
        'severity_level' => $recommendations['severity_level'],
        'recovery_time' => $recommendations['recovery_time'],
        'organic_solutions' => $recommendations['organic_solutions'][$lang] ?? $recommendations['organic_solutions']['en']
    ];
}

// Update the AI recommendation function to use Gemini API
function getAIRecommendation($disease_info, $lang = 'en') {
    // API Configuration
    $GEMINI_API_KEY = 'AIzaSyDX1TctI8_ytT104FZnuFbTamPCbMOLtmk';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $GEMINI_API_KEY;

    // Prepare context for AI
    $context = "Based on the detected crop disease with following characteristics:\n";
    $context .= "Disease: " . $disease_info['name'] . "\n";
    $context .= "Severity: " . $disease_info['severity_level'] . "\n";
    $context .= "Symptoms: " . implode(", ", $disease_info['symptoms']) . "\n";

    // Prepare the prompt based on language
    $language = ($lang == 'hi') ? 'Hindi' : (($lang == 'gu') ? 'Gujarati' : 'English');
    $prompt = "You are an agricultural expert. Based on this crop disease information: $context " .
             "Provide a brief, 1-2 line personalized recommendation for treatment in $language language. " .
             "Focus on immediate actions the farmer should take.";

    // Prepare request data
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 100,
        ]
    ];

    // Setup cURL request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    // Execute request
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        error_log("Gemini API Error: " . $err);
        return "Unable to generate AI recommendation at this time.";
    }

    // Parse response
    $result = json_decode($response, true);
    
    // Extract the generated text from Gemini's response
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }

    return "Unable to generate AI recommendation. Please try again later.";
}
?> 