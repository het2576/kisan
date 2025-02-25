<?php
require_once __DIR__ . '/includes/init.php';

// Get user role from session
$userRole = getCurrentUserRole() ?? 'buyer';

// Set language based on selection or session
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
} else if (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = 'en';
    $_SESSION['lang'] = $lang;
}

$translations = [
    'en' => [
        'welcome' => 'Welcome',
        'inventory' => 'Inventory',
        'market' => 'Market Insights', 
        'weather' => 'Weather',
        'tools' => 'Tools',
        'ai' => 'AI Assistant',
        'logout' => 'Logout',
        'profit_calc' => 'Profit Calculator',
        'calculate_profit' => 'Calculate Profit',
        'crop_name' => 'Crop Name',
        'land_size' => 'Land Size (acres)',
        'market_price' => 'Market Price (₹/kg)',
        'cost_per_acre' => 'Cost per Acre (₹)',
        'yield_per_acre' => 'Yield per Acre (kg)',
        'dashboard' => 'Dashboard',
        'smart_companion' => 'Your Smart Farming Companion',
        'hero_subtitle' => 'Access modern farming tools, market insights, and AI-powered recommendations all in one place',
        'about_title' => 'About Kisan.ai',
        'about_subtitle' => 'Empowering farmers with technology for better farming decisions',
        'how_to_use' => 'How to Use Kisan.ai',
        'how_to_subtitle' => 'Follow these simple steps to make the most of our platform',
        'step1_title' => 'Complete Your Profile',
        'step1_desc' => 'Add your farm details, crop preferences, and location information for personalized recommendations.',
        'step2_title' => 'Explore Features', 
        'step2_desc' => 'Navigate through different sections using the sidebar menu to access various tools and insights.',
        'step3_title' => 'Get AI Assistance',
        'step3_desc' => 'Use our AI assistant for crop-specific advice, pest management, and farming best practices.',
        'step4_title' => 'Track Progress',
        'step4_desc' => 'Monitor your farm\'s performance, inventory, and profits using our tracking tools.',
        'ai_insights_title' => 'AI-Powered Insights',
        'ai_insights_desc' => 'Get personalized recommendations for crop management, pest control, and optimal farming practices.',
        'market_intel_title' => 'Market Intelligence',
        'market_intel_desc' => 'Access real-time market prices, trends, and demand forecasts to maximize your profits.',
        'weather_forecast_title' => 'Weather Forecasting',
        'weather_forecast_desc' => 'Stay ahead with accurate weather predictions and plan your farming activities accordingly.',
        'news' => 'Agricultural News',
        'disease_detection' => 'Crop Disease Detection',
        'disease_detection_desc' => 'Upload crop images to detect diseases and get treatment recommendations',
        'marketplace'      => 'Marketplace',
        'edit_profile' => 'Edit Profile',
        'view_profile' => 'View Profile',
        'profile_settings' => 'Profile Settings'
    ],
    'hi' => [
        'welcome' => 'स्वागत है',
        'inventory' => 'इन्वेंटरी',
        'market' => 'बाजार अंतर्दृष्टि',
        'weather' => 'मौसम',
        'tools' => 'उपकरण',
        'ai' => 'एआई सहायक',
        'logout' => 'लॉग आउट',
        'profit_calc' => 'लाभ कैलकुलेटर',
        'calculate_profit' => 'लाभ की गणना करें',
        'crop_name' => 'फसल का नाम',
        'land_size' => 'भूमि का आकार (एकड़)',
        'market_price' => 'बाजार मूल्य (₹/किग्रा)',
        'cost_per_acre' => 'प्रति एकड़ लागत (₹)',
        'yield_per_acre' => 'प्रति एकड़ उपज (किग्रा)',
        'dashboard' => 'डैशबोर्ड',
        'smart_companion' => 'आपका स्मार्ट कृषि साथी',
        'hero_subtitle' => 'एक ही स्थान पर आधुनिक कृषि उपकरण, बाजार अंतर्दृष्टि और एआई-संचालित सिफारिशें प्राप्त करें',
        'about_title' => 'किसान.एआई के बारे में',
        'about_subtitle' => 'बेहतर कृषि निर्णयों के लिए किसानों को प्रौद्योगिकी से सशक्त बनाना',
        'how_to_use' => 'किसान.एआई का उपयोग कैसे करें',
        'how_to_subtitle' => 'हमारे प्लेटफॉर्म का अधिकतम लाभ उठाने के लिए इन सरल चरणों का पालन करें',
        'step1_title' => 'अपनी प्रोफ़ाइल पूरी करें',
        'step1_desc' => 'व्यक्तिगत सिफारिशों के लिए अपने खेत का विवरण, फसल प्राथमिकताएं और स्थान जानकारी जोड़ें।',
        'step2_title' => 'सुविधाएं एक्सप्लोर करें',
        'step2_desc' => 'विभिन्न टूल्स और इनसाइट्स तक पहुंचने के लिए साइडबार मेनू का उपयोग करें।',
        'step3_title' => 'एआई सहायता प्राप्त करें',
        'step3_desc' => 'फसल विशिष्ट सलाह, कीट प्रबंधन और कृषि सर्वोत्तम प्रथाओं के लिए हमारे एआई सहायक का उपयोग करें।',
        'step4_title' => 'प्रगति की निगरानी करें',
        'step4_desc' => 'हमारे ट्रैकिंग टूल्स का उपयोग करके अपने खेत के प्रदर्शन, इन्वेंट्री और लाभ की निगरानी करें।',
        'ai_insights_title' => 'एआई-संचालित अंतर्दृष्टि',
        'ai_insights_desc' => 'फसल प्रबंधन, कीट नियंत्रण और इष्टतम कृषि प्रथाओं के लिए व्यक्तिगत सिफारिशें प्राप्त करें।',
        'market_intel_title' => 'बाजार बुद्धिमत्ता',
        'market_intel_desc' => 'अपने लाभ को अधिकतम करने के लिए वास्तविक समय के बाजार मूल्य, रुझान और मांग पूर्वानुमान तक पहुंच प्राप्त करें।',
        'weather_forecast_title' => 'मौसम पूर्वानुमान',
        'weather_forecast_desc' => 'सटीक मौसम भविष्यवाणियों के साथ आगे रहें और तदनुसार अपनी कृषि गतिविधियों की योजना बनाएं।',
        'news' => 'कृषि समाचार',
        'disease_detection' => 'फसल रोग पहचान',
        'disease_detection_desc' => 'रोगों का पता लगाने और उपचार की सिफारिशें प्राप्त करने के लिए फसल की छवियां अपलोड करें',
        'marketplace'      => 'मार्केटप्लेस',
        'edit_profile' => 'प्रोफ़ाइल संपादित करें',
        'view_profile' => 'प्रोफ़ाइल देखें',
        'profile_settings' => 'प्रोफ़ाइल सेटिंग्स'
    ],
    'gu' => [
        'welcome' => 'સ્વાગત છે',
        'inventory' => 'ઇન્વેન્ટરી',
        'market' => 'બજાર માહિતી',
        'weather' => 'હવામાન',
        'tools' => 'સાધનો',
        'ai' => 'AI સહાયક',
        'logout' => 'લૉગ આઉટ',
        'profit_calc' => 'નફો કેલ્ક્યુલેટર',
        'calculate_profit' => 'નફો ગણો',
        'crop_name' => 'પાક નામ',
        'land_size' => 'જમીન કદ (એકર)',
        'market_price' => 'બજાર ભાવ (₹/કિગ્રા)',
        'cost_per_acre' => 'એકર દીઠ ખર્ચ (₹)',
        'yield_per_acre' => 'એકર દીઠ ઉપજ (કિગ્રા)',
        'dashboard' => 'ડેશબોર્ડ',
        'smart_companion' => 'તમારો સ્માર્ટ ખેતી સાથી',
        'hero_subtitle' => 'એક જ સ્થળે આધુનિક ખેતી સાધનો, બજાર માહિતી અને AI-આધારિત ભલામણો મેળવો',
        'about_title' => 'કિસાન.એઆઈ વિશે',
        'about_subtitle' => 'સારા ખેતી નિર્ણયો માટે ખેડૂતોને ટેકનોલોજી સાથે સશક્ત બનાવવા',
        'how_to_use' => 'કિસાન.એઆઈનો ઉપયોગ કેવી રીતે કરવો',
        'how_to_subtitle' => 'અમારા પ્લેટફોર્મનો મહત્તમ લાભ લેવા માટે આ સરળ પગલાંઓને અનુસરો',
        'step1_title' => 'તમારી પ્રોફાઇલ પૂર્ણ કરો',
        'step1_desc' => 'વ્યક્તિગત ભલામણો માટે તમારી ખેતરની વિગતો, પાક પસંદગીઓ અને સ્થાન માહિતી ઉમેરો.',
        'step2_title' => 'સુવિધાઓ એક્સપ્લોર કરો',
        'step2_desc' => 'વિવિધ ટૂલ્સ અને માહિતી મેળવવા માટે સાઇડબાર મેનૂનો ઉપયોગ કરો.',
        'step3_title' => 'AI સહાય મેળવો',
        'step3_desc' => 'પાક વિશિષ્ટ સલાહ, જીવાત વ્યવસ્થાપન અને ખેતીની શ્રેષ્ઠ પદ્ધતિઓ માટે અમારા AI સહાયકનો ઉપયોગ કરો.',
        'step4_title' => 'પ્રગતિ ટ્રૅક કરો',
        'step4_desc' => 'અમારા ટ્રેકિંગ ટૂલ્સનો ઉપયોગ કરીને તમારા ખેતરની કામગીરી, ઇન્વેન્ટરી અને નફાને મોનિટર કરો.',
        'ai_insights_title' => 'AI-આધારિત અંતર્દૃષ્ટિ',
        'ai_insights_desc' => 'પાક વ્યવસ્થાપન, જીવાત નિયંત્રણ અને શ્રેષ્ઠ ખેતી પદ્ધતિઓ માટે વ્યક્તિગત ભલામણો મેળવો.',
        'market_intel_title' => 'બજાર બુદ્ધિમત્તા',
        'market_intel_desc' => 'તમારા નફાને મહત્તમ કરવા માટે રીયલ-ટાઇમ બજાર ભાવ, વલણો અને માંગ આગાહીઓની માહિતી મેળવો.',
        'weather_forecast_title' => 'હવામાન આગાહી',
        'weather_forecast_desc' => 'ચોક્કસ હવામાન આગાહીઓ સાથે આગળ રહો અને તે મુજબ તમારી ખેતી પ્રવૃત્તિઓનું આયોજન કરો.',
        'news' => 'કૃષિ સમાચાર',
        'disease_detection' => 'પાક રોગ શોધ',
        'disease_detection_desc' => 'રોગોનું નિદાન કરવા અને સારવારની ભલામણો મેળવવા માટે પાકની છબીઓ અપલોડ કરો',
        'marketplace'      => 'માર્કેટપ્લેસ',
        'edit_profile' => 'પ્રોફાઇલ સંપાદિત કરો',
        'view_profile' => 'પ્રોફાઇલ જુઓ',
        'profile_settings' => 'પ્રોફાઇલ સેટિંગ્સ'
    ]
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2F855A;
            --secondary-color: #276749;
            --accent-color: #E6FFFA;
            --text-color: #2D3748;
            --border-color: #E2E8F0;
            --error-color: #E53E3E;
        }

        body {
            background: #f8f9fa;
            color: #2c3e50;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background: #1a1c23;
            color: #ffffff;
            padding: 1rem;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 100vh;
            font-size: 0.9rem;
        }

        .sidebar-logo {
            padding: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-logo h3 {
            color: #ffffff;
            font-weight: 600;
            margin: 0;
            font-size: 1.3rem;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            flex: 1;
            padding-bottom: 0.5rem;
            overflow-y: auto;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.6rem 0.8rem;
            margin: 0.1rem 0;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
            font-size: 0.8rem;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: #3182ce;
            color: #ffffff;
        }

        .nav-link i {
            margin-right: 8px;
            width: 16px;
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }

        .nav-link.dropdown-toggle {
            text-align: left;
            padding-left: 0.8rem;
        }

        .nav-link.dropdown-toggle i:last-child {
            margin-left: auto;
        }

        .nav-link.dropdown-toggle[aria-expanded="true"] i:last-child {
            transform: rotate(180deg);
        }

        .logout-container {
            margin-top: auto;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .logout-link {
            background: rgba(255,59,48,0.1);
            color: #ff3b30;
            width: 100%;
            margin: 0;
            font-size: 0.8rem;
            padding: 0.6rem 0.8rem;
        }

        .logout-link:hover {
            background: rgba(255,59,48,0.2);
            color: #ff3b30;
        }

        /* Header Styles */
        .main-header {
            position: fixed;
            top: 0;
            right: 0;
            left: 280px;
            height: 70px;
            background: white;
            padding: 0.8rem 2rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            z-index: 900;
            font-size: 0.9rem;
        }

        .hamburger-menu {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .hamburger-menu.active {
            transform: rotate(90deg);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: #3182ce;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }
         /* Main Content Area */
         .main-content {
            margin-left: 280px;
            padding: 80px 1.5rem 1.5rem;
        }

        .hero-section {
            background: linear-gradient(135deg, #3498db, #2ecc71);
            color: white;
            padding: 3rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('images/farm-pattern.png') repeat;
            opacity: 0.1;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .hero-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-icons {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
        }

        .hero-icon {
            background: rgba(255,255,255,0.2);
            padding: 1rem;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .hero-icon:hover {
            transform: scale(1.1);
        }

        .hero-icon i {
            font-size: 2rem;
        }

        .features-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .feature-card {
            padding: 1.5rem;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2rem;
            color: #3498db;
            margin-bottom: 1rem;
            background: rgba(52,152,219,0.1);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .guide-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
        }

        .step-card {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            transition: transform 0.3s ease;
        }

        .step-card:hover {
            transform: translateX(10px);
        }

        .step-number {
            background: #3498db;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .section-title {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Dropdown styles to match other links */
        .nav-links .dropdown-toggle {
            color: rgba(255,255,255,0.8);
            padding: 0.6rem 0.8rem;
            margin: 0.1rem 0;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
            font-size: 0.8rem;
            width: 100%;
        }

        .nav-links .dropdown-toggle:hover {
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            transform: translateX(5px);
        }

        .nav-links .dropdown-toggle i:first-child {
            margin-right: 8px;
            width: 16px;
            font-size: 0.9rem;
        }

        .nav-links .dropdown-toggle i.fa-chevron-down {
            margin-left: auto;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .nav-links .dropdown.show .fa-chevron-down {
            transform: rotate(180deg);
        }

        /* Dropdown menu styles */
        .nav-links .dropdown-menu {
            background: transparent;
            border: none;
            padding: 0;
            margin: 0.1rem 0;
            width: 100%;
        }

        .nav-links .dropdown-item {
            color: rgba(255,255,255,0.8);
            padding: 0.6rem 0.8rem 0.6rem 2.3rem;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .nav-links .dropdown-item:hover {
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            transform: translateX(5px);
        }

        .nav-links .dropdown-item i {
            margin-right: 8px;
            width: 16px;
            font-size: 0.9rem;
        }

        .nav-links .dropdown-item.active {
            background: #3182ce;
            color: #ffffff;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .nav-links .dropdown-toggle,
            .nav-links .dropdown-item {
                padding-left: 1.2rem;
            }
            
            .nav-links .dropdown-item {
                padding-left: 2.7rem;
            }
        }

        @media (max-width: 768px) {
            .hamburger-menu {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 100%;
                max-width: 250px;
                padding: 0.8rem;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-header {
                left: 0;
                padding: 0.8rem 1rem;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .mobile-menu-overlay.active {
                display: block;
                opacity: 1;
            }
        }

        @media (min-width: 1200px) {
            .main-header {
                padding: 0.8rem 3rem;
            }
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-left: auto;
            position: relative;
        }

        .notification-wrapper {
            position: relative;
            margin-right: 1rem;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 0.5rem;
            font-size: 1.25rem;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .notification-bell:hover {
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--error-color);
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .notification-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 380px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: none;
            z-index: 1000;
            border: 1px solid var(--border-color);
            max-height: 500px;
            overflow-y: auto;
        }

        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
        }

        .notification-header h6 {
            margin: 0;
            font-weight: 600;
            color: var(--text-color);
        }

        .mark-all-read {
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 0.875rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .mark-all-read:hover {
            background: var(--accent-color);
        }

        .notification-list {
            padding: 0.5rem;
        }

        .notification-item {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            background: white;
        }

        .notification-item:hover {
            background: var(--accent-color);
            border-color: var(--border-color);
        }

        .notification-item.unread {
            background: var(--accent-color);
        }

        .notification-item .title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--text-color);
        }

        .notification-item .message {
            font-size: 0.875rem;
            color: var(--text-color);
            opacity: 0.8;
        }

        .notification-item .time {
            font-size: 0.75rem;
            color: var(--text-color);
            opacity: 0.6;
            margin-top: 0.5rem;
        }

        .notification-item i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .notification-dropdown {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                width: auto;
                margin: 0 10px;
                max-height: calc(100vh - 80px);
            }

            .header-right {
                gap: 0.8rem;
            }

            .lang-selector {
                display: flex;
                gap: 0.3rem;
            }

            .lang-selector .btn {
                padding: 0.3rem 0.5rem;
                font-size: 0.75rem;
            }

            .user-profile span {
                display: none;
            }
        }

        /* Profile Dropdown Styles */
        .profile-dropdown {
            min-width: 280px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }

        .user-profile {
            position: relative;
        }

        .profile-info {
            background: #f8fafb;
            padding: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
        }

        .info-item i {
            width: 24px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
            color: #4CAF50;
        }

        .info-item span {
            font-size: 0.9rem;
            color: #2d3748;
            font-weight: 500;
            word-break: break-word;
        }

        .dropdown-item {
            padding: 12px 15px;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: #f8fafb;
            color: #4CAF50;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .profile-dropdown {
                position: fixed !important;
                top: 60px !important;
                right: 10px !important;
                left: 10px !important;
                width: auto !important;
                transform: none !important;
                margin: 0 !important;
                max-height: calc(100vh - 70px);
                overflow-y: auto;
                border-radius: 8px !important;
            }

            .user-profile {
                position: static !important;
            }

            .dropdown-menu.show {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                transform: none !important;
            }

            .profile-header {
                padding: 15px !important;
            }

            .info-item {
                padding: 10px 0;
            }

            .info-item span {
                font-size: 0.9rem;
                line-height: 1.4;
            }

            /* Add backdrop overlay */
            .dropdown-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9998;
            }

            /* Ensure header stays above dropdown */
            .main-header {
                position: relative;
                z-index: 10000;
            }
        }

        /* Small screen styles */
        @media (max-width: 576px) {
            .profile-dropdown {
                top: 50px !important;
            }

            .profile-info {
                padding: 12px;
            }

            .info-item {
                padding: 8px 0;
            }

            .dropdown-item {
                padding: 10px 12px;
            }
        }

        /* Sidebar dropdown styles */
        .nav-links .dropdown {
            width: 100%;
        }

        .nav-links .dropdown-menu {
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            width: 100%;
            position: static !important;
            transform: none !important;
            box-shadow: none;
            padding-left: 20px;
        }

        .nav-links .dropdown-toggle {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 12px 20px;
            color: #f1f5f9;
            text-decoration: none;
            gap: 12px;
        }

        .nav-links .dropdown-toggle:hover,
        .nav-links .dropdown-toggle:focus {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .nav-links .dropdown-toggle i:first-child {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        .nav-links .dropdown-toggle i.fa-chevron-down {
            margin-left: auto;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .nav-links .dropdown.show .fa-chevron-down {
            transform: rotate(180deg);
        }

        .nav-links .dropdown-item {
            padding: 10px 15px 10px 52px;
            color: #f1f5f9;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links .dropdown-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            color: inherit;
        }

        .nav-links .dropdown-item:hover,
        .nav-links .dropdown-item:focus {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* Active state styles */
        .nav-links .dropdown-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .nav-links .dropdown-toggle.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            .nav-links .dropdown-toggle {
                padding: 12px 25px;
            }
            
            .nav-links .dropdown-item {
                padding: 10px 15px 10px 57px;
            }
        }

        .external-link-icon {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .nav-link:hover .external-link-icon {
            opacity: 1;
        }
    </style>
    <script>
    // Check for notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkNotifications();
    });

    // Function to check notifications
    function checkNotifications() {
        fetch('check_notifications.php')
            .then(response => {
                // Notification check complete
                console.log('Notifications checked');
            });
    }
    </script>
</head>
<body>
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar">
    <div class="sidebar-logo">
        <h3>Kisan.ai</h3>
    </div>
    <div class="nav-links">
        <!-- Dashboard -->
        <a href="dashboard.php" class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span><?php echo $translations[$lang]['dashboard']; ?></span>
        </a>

        <!-- Marketplace Dropdown -->
        <div class="dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-store"></i>
                <span><?php echo $translations[$lang]['marketplace']; ?></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="marketplace.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Buy/Sell Products</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="https://kisan-bid.netlify.app/" target="_blank">
                        <i class="fas fa-gavel"></i>
                        <span>Agricultural Auctions</span>
                        <i class="fas fa-external-link-alt ms-auto external-link-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Tools Dropdown -->
        <div class="dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-tools"></i>
                <span><?php echo $translations[$lang]['tools']; ?></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="inventory.php">
                        <i class="fas fa-box"></i>
                        <span><?php echo $translations[$lang]['inventory']; ?></span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="crop_profit_calc.php">
                        <i class="fas fa-calculator"></i>
                        <span><?php echo $translations[$lang]['profit_calc']; ?></span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="crop_disease_detection.php">
                        <i class="fas fa-microscope"></i>
                        <span><?php echo $translations[$lang]['disease_detection']; ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Market Insights -->
        <a href="market.php" class="nav-link <?php echo ($page == 'market') ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i>
            <span><?php echo $translations[$lang]['market']; ?></span>
        </a>

        <!-- Weather -->
        <a href="weather.php" class="nav-link <?php echo ($page == 'weather') ? 'active' : ''; ?>">
            <i class="fas fa-cloud-sun"></i>
            <span><?php echo $translations[$lang]['weather']; ?></span>
        </a>

        <!-- AI Assistant -->
        <a href="ai_assistant.php" class="nav-link <?php echo ($page == 'ai') ? 'active' : ''; ?>">
            <i class="fas fa-robot"></i>
            <span><?php echo $translations[$lang]['ai']; ?></span>
        </a>

        <!-- News -->
        <a href="agri_news.php" class="nav-link <?php echo ($page == 'news') ? 'active' : ''; ?>">
            <i class="fas fa-newspaper"></i>
            <span><?php echo $translations[$lang]['news']; ?></span>
        </a>
    </div>
    <div class="logout-container">
        <a href="logout.php" class="nav-link logout-link">
            <i class="fas fa-sign-out-alt"></i><?php echo $translations[$lang]['logout']; ?>
        </a>
    </div>
</nav>

    <!-- Header -->
    <header class="main-header">
        <div class="hamburger-menu">
            <i class="fas fa-bars"></i>
        </div>
        <div class="header-right">
            <div class="notification-wrapper">
                <div class="notification-bell" id="notificationBell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationCount"></span>
                </div>
                
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h6>Notifications</h6>
                        <button class="mark-all-read">Mark all as read</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <!-- Notifications will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="lang-selector">
                <a href="?lang=en" class="btn btn-outline-primary btn-sm">English</a>
                <a href="?lang=hi" class="btn btn-outline-primary btn-sm">हिंदी</a>
                <a href="?lang=gu" class="btn btn-outline-primary btn-sm">ગુજરાતી</a>
            </div>
            <div class="user-profile dropdown">
                <div class="d-flex align-items-center" role="button" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        <?php echo substr($_SESSION['name'], 0, 1); ?>
                    </div>
                    <span class="ms-2 d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <i class="fas fa-chevron-down ms-2"></i>
                </div>
                
                <div class="dropdown-menu profile-dropdown">
                    <div class="profile-header p-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar-large">
                                <?php echo substr($_SESSION['name'], 0, 1); ?>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 text-dark"><?php echo htmlspecialchars($_SESSION['name']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($_SESSION['email']); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-info border-bottom">
                        <?php if (!empty($_SESSION['phone'])): ?>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span><?php echo htmlspecialchars($_SESSION['phone']); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['region'])): ?>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($_SESSION['region']); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['farming_type'])): ?>
                        <div class="info-item">
                            <i class="fas fa-seedling"></i>
                            <span><?php echo htmlspecialchars($_SESSION['farming_type']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <a class="dropdown-item" href="profile.php">
                        <i class="fas fa-user"></i>
                        <span>Edit Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <h1><?php echo $translations[$lang]['welcome']; ?>, <?php echo $_SESSION['name']; ?>!</h1>
            <p><?php echo $translations[$lang]['smart_companion']; ?></p>
            <p><?php echo $translations[$lang]['hero_subtitle']; ?></p>
            <div class="hero-icons">
                <div class="hero-icon">
                    <i class="fas fa-tractor"></i>
                </div>
                <div class="hero-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="hero-icon">
                    <i class="fas fa-sun"></i>
                </div>
                <div class="hero-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="features-section">
            <h2 class="section-title"><?php echo $translations[$lang]['about_title']; ?></h2>
            <p class="section-subtitle"><?php echo $translations[$lang]['about_subtitle']; ?></p>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3><?php echo $translations[$lang]['ai_insights_title']; ?></h3>
                        <p><?php echo $translations[$lang]['ai_insights_desc']; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3><?php echo $translations[$lang]['market_intel_title']; ?></h3>
                        <p><?php echo $translations[$lang]['market_intel_desc']; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-cloud-sun"></i>
                        </div>
                        <h3><?php echo $translations[$lang]['weather_forecast_title']; ?></h3>
                        <p><?php echo $translations[$lang]['weather_forecast_desc']; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Guide Section -->
        <section class="guide-section">
            <h2 class="section-title"><?php echo $translations[$lang]['how_to_use']; ?></h2>
            <p class="section-subtitle"><?php echo $translations[$lang]['how_to_subtitle']; ?></p>

            <div class="steps-container">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4><?php echo $translations[$lang]['step1_title']; ?></h4>
                        <p><?php echo $translations[$lang]['step1_desc']; ?></p>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4><?php echo $translations[$lang]['step2_title']; ?></h4>
                        <p><?php echo $translations[$lang]['step2_desc']; ?></p>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4><?php echo $translations[$lang]['step3_title']; ?></h4>
                        <p><?php echo $translations[$lang]['step3_desc']; ?></p>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4><?php echo $translations[$lang]['step4_title']; ?></h4>
                        <p><?php echo $translations[$lang]['step4_desc']; ?></p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Hamburger Menu Script -->
    <script>
        document.querySelector('.hamburger-menu').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.mobile-menu-overlay').classList.toggle('active');
        });

        document.querySelector('.mobile-menu-overlay').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
            document.querySelector('.mobile-menu-overlay').classList.remove('active');
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bell = document.getElementById('notificationBell');
        const dropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');
        const countBadge = document.getElementById('notificationCount');
        const markAllReadBtn = document.querySelector('.mark-all-read');
        let isDropdownOpen = false;

        // Function to handle fetch errors
        async function handleFetchResponse(response) {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }
            return await response.text();
        }

        // Function to update notification count
        async function updateNotificationCount() {
            try {
                const response = await fetch('notifications.php?action=get_count');
                const data = await handleFetchResponse(response);
                
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                
                const count = parseInt(data.count) || 0;
                countBadge.textContent = count;
                countBadge.style.display = count > 0 ? 'flex' : 'none';
                
                if (count > 0) {
                    bell.classList.add('animate__animated', 'animate__headShake');
                }
            } catch (error) {
                console.error('Error updating count:', error);
                countBadge.style.display = 'none';
            }
        }

        // Function to load notifications
        async function loadNotifications() {
            try {
                notificationList.innerHTML = '<div class="notification-item"><div class="message">Loading...</div></div>';
                
                const response = await fetch('notifications.php?action=get_notifications');
                const data = await handleFetchResponse(response);
                
                if (data.error) {
                    throw new Error(data.error);
                }

                const notifications = data.notifications || [];
                const translations = data.translations || {
                    no_notifications: 'No notifications',
                    mark_all_read: 'Mark all as read',
                    notifications: 'Notifications'
                };

                if (notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="notification-item">
                            <div class="message">${translations.no_notifications}</div>
                        </div>
                    `;
                    return;
                }

                notificationList.innerHTML = notifications.map(notification => `
                    <div class="notification-item ${!notification.is_read ? 'unread' : ''}" 
                         data-id="${notification.id}">
                        <i class="fas ${notification.icon}"></i>
                        <div class="title">${notification.title}</div>
                        <div class="message">${notification.message}</div>
                        <div class="time">${timeAgo(notification.created_at)}</div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = `
                    <div class="notification-item">
                        <div class="message">Error loading notifications</div>
                    </div>
                `;
            }
        }

        // Function to format time
        function timeAgo(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " years ago";
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " months ago";
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " days ago";
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " hours ago";
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " minutes ago";
            return Math.floor(seconds) + " seconds ago";
        }

        // Toggle dropdown
        bell.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            isDropdownOpen = !isDropdownOpen;
            dropdown.style.display = isDropdownOpen ? 'block' : 'none';
            
            if (isDropdownOpen) {
                await loadNotifications();
                bell.classList.remove('animate__animated', 'animate__headShake');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                isDropdownOpen = false;
                dropdown.style.display = 'none';
            }
        });

        // Mark notification as read
        notificationList.addEventListener('click', async (e) => {
            const item = e.target.closest('.notification-item');
            if (item && item.classList.contains('unread')) {
                try {
                    const notificationId = item.dataset.id;
                    const formData = new FormData();
                    formData.append('notification_id', notificationId);

                    const response = await fetch('notifications.php?action=mark_read', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await handleFetchResponse(response);
                    
                    if (data.success) {
                        item.classList.remove('unread');
                        await updateNotificationCount();
                    }
                } catch (error) {
                    console.error('Error marking as read:', error);
                }
            }
        });

        // Mark all as read
        markAllReadBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                const response = await fetch('notifications.php?action=mark_all_read');
                const data = await handleFetchResponse(response);
                
                if (data.success) {
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    await updateNotificationCount();
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        });

        // Initial check for notifications
        async function checkNotifications() {
            try {
                const response = await fetch('check_notifications.php');
                const data = await handleFetchResponse(response);
                
                if (data.success) {
                    await updateNotificationCount();
                    if (isDropdownOpen) {
                        await loadNotifications();
                    }
                }
            } catch (error) {
                console.error('Error checking notifications:', error);
            }
        }

        // Initial load
        updateNotificationCount();
        checkNotifications();
        
        // Check for new notifications every 30 seconds
        setInterval(checkNotifications, 30000);
    });
    </script>

    <!-- Add this before closing body tag -->
    <script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Handle Edit Button Click
        $('.edit-product').click(function() {
            var productId = $(this).data('id');
            $('#product_id').val(productId);
            $('.modal-title').text('Edit Product Listing');
            
            // Fetch product data
            $.get('get_product.php', {product_id: productId}, function(product) {
                // Populate form fields
                $('[name="name"]').val(product.name);
                $('[name="category_id"]').val(product.category_id);
                $('[name="description"]').val(product.description);
                $('[name="price_per_kg"]').val(product.price_per_kg);
                $('[name="quantity_available"]').val(product.quantity_available);
                $('[name="unit"]').val(product.unit);
                $('[name="harvest_date"]').val(product.harvest_date);
                $('[name="expiry_date"]').val(product.expiry_date);
                $('[name="farming_method"]').val(product.farming_method);
                $('[name="location"]').val(product.location);
                $('[name="is_organic"]').prop('checked', product.is_organic == 1);
                $('[name="status"]').val(product.status);
                $('[name="min_order_quantity"]').val(product.min_order_quantity);
                
                // Show existing images if any
                if (product.images) {
                    // Display existing images logic here
                }
                
                $('#addProductModal').modal('show');
            });
        });

        // Handle Delete Button Click
        $('.delete-product').click(function() {
            if(confirm('Are you sure you want to delete this product listing?')) {
                var productId = $(this).data('id');
                $.post('delete_product.php', {product_id: productId}, function(response) {
                    if(response.success) {
                        location.reload();
                    } else {
                        alert('Error deleting product');
                    }
                });
            }
        });

        // Handle Promote Button Click
        $('.promote-product').click(function() {
            var productId = $(this).data('id');
            // Add promotion logic here
            alert('Promotion feature coming soon!');
        });

        // Reset form when modal is closed
        $('#addProductModal').on('hidden.bs.modal', function () {
            $('#productForm')[0].reset();
            $('#product_id').val('');
            $('.modal-title').text('List New Product');
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const userProfile = document.querySelector('.user-profile');
        const dropdown = document.querySelector('.profile-dropdown');
        
        // Custom dropdown handler for mobile
        if (window.innerWidth <= 768) {
            userProfile.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle dropdown
                dropdown.classList.toggle('show');
                
                // Handle backdrop
                if (dropdown.classList.contains('show')) {
                    const backdrop = document.createElement('div');
                    backdrop.className = 'dropdown-backdrop';
                    document.body.appendChild(backdrop);
                    
                    backdrop.addEventListener('click', function() {
                        dropdown.classList.remove('show');
                        backdrop.remove();
                    });
                } else {
                    const backdrop = document.querySelector('.dropdown-backdrop');
                    if (backdrop) backdrop.remove();
                }
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !userProfile.contains(e.target)) {
                dropdown.classList.remove('show');
                const backdrop = document.querySelector('.dropdown-backdrop');
                if (backdrop) backdrop.remove();
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const backdrop = document.querySelector('.dropdown-backdrop');
                if (backdrop) backdrop.remove();
            }
        });
    });
    </script>
</body>
</html>
