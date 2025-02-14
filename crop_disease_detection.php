<?php
// Enable full error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_connect.php';
require_once 'disease_recommendations.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Set language based on selection or session
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = 'en';
    $_SESSION['lang'] = $lang;
}

// Language translations
$translations = [
    'en' => [
        'disease_detection' => 'Crop Disease Detection',
        'upload_image' => 'Upload Crop Image',
        'choose_image' => 'Choose image',
        'detect' => 'Detect Disease',
        'results' => 'Detection Results',
        'detected_disease' => 'Detected Disease:',
        'confidence' => 'Confidence:',
        'treatment' => 'Recommended Treatment:',
        'error_not_image' => 'File is not an image.',
        'error_large_file' => 'Sorry, your file is too large.',
        'error_file_type' => 'Sorry, only JPG, JPEG & PNG files are allowed.',
        'error_upload' => 'Sorry, there was an error uploading your file.',
        'back_to_dashboard' => 'Back to Dashboard',
        'error_not_crop' => 'Please upload only crop or plant images.',
        'no_results' => 'No detection results yet'
    ],
    'hi' => [
        'disease_detection' => 'फसल रोग पहचान',
        'upload_image' => 'फसल की छवि अपलोड करें',
        'choose_image' => 'छवि चुनें',
        'detect' => 'रोग का पता लगाएं',
        'results' => 'पहचान परिणाम',
        'detected_disease' => 'पहचाना गया रोग:',
        'confidence' => 'विश्वास स्तर:',
        'treatment' => 'अनुशंसित उपचार:',
        'error_not_image' => 'फ़ाइल एक छवि नहीं है।',
        'error_large_file' => 'क्षमा करें, आपकी फ़ाइल बहुत बड़ी है।',
        'error_file_type' => 'क्षमा करें, केवल JPG, JPEG और PNG फ़ाइलें स्वीकृत हैं।',
        'error_upload' => 'क्षमा करें, आपकी फ़ाइल अपलोड करने में त्रुटि हुई।',
        'back_to_dashboard' => 'डैशबोर्ड पर वापस जाएं',
        'error_not_crop' => 'कृपया केवल फसल या पौधों की छवियां अपलोड करें।',
        'no_results' => 'अभी तक कोई पहचान परिणाम नहीं'
    ],
    'gu' => [
        'disease_detection' => 'પાક રોગ ઓળખ',
        'upload_image' => 'પાક છબી અપલોડ કરો',
        'choose_image' => 'છબી પસંદ કરો',
        'detect' => 'રોગ શોધો',
        'results' => 'ઓળખ પરિણામો',
        'detected_disease' => 'શોધાયેલ રોગ:',
        'confidence' => 'વિશ્વાસ સ્તર:',
        'treatment' => 'ભલામણ કરેલ સારવાર:',
        'error_not_image' => 'ફાઇલ છબી નથી.',
        'error_large_file' => 'માફ કરશો, તમારી ફાઇલ ખૂબ મોટી છે.',
        'error_file_type' => 'માફ કરશો, માત્ર JPG, JPEG અને PNG ફાઇલો માન્ય છે.',
        'error_upload' => 'માફ કરશો, તમારી ફાઇલ અપલોડ કરવામાં ભૂલ થઈ.',
        'back_to_dashboard' => 'ડેશબોર્ડ પર પાછા જાઓ',
        'error_not_crop' => 'કૃપા કરીને માત્ર પાક અથવા છોડની છબીઓ અપલોડ કરો.',
        'no_results' => 'હજુ સુધી કોઈ ઓળખ પરિણામો નથી'
    ]
];

// Local disease detection database for additional mapping details
$disease_database = [
    'leaf_blight' => [
        'name' => [
            'en' => 'Leaf Blight',
            'hi' => 'पत्ती झुलसा',
            'gu' => 'પાન બ્લાઇટ'
        ],
        'confidence' => 95,
        'treatment' => [
            'en' => 'Apply copper-based fungicide and ensure proper irrigation',
            'hi' => 'कॉपर-आधारित फफूंदनाशक लगाएं और उचित सिंचाई सुनिश्चित करें',
            'gu' => 'કોપર-આધારિત ફૂગનાશક લગાવો અને યોગ્ય સિંચાઈ સુનિશ્ચિત કરો'
        ],
        'image' => 'images/diseases/leaf_blight.jpg'
    ],
    'powdery_mildew' => [
        'name' => [
            'en' => 'Powdery Mildew',
            'hi' => 'चूर्णिल फफूंदी',
            'gu' => 'પાવડરી મિલ્ડ્યુ'
        ],
        'confidence' => 92,
        'treatment' => [
            'en' => 'Apply sulfur-based fungicide and improve air circulation',
            'hi' => 'सल्फर-आधारित फफूंदनाशक लगाएं और हवा का संचार बढ़ाएं',
            'gu' => 'સલ્ફર-આધારિત ફૂગનાશક લગાવો અને હવાની અવરજવર સુધારો'
        ],
        'image' => 'images/diseases/powdery_mildew.jpg'
    ],
    'rust' => [
        'name' => [
            'en' => 'Rust Disease',
            'hi' => 'जंग रोग',
            'gu' => 'રસ્ટ રોગ'
        ],
        'confidence' => 88,
        'treatment' => [
            'en' => 'Remove infected leaves and apply fungicide',
            'hi' => 'संक्रमित पत्तियों को हटाएं और फफूंदनाशक लगाएं',
            'gu' => 'ચેપગ્રસ્ત પાંદડા દૂર કરો અને ફૂગનાશક લગાવો'
        ],
        'image' => 'images/diseases/rust.jpg'
    ]
];

