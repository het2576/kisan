<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['success' => false, 'error' => 'Not logged in']));
}

$user_id = $_SESSION['user_id'];

// Translation mapping for notifications
$translations = [
    'inventory' => [
        'en' => [
            'title' => 'Low Stock Alert',
            'message' => 'Your [ITEM] inventory is running low. Consider restocking soon.'
        ],
        'hi' => [
            'title' => 'स्टॉक अलर्ट',
            'message' => 'आपका [ITEM] स्टॉक कम हो रहा है। जल्द ही रीस्टॉक करने पर विचार करें।'
        ],
        'gu' => [
            'title' => 'સ્ટોક એલર્ટ',
            'message' => 'તમારો [ITEM] સ્ટોક ઓછો થઈ રહ્યો છે. જલ્દીથી રીસ્ટોક કરવાનું વિચારો.'
        ]
    ],
    'market' => [
        'en' => [
            'title' => 'Price Update',
            'message' => '[ITEM] prices have [CHANGE] by [PERCENT]% in your region.'
        ],
        'hi' => [
            'title' => 'मूल्य अपडेट',
            'message' => 'आपके क्षेत्र में [ITEM] के दाम में [PERCENT]% की [CHANGE] हुई है।'
        ],
        'gu' => [
            'title' => 'ભાવ અપડેટ',
            'message' => 'તમારા વિસ્તારમાં [ITEM] ના ભાવમાં [PERCENT]% નો [CHANGE] થયો છે.'
        ]
    ],
    'weather' => [
        'en' => [
            'title' => 'Weather Alert',
            'message' => '[CONDITION] expected in your region [TIME]. [ADVICE]'
        ],
        'hi' => [
            'title' => 'मौसम अलर्ट',
            'message' => 'आपके क्षेत्र में [TIME] [CONDITION] की संभावना है। [ADVICE]'
        ],
        'gu' => [
            'title' => 'હવામાન એલર્ટ',
            'message' => 'તમારા વિસ્તારમાં [TIME] [CONDITION] ની આગાહી છે. [ADVICE]'
        ]
    ]
];

// Helper function to insert notification in all languages
function insertMultilingualNotification($conn, $user_id, $type, $data, $translations, $reference_id = null) {
    $languages = ['en', 'hi', 'gu'];
    
    foreach ($languages as $lang) {
        $title = $translations[$type][$lang]['title'];
        $message = $translations[$type][$lang]['message'];
        
        // Replace placeholders with actual data
        foreach ($data as $key => $value) {
            $message = str_replace("[$key]", $value, $message);
        }
        
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, type, title, message, reference_id, language) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("isssss", $user_id, $type, $title, $message, $reference_id, $lang);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
    }
}

try {
    // Check for inventory updates
    $stmt = $conn->prepare("
        SELECT i.* 
        FROM Inventory i
        WHERE i.user_id = ? 
        AND i.quantity <= 10
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.reference_id = i.item_id 
            AND n.type = 'inventory'
            AND n.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        )
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    while ($item = $result->fetch_assoc()) {
        insertMultilingualNotification(
            $conn, 
            $user_id, 
            'inventory',
            ['ITEM' => $item['item_name']],
            $translations,
            $item['item_id']
        );
    }

    // Check for market price updates
    $stmt = $conn->prepare("
        SELECT mp.*, 
               CASE 
                   WHEN mp.price > mp_old.price THEN 'increased'
                   ELSE 'decreased'
               END as price_change,
               ABS((mp.price - mp_old.price) / mp_old.price * 100) as change_percent
        FROM MarketPrices mp
        JOIN MarketPrices mp_old ON mp.crop_name = mp_old.crop_name
        WHERE mp.date = CURRENT_DATE
        AND mp_old.date = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
        AND ABS((mp.price - mp_old.price) / mp_old.price * 100) >= 5
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.reference_id = mp.price_id 
            AND n.type = 'market'
            AND n.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        )
    ");
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    while ($price = $result->fetch_assoc()) {
        $changeWords = [
            'en' => $price['price_change'],
            'hi' => ($price['price_change'] == 'increased' ? 'वृद्धि' : 'कमी'),
            'gu' => ($price['price_change'] == 'increased' ? 'વધારો' : 'ઘટાડો')
        ];
        
        insertMultilingualNotification(
            $conn,
            $user_id,
            'market',
            [
                'ITEM' => $price['crop_name'],
                'CHANGE' => $changeWords['en'],
                'PERCENT' => number_format($price['change_percent'], 1)
            ],
            $translations,
            $price['price_id']
        );
    }

    // Check for weather alerts
    $stmt = $conn->prepare("
        SELECT w.* 
        FROM WeatherData w
        JOIN Users u ON u.region = w.region
        WHERE u.user_id = ?
        AND w.forecast_date = CURRENT_DATE
        AND (w.rainfall > 50 OR w.temperature > 40 OR w.temperature < 5)
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.reference_id = w.weather_id 
            AND n.type = 'weather'
            AND n.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        )
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    while ($weather = $result->fetch_assoc()) {
        $condition = '';
        $advice = '';
        
        if ($weather['rainfall'] > 50) {
            $condition = [
                'en' => 'Heavy rainfall',
                'hi' => 'भारी वर्षा',
                'gu' => 'ભારે વરસાદ'
            ];
            $advice = [
                'en' => 'Please secure your crops.',
                'hi' => 'कृपया अपनी फसलों को सुरक्षित करें।',
                'gu' => 'કૃપા કરીને તમારા પાકને સુરક્ષિત કરો.'
            ];
        } elseif ($weather['temperature'] > 40) {
            $condition = [
                'en' => 'Extreme heat',
                'hi' => 'भीषण गर्मी',
                'gu' => 'ભારે ગરમી'
            ];
            $advice = [
                'en' => 'Ensure proper irrigation.',
                'hi' => 'उचित सिंचाई सुनिश्चित करें।',
                'gu' => 'યોગ્ય સિંચાઈની ખાતરી કરો.'
            ];
        } elseif ($weather['temperature'] < 5) {
            $condition = [
                'en' => 'Cold wave',
                'hi' => 'शीत लहर',
                'gu' => 'ઠંડી લહેર'
            ];
            $advice = [
                'en' => 'Protect crops from frost.',
                'hi' => 'फसलों को पाले से बचाएं।',
                'gu' => 'પાકને ઠંડીથી બચાવો.'
            ];
        }
        
        insertMultilingualNotification(
            $conn,
            $user_id,
            'weather',
            [
                'CONDITION' => $condition['en'],
                'TIME' => 'today',
                'ADVICE' => $advice['en']
            ],
            $translations,
            $weather['weather_id']
        );
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Check Notifications Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?> 