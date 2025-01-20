<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Create agri_news table if it doesn't exist
$create_news_table = "CREATE TABLE IF NOT EXISTS agri_news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    title_hi VARCHAR(255) NOT NULL,
    title_gu VARCHAR(255) NOT NULL, 
    content TEXT NOT NULL,
    content_hi TEXT NOT NULL,
    content_gu TEXT NOT NULL,
    image_url VARCHAR(255),
    published_date DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($create_news_table)) {
    die("Error creating news table: " . $conn->error);
}

// Create agri_blogs table if it doesn't exist
$create_blogs_table = "CREATE TABLE IF NOT EXISTS agri_blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    title_hi VARCHAR(255) NOT NULL,
    title_gu VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    content_hi TEXT NOT NULL,
    content_gu TEXT NOT NULL,
    image_url VARCHAR(255),
    author VARCHAR(100) NOT NULL,
    published_date DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($create_blogs_table)) {
    die("Error creating blogs table: " . $conn->error);
}

// Drop existing tables to recreate with correct schema
$conn->query("DROP TABLE IF EXISTS agri_news");
$conn->query("DROP TABLE IF EXISTS agri_blogs");

// Recreate tables
$conn->query($create_news_table);
$conn->query($create_blogs_table);

// Insert sample news if table is empty
$news_count = $conn->query("SELECT COUNT(*) as count FROM agri_news")->fetch_assoc()['count'];
if ($news_count == 0) {
    $sample_news = [
        [
            'title' => 'New Drought-Resistant Crop Varieties Released',
            'title_hi' => 'सूखा प्रतिरोधी फसल की नई किस्में जारी',
            'title_gu' => 'દુષ્કાળ પ્રતિરોધક પાકની નવી જાતો રજૂ',
            'content' => 'Scientists have developed new crop varieties that can withstand prolonged periods of drought. These varieties show promising results with up to 40% higher yields in dry conditions compared to traditional varieties.',
            'content_hi' => 'वैज्ञानिकों ने नई फसल किस्में विकसित की हैं जो लंबी अवधि के सूखे का सामना कर सकती हैं। ये किस्में पारंपरिक किस्मों की तुलना में सूखी परिस्थितियों में 40% तक अधिक उपज देने के आशाजनक परिणाम दिखाती हैं।',
            'content_gu' => 'વૈજ્ઞાનિકોએ નવી પાક જાતો વિકસાવી છે જે લાંબા સમય સુધી દુષ્કાળનો સામનો કરી શકે છે. આ જાતો પરંપરાગત જાતોની તુલનામાં સૂકી પરિસ્થિતિઓમાં 40% સુધી વધુ ઉપજ આપવાના આશાસ્પદ પરિણામો દર્શાવે છે.',
            'image_url' => 'https://erc.europa.eu/sites/default/files/stories/images/iStock-947283378.jpg'
        ],
        [
            'title' => 'Government Launches New Farmer Support Program',
            'title_hi' => 'सरकार ने नया किसान सहायता कार्यक्रम शुरू किया',
            'title_gu' => 'સરકારે નવો ખેડૂત સહાય કાર્યક્રમ શરૂ કર્યો',
            'content' => 'The Agriculture Ministry has announced a new support program providing subsidies for modern farming equipment and techniques. Farmers can apply through local agricultural offices.',
            'content_hi' => 'कृषि मंत्रालय ने आधुनिक कृषि उपकरणों और तकनीकों के लिए सब्सिडी प्रदान करने वाला एक नया सहायता कार्यक्रम घोषित किया है। किसान स्थानीय कृषि कार्यालयों के माध्यम से आवेदन कर सकते हैं।',
            'content_gu' => 'કૃષિ મંત્રાલયે આધુનિક ખેતીના સાધનો અને તકનીકો માટે સબસિડી આપતો નવો સહાય કાર્યક્રમ જાહેર કર્યો છે. ખેડૂતો સ્થાનિક કૃષિ કચેરીઓ મારફતે અરજી કરી શકે છે.',
            'image_url' => 'https://timesofagriculture.in/wp-content/uploads/2023/08/feature-image-2-1-1-1-1024x576.jpg'
        ],
        [
            'title' => 'Organic Farming Sees 25% Growth in Gujarat',
            'title_hi' => 'गुजरात में जैविक खेती में 25% की वृद्धि',
            'title_gu' => 'ગુજરાતમાં સેન્દ્રિય ખેતીમાં 25% વૃદ્ધિ',
            'content' => 'Organic farming practices have seen significant adoption in Gujarat, with a 25% increase in organic farmland over the past year. Farmers report higher profits due to premium pricing.',
            'content_hi' => 'गुजरात में जैविक खेती पद्धतियों को काफी अपनाया गया है, पिछले साल की तुलना में जैविक खेती में 25% की वृद्धि हुई है। किसानों को प्रीमियम मूल्य के कारण अधिक लाभ की सूचना मिल रही है।',
            'content_gu' => 'ગુજરાતમાં સેન્દ્રિય ખેતી પદ્ધતિઓનો નોંધપાત્ર સ્વીકાર થયો છે, છેલ્લા વર્ષમાં સેન્દ્રિય ખેતીમાં 25% વધારો થયો છે. ખેડૂતો પ્રીમિયમ ભાવને કારણે ઊંચા નફાની જાણ કરે છે.',
            'image_url' => 'https://i.ytimg.com/vi/S1eB1ztbi1M/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLDcjOTDy2oMZu2Zi5tDtNq5Mund-A'
        ]
    ];

    foreach ($sample_news as $news) {
        $stmt = $conn->prepare("INSERT INTO agri_news (title, title_hi, title_gu, content, content_hi, content_gu, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", 
            $news['title'],
            $news['title_hi'],
            $news['title_gu'],
            $news['content'],
            $news['content_hi'],
            $news['content_gu'],
            $news['image_url']
        );
        if (!$stmt->execute()) {
            die("Error inserting news: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Insert sample blogs if table is empty
$blogs_count = $conn->query("SELECT COUNT(*) as count FROM agri_blogs")->fetch_assoc()['count'];
if ($blogs_count == 0) {
    $sample_blogs = [
        [
            'title' => 'Best Practices for Sustainable Agriculture',
            'title_hi' => 'टिकाऊ कृषि के लिए सर्वोत्तम प्रथाएं',
            'title_gu' => 'ટકાઉ ખેતી માટેની શ્રેષ્ઠ પદ્ધતિઓ',
            'content' => 'Sustainable agriculture is key to our future. This post explores various techniques like crop rotation, natural pest control, and water conservation methods that can help create a more sustainable farming system.',
            'content_hi' => 'टिकाऊ कृषि हमारे भविष्य की कुंजी है। यह पोस्ट फसल चक्र, प्राकृतिक कीट नियंत्रण और जल संरक्षण विधियों जैसी विभिन्न तकनीकों की खोज करता है जो एक अधिक टिकाऊ कृषि प्रणाली बनाने में मदद कर सकती हैं।',
            'content_gu' => 'ટકાઉ ખેતી આપણા ભવિષ્ય માટે મહત્વપૂર્ણ છે. આ પોસ્ટ પાક ફેરબદલી, કુદરતી જીવાત નિયંત્રણ અને પાણી સંરક્ષણની પદ્ધતિઓ જેવી વિવિધ તકનીકોની તપાસ કરે છે જે વધુ ટકાઉ ખેતી પ્રણાલી બનાવવામાં મદદ કરી શકે છે.',
            'image_url' => 'https://preview.eitfood.eu/media/news/MicrosoftTeams-image_%2841%29.png',
            'author' => 'Dr. Patel'
        ],
        [
            'title' => 'Modern Technology in Agriculture',
            'title_hi' => 'कृषि में आधुनिक तकनीक',
            'title_gu' => 'ખેતીમાં આધુનિક ટેકનોલોજી',
            'content' => 'From GPS-guided tractors to drone monitoring, modern technology is revolutionizing farming. Learn how these innovations can improve your farm\'s efficiency and productivity.',
            'content_hi' => 'जीपीएस-निर्देशित ट्रैक्टरों से लेकर ड्रोन निगरानी तक, आधुनिक तकनीक खेती में क्रांति ला रही है। जानें कैसे ये नवाचार आपके खेत की दक्षता और उत्पादकता में सुधार कर सकते हैं।',
            'content_gu' => 'GPS-ગાઈડેડ ટ્રેક્ટરથી લઈને ડ્રોન મોનિટરિંગ સુધી, આધુનિક ટેકનોલોજી ખેતીમાં ક્રાંતિ લાવી રહી છે. જાણો કે આ નવીનતાઓ તમારા ખેતરની કાર્યક્ષમતા અને ઉત્પાદકતામાં કેવી રીતે સુધારો કરી શકે છે.',
            'image_url' => 'https://kj1bcdn.b-cdn.net/media/100537/modern-farming.png',
            'author' => 'Tech Farmer'
        ],
        [
            'title' => 'Monsoon Farming Tips',
            'title_hi' => 'मानसून खेती के टिप्स',
            'title_gu' => 'ચોમાસુ ખેતીના ટિપ્સ',
            'content' => 'Preparing your farm for monsoon season is crucial. This guide covers essential preparations, crop selection, and management practices for successful monsoon farming.',
            'content_hi' => 'मानसून के मौसम के लिए अपने खेत को तैयार करना महत्वपूर्ण है। यह गाइड सफल मानसून खेती के लिए आवश्यक तैयारियों, फसल चयन और प्रबंधन प्रथाओं को कवर करता है।',
            'content_gu' => 'ચોમાસા માટે તમારા ખેતરની તૈયારી કરવી ખૂબ જ મહત્વપૂર્ણ છે. આ માર્ગદર્શિકા સફળ ચોમાસુ ખેતી માટે આવશ્યક તૈયારીઓ, પાક પસંદગી અને વ્યવસ્થાપન પદ્ધતિઓને આવરી લે છે.',
            'image_url' => 'https://akm-img-a-in.tosshub.com/indiatoday/images/story/201910/Flood.png?size=690:388',
            'author' => 'Experienced Farmer'
        ]
    ];

    foreach ($sample_blogs as $blog) {
        $stmt = $conn->prepare("INSERT INTO agri_blogs (title, title_hi, title_gu, content, content_hi, content_gu, image_url, author) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss",
            $blog['title'],
            $blog['title_hi'],
            $blog['title_gu'],
            $blog['content'],
            $blog['content_hi'],
            $blog['content_gu'],
            $blog['image_url'],
            $blog['author']
        );
        if (!$stmt->execute()) {
            die("Error inserting blog: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Get language from session
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Translations array
$translations = [
    'en' => [
        'agri_news' => 'Agricultural News & Insights',
        'latest_news' => 'Latest News',
        'featured_blogs' => 'Featured Blogs',
        'read_more' => 'Read More',
        'published_on' => 'Published on',
        'by_author' => 'By',
        'no_news' => 'No news articles available at the moment.',
        'no_blogs' => 'No blog posts available at the moment.',
        'back_to_dashboard' => 'Back to Dashboard'
    ],
    'hi' => [
        'agri_news' => 'कृषि समाचार और जानकारी',
        'latest_news' => 'ताज़ा खबरें',
        'featured_blogs' => 'विशेष ब्लॉग',
        'read_more' => 'और पढ़ें',
        'published_on' => 'प्रकाशित',
        'by_author' => 'द्वारा',
        'no_news' => 'इस समय कोई समाचार उपलब्ध नहीं है।',
        'no_blogs' => 'इस समय कोई ब्लॉग पोस्ट उपलब्ध नहीं है।',
        'back_to_dashboard' => 'डैशबोर्ड पर वापस जाएं'
    ],
    'gu' => [
        'agri_news' => 'કૃષિ સમાચાર અને માહિતી',
        'latest_news' => 'તાજા સમાચાર',
        'featured_blogs' => 'વિશેષ બ્લોગ્સ',
        'read_more' => 'વધુ વાંચો',
        'published_on' => 'પ્રકાશિત',
        'by_author' => 'દ્વારા',
        'no_news' => 'હાલમાં કોઈ સમાચાર ઉપલબ્ધ નથી.',
        'no_blogs' => 'હાલમાં કોઈ બ્લોગ પોસ્ટ્સ ઉપલબ્ધ નથી.',
        'back_to_dashboard' => 'ડેશબોર્ડ પર પાછા જાઓ'
    ]
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kisan.ai - <?php echo $translations[$lang]['agri_news']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #4CAF50;
            --light-green: #8BC34A;
            --pale-green: #E8F5E9;
            --dark-green: #2E7D32;
            --white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--pale-green);
        }

        .news-container {
            padding: 2rem;
        }

        .section-title {
            color: var(--dark-green);
            font-weight: 600;
            margin-bottom: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid var(--primary-green);
        }

        .news-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .news-card:hover {
            transform: translateY(-5px);
        }

        .news-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .news-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .news-title {
            color: var(--dark-green);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .news-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .news-excerpt {
            color: #444;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .read-more-btn {
            background: var(--primary-green);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            text-decoration: none;
            transition: background 0.3s ease;
            display: inline-block;
            cursor: pointer;
            text-align: center;
            margin-top: auto;
        }

        .read-more-btn:hover {
            background: var(--dark-green);
            color: var(--white);
        }

        .back-to-dashboard {
            position: fixed;
            top: 20px;
            left: 20px;
            background: var(--primary-green);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            text-decoration: none;
            transition: background 0.3s ease;
            z-index: 1000;
        }

        .back-to-dashboard:hover {
            background: var(--dark-green);
            color: var(--white);
        }

        @media (max-width: 768px) {
            .news-container {
                padding: 1rem;
            }
            
            .news-card {
                margin-bottom: 2rem;  /* Increased margin between cards */
                min-height: auto;
            }

            .news-image {
                height: 180px;  /* Increased height for better visibility */
                object-fit: cover;
                object-position: center;
            }

            .news-content {
                padding: 1.25rem;  /* Slightly increased padding */
            }

            .news-title {
                font-size: 1.1rem;
                margin-bottom: 0.75rem;
            }

            .news-meta {
                font-size: 0.8rem;
                margin-bottom: 0.75rem;
            }

            .news-excerpt {
                font-size: 0.85rem;
                margin-bottom: 0.75rem;
            }

            .read-more-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .news-container {
                padding: 1rem;  /* Increased padding */
            }

            .news-card {
                margin-bottom: 1.5rem;  /* Increased margin for more spacing */
            }

            .news-image {
                height: 160px;  /* Increased height for better visibility */
                object-fit: cover;
                object-position: center;
            }

            .news-content {
                padding: 1rem;  /* Increased padding */
            }

            .news-title {
                font-size: 1rem;
                margin-bottom: 0.75rem;  /* Increased margin */
            }

            .news-meta {
                font-size: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .news-excerpt {
                font-size: 0.8rem;
                margin-bottom: 0.75rem;
            }

            .read-more-btn {
                padding: 0.4rem 0.8rem;  /* Increased padding */
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="back-to-dashboard">
        <i class="fas fa-arrow-left"></i> <?php echo $translations[$lang]['back_to_dashboard']; ?>
    </a>
    
    <div class="container news-container">
        <h1 class="text-center mb-5"><?php echo $translations[$lang]['agri_news']; ?></h1>

        <!-- Latest News Section -->
        <h2 class="section-title"><?php echo $translations[$lang]['latest_news']; ?></h2>
        <div class="row">
            <?php
            $news_query = "SELECT * FROM agri_news ORDER BY published_date DESC LIMIT 3";
            $news_result = $conn->query($news_query);

            if ($news_result->num_rows > 0) {
                while($news = $news_result->fetch_assoc()) {
                    $title_field = ($lang == 'en') ? 'title' : 'title_' . $lang;
                    $content_field = ($lang == 'en') ? 'content' : 'content_' . $lang;
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="news-card">
                            <img src="<?php echo $news['image_url']; ?>" alt="<?php echo $news[$title_field]; ?>" class="news-image">
                            <div class="news-content">
                                <h3 class="news-title"><?php echo $news[$title_field]; ?></h3>
                                <div class="news-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?php echo $translations[$lang]['published_on']; ?>: <?php echo date('d M Y', strtotime($news['published_date'])); ?></span>
                                </div>
                                <p class="news-excerpt"><?php echo substr($news[$content_field], 0, 150); ?>...</p>
                                <button class="read-more-btn">
                                    <?php echo $translations[$lang]['read_more']; ?> <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center'>" . $translations[$lang]['no_news'] . "</p>";
            }
            ?>
        </div>

        <!-- Featured Blogs Section -->
        <h2 class="section-title mt-5"><?php echo $translations[$lang]['featured_blogs']; ?></h2>
        <div class="row">
            <?php
            $blogs_query = "SELECT * FROM agri_blogs ORDER BY published_date DESC LIMIT 3";
            $blogs_result = $conn->query($blogs_query);

            if ($blogs_result->num_rows > 0) {
                while($blog = $blogs_result->fetch_assoc()) {
                    $title_field = ($lang == 'en') ? 'title' : 'title_' . $lang;
                    $content_field = ($lang == 'en') ? 'content' : 'content_' . $lang;
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="news-card">
                            <img src="<?php echo $blog['image_url']; ?>" alt="<?php echo $blog[$title_field]; ?>" class="news-image">
                            <div class="news-content">
                                <h3 class="news-title"><?php echo $blog[$title_field]; ?></h3>
                                <div class="news-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?php echo $translations[$lang]['published_on']; ?>: <?php echo date('d M Y', strtotime($blog['published_date'])); ?></span><br>
                                    <span><i class="far fa-user"></i> <?php echo $translations[$lang]['by_author']; ?>: <?php echo $blog['author']; ?></span>
                                </div>
                                <p class="news-excerpt"><?php echo substr($blog[$content_field], 0, 150); ?>...</p>
                                <button class="read-more-btn">
                                    <?php echo $translations[$lang]['read_more']; ?> <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center'>" . $translations[$lang]['no_blogs'] . "</p>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