// Clear previous results if not a POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    unset($_SESSION['detection_result']);
    unset($_SESSION['uploaded_image']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["cropImage"])) {
    // Use absolute path for the uploads directory
    $target_dir = __DIR__ . "/uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES["cropImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Log the uploaded file details for debugging
    error_log("FILES array: " . print_r($_FILES["cropImage"], true));

    // Check for upload errors
    if ($_FILES["cropImage"]["error"] !== UPLOAD_ERR_OK) {
        $error = "Upload error code: " . $_FILES["cropImage"]["error"];
        $uploadOk = 0;
    }

    $tmp_file = $_FILES["cropImage"]["tmp_name"];
    error_log("Temporary file path: " . $tmp_file);

    // Check if the file was properly uploaded
    if ($uploadOk && !is_uploaded_file($tmp_file)) {
        $error = "The file was not properly uploaded.";
        $uploadOk = 0;
        error_log($error);
    }

    // Check if the temporary file exists
    if ($uploadOk && !file_exists($tmp_file)) {
        $error = "Temporary file does not exist: " . $tmp_file;
        $uploadOk = 0;
        error_log($error);
    }

    // Log additional info about the temporary file
    if ($uploadOk) {
        error_log("File size: " . $_FILES["cropImage"]["size"]);
        $mime = mime_content_type($tmp_file);
        error_log("Detected MIME type: " . $mime);
    }

    // Validate that the file is an image using getimagesize (before moving the file)
    if ($uploadOk) {
        $check = @getimagesize($tmp_file);
        if ($check === false) {
            $error = $translations[$lang]['error_not_image'];
            $uploadOk = 0;
            error_log("getimagesize failed: " . $error);
        } else {
            error_log("getimagesize result: " . print_r($check, true));
        }
    }

    // Check file size (limit: 5MB)
    if ($uploadOk && $_FILES["cropImage"]["size"] > 5000000) {
        $error = $translations[$lang]['error_large_file'];
        $uploadOk = 0;
        error_log($error);
    }

    // Validate file extension
    if ($uploadOk && ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")) {
        $error = $translations[$lang]['error_file_type'];
        $uploadOk = 0;
        error_log($error);
    }

    if ($uploadOk == 0) {
        $error = isset($error) ? $error : $translations[$lang]['error_upload'];
    } else {
        if (move_uploaded_file($tmp_file, $target_file)) {
            // File moved successfully; now call the Flask API for disease prediction
            $_SESSION['uploaded_image'] = $target_file;

            // Set your Flask API URL
            $apiUrl = 'http://localhost:5000/predict';
            $cfile = curl_file_create($target_file, mime_content_type($target_file), basename($target_file));
            $postData = array('image' => $cfile);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $apiResponse = curl_exec($ch);
            
            if (curl_errno($ch)) {
                $error = 'API Request Error: ' . curl_error($ch);
                $uploadOk = 0;
                error_log($error);
            }
            curl_close($ch);

            if ($uploadOk == 1) {
                $result = json_decode($apiResponse, true);
                error_log("API Response: " . print_r($result, true)); // Debug log
                
                if (isset($result['prediction'])) {
                    $detected_disease = trim($result['prediction']);
                    $_SESSION['detected_disease'] = $detected_disease;
                    
                    // Store confidence value - handle both possible response formats
                    if (isset($result['confidence'])) {
                        $_SESSION['confidence'] = floatval($result['confidence']);
                    } elseif (isset($result['probability'])) {
                        $_SESSION['confidence'] = floatval($result['probability']);
                    } else {
                        $_SESSION['confidence'] = 0.95; // Default high confidence if not provided
                    }
                    
                    error_log("Confidence value stored: " . $_SESSION['confidence']); // Debug log
                    
                    // Get detailed recommendations
                    $disease_info = getDetailedRecommendations($detected_disease, $lang);
                    $_SESSION['detection_result'] = $disease_info;
                } else {
                    $error = isset($result['error']) ? $result['error'] : $translations[$lang]['error_upload'];
                }
            }
            error_log("File moved successfully to: " . $target_file);
        } else {
            $error = $translations[$lang]['error_upload'] . " (move_uploaded_file() failed. Temp: " . $tmp_file . ", Target: " . $target_file . ")";
            error_log($error);
            $uploadOk = 0;
        }
    }
}

