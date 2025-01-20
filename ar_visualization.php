<?php
session_start();

// Get language from session or default to English
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations for coming soon text
$translations = [
    'en' => [
        'coming_soon' => 'Coming Soon',
        'description' => 'We are working on an exciting AR visualization feature that will help you better understand and manage your farm.',
        'notify' => 'Stay tuned for updates!'
    ],
    'hi' => [
        'coming_soon' => 'जल्द आ रहा है',
        'description' => 'हम एक रोमांचक एआर विज़ुअलाइज़ेशन फीचर पर काम कर रहे हैं जो आपको अपने खेत को बेहतर ढंग से समझने और प्रबंधित करने में मदद करेगा।',
        'notify' => 'अपडेट के लिए बने रहें!'
    ],
    'gu' => [
        'coming_soon' => 'ટૂંક સમયમાં આવી રહ્યું છે',
        'description' => 'અમે એક રોમાંચક AR વિઝ્યુઅલાઇઝેશન ફીચર પર કામ કરી રહ્યા છીએ જે તમને તમારા ખેતરને વધુ સારી રીતે સમજવામાં અને સંચાલિત કરવામાં મદદ કરશે.',
        'notify' => 'અપડેટ્સ માટે જોડાયેલા રહો!'
    ]
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR Visualization - Coming Soon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .coming-soon-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .coming-soon-icon {
            font-size: 5rem;
            color: #3498db;
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .coming-soon-title {
            font-size: 3.5rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .coming-soon-description {
            font-size: 1.2rem;
            color: #7f8c8d;
            max-width: 600px;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .coming-soon-notify {
            font-size: 1.1rem;
            color: #3498db;
            font-weight: 500;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="coming-soon-container">
        <div class="coming-soon-icon">
            <i class="fas fa-vr-cardboard"></i>
        </div>
        <h1 class="coming-soon-title"><?php echo $translations[$lang]['coming_soon']; ?></h1>
        <p class="coming-soon-description"><?php echo $translations[$lang]['description']; ?></p>
        <p class="coming-soon-notify"><?php echo $translations[$lang]['notify']; ?></p>
    </div>
</body>
</html>
