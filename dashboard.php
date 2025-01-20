<?php
session_start(); // Start the session

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Set language based on selection or session
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang; // Store language in session
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
        'team' => 'Team',
        'projects' => 'Projects',
        'calendar' => 'Calendar',
        'ar_viz' => 'AR Visualization',
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
        'weather_forecast_desc' => 'Stay ahead with accurate weather predictions and plan your farming activities accordingly.'
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
        'team' => 'टीम',
        'projects' => 'परियोजनाएं',
        'calendar' => 'कैलेंडर',
        'ar_viz' => 'एआर विज़ुअलाइज़ेशन',
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
        'weather_forecast_desc' => 'सटीक मौसम भविष्यवाणियों के साथ आगे रहें और तदनुसार अपनी कृषि गतिविधियों की योजना बनाएं।'
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
        'team' => 'ટીમ',
        'projects' => 'પ્રોજેક્ટ્સ',
        'calendar' => 'કેલેન્ડર',
        'ar_viz' => 'એઆર વિઝ્યુઅલાઇઝેશન',
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
        'weather_forecast_desc' => 'ચોક્કસ હવામાન આગાહીઓ સાથે આગળ રહો અને તે મુજબ તમારી ખેતી પ્રવૃત્તિઓનું આયોજન કરો.'
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
    <style>
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
            padding: 1.5rem;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 100vh;
            font-size: 0.9rem;
        }

        .sidebar-logo {
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .sidebar-logo h3 {
            color: #ffffff;
            font-weight: 600;
            margin: 0;
            font-size: 1.5rem;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            flex: 1;
            padding-bottom: 1rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.8rem 1rem;
            margin: 0.1rem 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
            font-size: 0.85rem;
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
            margin-right: 10px;
            width: 18px;
            font-size: 1rem;
        }

        .logout-container {
            margin-top: auto;
            padding-top: 0.8rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .logout-link {
            background: rgba(255,59,48,0.1);
            color: #ff3b30;
            width: 100%;
            margin: 0;
            font-size: 0.85rem;
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
            height: 60px;
            background: white;
            padding: 0.8rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            z-index: 900;
            font-size: 0.9rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #3182ce;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
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

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 100%;
                max-width: 300px;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-header {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-logo">
            <h3>Kisan.ai</h3>
        </div>
        <div class="nav-links">
            <a href="#" class="nav-link active"><i class="fas fa-home"></i><?php echo $translations[$lang]['dashboard']; ?></a>
            <a href="inventory.php" class="nav-link"><i class="fas fa-box"></i><?php echo $translations[$lang]['inventory']; ?></a>
            <a href="market.php" class="nav-link"><i class="fas fa-chart-line"></i><?php echo $translations[$lang]['market']; ?></a>
            <a href="weather.php" class="nav-link"><i class="fas fa-cloud-sun"></i><?php echo $translations[$lang]['weather']; ?></a>
            <a href="tools.php" class="nav-link"><i class="fas fa-tools"></i><?php echo $translations[$lang]['tools']; ?></a>
            <a href="ai_assistant.php" class="nav-link"><i class="fas fa-robot"></i><?php echo $translations[$lang]['ai']; ?></a>
            <a href="profit_calculator.php" class="nav-link"><i class="fas fa-calculator"></i><?php echo $translations[$lang]['profit_calc']; ?></a>
            <a href="ar_visualization.php" class="nav-link"><i class="fas fa-vr-cardboard"></i><?php echo $translations[$lang]['ar_viz']; ?></a>
        </div>
        <div class="logout-container">
            <a href="logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i><?php echo $translations[$lang]['logout']; ?></a>
        </div>
    </nav>

    <!-- Header -->
    <header class="main-header">
        <div class="lang-selector">
            <a href="?lang=en" class="btn btn-outline-primary btn-sm">English</a>
            <a href="?lang=hi" class="btn btn-outline-primary btn-sm">हिंदी</a>
            <a href="?lang=gu" class="btn btn-outline-primary btn-sm">ગુજરાતી</a>
        </div>
        <div class="user-profile">
            <div class="user-avatar">
                <?php echo substr($_SESSION['name'], 0, 1); ?>
            </div>
            <span><?php echo $_SESSION['name']; ?></span>
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
</body>
</html>