// Add this function at the top of your file
function getConfidencePercentage() {
    if (!isset($_SESSION['confidence'])) {
        error_log("No confidence value in session");
        return 0;
    }
    
    $confidence = $_SESSION['confidence'];
    
    // Handle different formats
    if (is_string($confidence)) {
        $confidence = floatval($confidence);
    }
    
    // If confidence is already in percentage format (>1)
    if ($confidence > 1) {
        $confidence = $confidence / 100;
    }
    
    $percentage = round($confidence * 100);
    error_log("Calculated confidence percentage: " . $percentage);
    
    return $percentage;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Disease Detection - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS styles (retained from your original code) */
        :root {
            --primary-color: #2F855A;
            --secondary-color: #276749;
            --accent-color: #C6F6D5;
            --success-color: #38A169;
            --warning-color: #D69E2E;
            --error-color: #E53E3E;
            --background-color: #F0FFF4;
            --text-color: #234E52;
            --border-color: #9AE6B4;
            --card-bg: rgba(255, 255, 255, 0.95);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--background-color);
            background-image: 
                linear-gradient(120deg, rgba(255,255,255,0.8), rgba(240,255,244,0.8)),
                url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><path d="M30 5C15 5 5 15 5 30s10 25 25 25 25-10 25-25S45 5 30 5zm0 45c-11 0-20-9-20-20s9-20 20-20 20 9 20 20-9 20-20 20z" fill="%239AE6B4" fill-opacity="0.2"/></svg>');
            background-size: auto, 60px 60px;
            color: var(--text-color);
            min-height: 100vh;
        }
        .back-button {
            position: fixed;
            top: 2rem;
            left: 2rem;
            z-index: 1000;
            padding: 0.75rem 1.5rem;
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(47,133,90,0.1);
            backdrop-filter: blur(10px);
        }
        .back-button:hover {
            transform: translateX(-5px);
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 5rem 2rem 2rem;
        }
        .upload-section, .result-section {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(47,133,90,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
        }
        .upload-zone {
            border: 2px dashed var(--border-color);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            background: var(--card-bg);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-zone.drag-over {
            background: var(--accent-color);
            border-color: var(--primary-color);
        }
        .preview-container {
            margin: 20px auto;
            max-width: 100%;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .preview-image {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 15px;
            object-fit: contain;
        }
        .disease-card {
            border-radius: 20px;
            padding: 2rem;
            background: linear-gradient(145deg, var(--card-bg), #FFFFFF);
            box-shadow: 0 10px 25px rgba(47,133,90,0.1);
            margin-top: 2rem;
            border: 1px solid var(--border-color);
        }
        .confidence-bar {
            height: 12px;
            border-radius: 10px;
            background: var(--accent-color);
            margin: 1rem 0;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(47,133,90,0.1);
        }
        .confidence-level {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
            transition: width 1.5s cubic-bezier(0.4,0,0.2,1);
        }
        .treatment-card {
            background: var(--accent-color);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 4px 12px rgba(47,133,90,0.1);
        }
        .severity-indicator {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .severity-low { background: #C6F6D5; color: #276749; }
        .severity-medium { background: #FEFCBF; color: #975A16; }
        .severity-high { background: #FED7D7; color: #C53030; }
        .btn-detect {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-detect:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        .disease-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 6px rgba(47,133,90,0.2));
        }
        h2, h3, h4, h5, h6 { color: var(--primary-color); font-weight: 600; }
        .text-primary { color: var(--primary-color) !important; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeInUp 0.6s ease-out forwards; }
        @media (max-width:768px) {
            .main-container { padding: 4rem 1rem 1rem; }
            .upload-section, .result-section { padding: 1.5rem; }
            .back-button { top: 1rem; left: 1rem; }
        }
        .alert {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border: none;
            background: rgba(220,38,38,0.1);
            color: var(--error-color);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(220,38,38,0.2);
        }
        .alert-dismissible .btn-close { padding: 1.25rem; }
        .empty-result-state {
            background: var(--accent-color);
            border-radius: 20px;
            padding: 4rem 2rem;
            text-align: center;
            border: 2px dashed var(--border-color);
            margin: 1rem 0;
        }
        .result-empty-icon {
            font-size: 4rem;
            color: var(--primary-color);
            opacity: 0.5;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.05); opacity: 0.7; }
            100% { transform: scale(1); opacity: 0.5; }
        }
        .result-section {
            min-height: 400px;
            display: flex;
            flex-direction: column;
        }
        .empty-result-state p {
            font-size: 1.1rem;
            margin: 0;
            color: var(--text-color);
        }
        .ai-recommendation-section {
            background: linear-gradient(145deg, #f0fff4, #e6ffed);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid var(--border-color);
        }
        .ai-message {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-top: 15px;
        }
        .ai-avatar {
            background: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .ai-content {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            font-size: 0.95rem;
            line-height: 1.5;
            color: var(--text-color);
        }
        .ai-recommendation-section h5 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .result-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .image-preview-section {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .uploaded-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 10px;
            display: block;
        }
        .disease-name {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
        }
        .disease-info {
            padding-top: 15px;
        }
        .ai-recommendation-section {
            margin-top: 15px;
            background: linear-gradient(145deg, #f0fff4, #e6ffed);
            border-radius: 12px;
            padding: 20px;
        }
        .ai-message {
            margin-top: 15px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        .ai-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .ai-content {
            flex: 1;
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .accuracy-badge {
            background: var(--accent-color);
            color: var(--primary-color);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .accuracy-badge span {
            display: flex;
            align-items: center;
        }
        .confidence-bar {
            height: 8px;
            background: var(--accent-color);
            border-radius: 4px;
            overflow: hidden;
        }
        .confidence-level {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 4px;
            transition: width 1s ease-in-out;
        }
        .disease-header {
            background: var(--card-bg);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="back-button">
        <i class="fas fa-arrow-left me-2"></i> <?php echo $translations[$lang]['back_to_dashboard']; ?>
    </a>
    <div class="main-container">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="upload-section fade-in">
                    <h2 class="mb-4"><?php echo $translations[$lang]['disease_detection']; ?></h2>
                    <form method="POST" enctype="multipart/form-data" id="uploadForm">
                        <div class="upload-zone" id="dropZone">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <h4 class="mb-3"><?php echo $translations[$lang]['upload_image']; ?></h4>
                            <p class="text-muted">Drag and drop your image here or click to browse</p>
                            <input type="file" class="form-control d-none" id="cropImage" name="cropImage" accept="image/*" required>
                        </div>
                        <div id="imagePreview" class="mt-4 text-center">
                            <!-- Preview will be shown here -->
                        </div>
                        <button type="submit" class="btn-detect" name="submit">
                            <i class="fas fa-microscope me-2"></i><?php echo $translations[$lang]['detect']; ?>
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="result-section fade-in">
                    <div class="text-center mb-4">
                        <i class="fas fa-leaf disease-icon"></i>
                        <h3><?php echo $translations[$lang]['results']; ?></h3>
                    </div>
                    <?php if(isset($_SESSION['detection_result']) && isset($_SESSION['uploaded_image'])): ?>
                        <div class="result-card">
                            <!-- Image Preview -->
                            <div class="image-preview-section mb-4">
                                <img src="<?php echo $_SESSION['uploaded_image']; ?>" 
                                     class="uploaded-image" 
                                     alt="Uploaded crop">
                            </div>

                            <!-- Disease Name and AI Recommendation -->
                            <div class="disease-info">
                                <div class="disease-header d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="disease-name">
                                        <i class="fas fa-bug me-2"></i>
                                        <?php echo $disease_info['name']; ?>
                                    </h4>
                                    <div class="accuracy-badge">
                                        <?php 
                                        $confidence_percentage = getConfidencePercentage();
                                        echo "<span>{$confidence_percentage}% Accuracy</span>";
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="confidence-bar mb-4">
                                    <div class="confidence-level" style="width: <?php echo $confidence_percentage; ?>%"></div>
                                </div>
                                
                                <div class="ai-recommendation-section">
                                    <h5><i class="fas fa-robot me-2"></i>AI Expert Recommendation</h5>
                                    <div class="ai-message">
                                        <div class="ai-avatar">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                        <div class="ai-content">
                                            <?php 
                                            $ai_recommendation = getAIRecommendation($disease_info, $lang);
                                            echo $ai_recommendation; 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="empty-result-state">
                            <i class="fas fa-microscope result-empty-icon"></i>
                            <p class="text-muted mt-3"><?php echo $translations[$lang]['no_results']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced drag and drop functionality with preview
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('cropImage');
        const preview = document.getElementById('imagePreview');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener('dragleave', unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('drag-over');
        }

        function unhighlight(e) {
            dropZone.classList.remove('drag-over');
        }

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            showPreview(files[0]);
        }

        fileInput.addEventListener('change', (e) => {
            showPreview(e.target.files[0]);
        });

        function showPreview(file) {
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `
                        <div class="preview-container">
                            <img src="${e.target.result}" class="preview-image" alt="Image preview">
                        </div>`;
                };
                reader.readAsDataURL(file);
                dropZone.style.display = 'none'; // Hide drop zone after upload
            }
        }
    </script>
</body>
</html>
