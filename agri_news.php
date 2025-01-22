<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user's language preference from session
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'hi';

// Define news sources for each language
$news_sources = [
    'en' => [
        [
            'name' => 'AgFunder News',
            'url' => 'https://agfundernews.com',
            'description' => 'Latest news on agrifood technology and investment',
            'icon' => 'fa-seedling'
        ],
        [
            'name' => 'Agriculture.com', 
            'url' => 'https://www.agriculture.com',
            'description' => 'Successful Farming - News, Markets & Analysis',
            'icon' => 'fa-tractor'
        ],
        [
            'name' => 'AgWeb',
            'url' => 'https://www.agweb.com',
            'description' => 'Farm Journal\'s news and business information for agriculture',
            'icon' => 'fa-wheat-awn'
        ],
        [
            'name' => 'Modern Farmer',
            'url' => 'https://modernfarmer.com',
            'description' => 'Latest farming trends and agricultural innovation news',
            'icon' => 'fa-farm'
        ]
    ],
    'hi' => [
        [
            'name' => 'कृषि जागरण',
            'url' => 'https://hindi.krishijagran.com',
            'description' => 'किसानों के लिए खेती से जुड़ी सभी जानकारी',
            'icon' => 'fa-leaf'
        ],
        [
            'name' => 'गांव कनेक्शन',
            'url' => 'https://www.gaonconnection.com/hindi/agriculture', 
            'description' => 'ग्रामीण भारत की आवाज़ - कृषि समाचार और जानकारी',
            'icon' => 'fa-sun'
        ],
        [
            'name' => 'किसान समाचार',
            'url' => 'https://www.aajtak.in/agriculture',
            'description' => 'किसानों के लिए विश्वसनीय कृषि समाचार और मार्केट अपडेट',
            'icon' => 'fa-newspaper'
        ],
        [
            'name' => 'कृषि दर्पण',
            'url' => 'https://www.krishakjagat.org/',
            'description' => 'आधुनिक कृषि तकनीक और बाजार जानकारी',
            'icon' => 'fa-seedling'
        ]
    ],
    'gu' => [
        [
            'name' => 'કૃષિ જાગરણ',
            'url' => 'https://gujarati.krishijagran.com',
            'description' => 'ખેતી અને ખેડૂતો માટે સમાચાર',
            'icon' => 'fa-leaf'
        ],
        [
            'name' => 'ખેડૂત મિત્ર',
            'url' => 'https://gujarati.news18.com/business/agriculture/',
            'description' => 'ગુજરાતના ખેડૂતો માટે માર્ગદર્શન અને સમાચાર',
            'icon' => 'fa-sun'
        ],
        [
            'name' => 'સંદેશ કૃષિ',
            'url' => 'https://www.sandesh.com/agriculture',
            'description' => 'ગુજરાતના ખેડૂતો માટે તાજા સમાચાર',
            'icon' => 'fa-newspaper'
        ],
        [
            'name' => 'કૃષિ સમાચાર',
            'url' => 'https://tv9gujarati.com/dhartiputra-agriculture',
            'description' => 'ખેતી વિષયક માહિતી અને તાજા સમાચાર',
            'icon' => 'fa-wheat-awn'
        ]
    ]
];

// Translations
$translations = [
    'en' => [
        'news' => 'Agricultural News Portals',
        'visit_site' => 'Visit Website',
        'popular_sources' => 'Popular Agriculture News Sources',
        'back_to_dashboard' => 'Back to Dashboard'
    ],
    'hi' => [
        'news' => 'कृषि समाचार पोर्टल',
        'visit_site' => 'वेबसाइट देखें',
        'popular_sources' => 'लोकप्रिय कृषि समाचार स्रोत',
        'back_to_dashboard' => 'डैशबोर्ड पर वापस जाएं'
    ],
    'gu' => [
        'news' => 'કૃષિ સમાચાર પોર્ટલ',
        'visit_site' => 'વેબસાઇટ જુઓ',
        'popular_sources' => 'લોકપ્રિય કૃષિ સમાચાર સ્ત્રોતો',
        'back_to_dashboard' => 'ડેશબોર્ડ પર પાછા જાઓ'
    ]
];

$current_translations = $translations[$lang];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_translations['news']; ?> - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans', 'Noto Sans Devanagari', sans-serif;
            background: url('assets/images/farm-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            padding: 2rem 0;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            z-index: 0;
        }

        .container {
            max-width: 1200px;
            position: relative;
            z-index: 1;
        }

        .section-title {
            font-size: 3.5rem;
            color: #1b5e20;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            position: relative;
            padding-bottom: 20px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #4caf50, #8bc34a);
            border-radius: 2px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
            padding: 2rem;
        }

        .news-source-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
            position: relative;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .news-source-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 7px;
            background: linear-gradient(90deg, #2e7d32, #66bb6a);
            border-radius: 20px 20px 0 0;
        }

        .news-source-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .source-icon {
            font-size: 3rem;
            color: #2e7d32;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.2);
        }

        .source-name {
            font-size: 1.8rem;
            color: #1b5e20;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .source-description {
            color: #37474f;
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .visit-btn {
            background: linear-gradient(45deg, #2e7d32, #43a047);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }

        .visit-btn:hover {
            background: linear-gradient(45deg, #1b5e20, #2e7d32);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 7px 20px rgba(46, 125, 50, 0.4);
        }

        .popular-sources {
            text-align: center;
            color: #2e7d32;
            font-size: 2rem;
            margin: 2rem 0 3rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
            padding: 0 2rem;
        }

        .popular-sources::before,
        .popular-sources::after {
            content: '🌾';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
        }

        .popular-sources::before {
            left: -1rem;
        }

        .popular-sources::after {
            right: -1rem;
        }

        .back-btn {
            background: linear-gradient(45deg, #1b5e20, #2e7d32);
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 2rem auto;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }

        .back-btn:hover {
            background: linear-gradient(45deg, #2e7d32, #43a047);
            color: white;
            transform: translateX(-5px);
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2.5rem;
            }
            
            .news-grid {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="section-title"><?php echo $current_translations['news']; ?></h1>
        <h2 class="popular-sources"><?php echo $current_translations['popular_sources']; ?></h2>
        
        <div class="news-grid">
            <?php foreach ($news_sources[$lang] as $source): ?>
                <div class="news-source-card">
                    <div class="source-icon">
                        <i class="fas <?php echo $source['icon']; ?>"></i>
                    </div>
                    <h3 class="source-name"><?php echo htmlspecialchars($source['name']); ?></h3>
                    <p class="source-description"><?php echo htmlspecialchars($source['description']); ?></p>
                    <a href="<?php echo htmlspecialchars($source['url']); ?>" target="_blank" class="visit-btn">
                        <?php echo $current_translations['visit_site']; ?>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center">
            <a href="dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <?php echo $current_translations['back_to_dashboard']; ?>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
