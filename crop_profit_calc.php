<?php
session_start();
require_once 'db_connect.php';  // Include your database connection if needed

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

// Disease detection database to map Python prediction to extra details
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

// Clear results on page load (when not POST)
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    unset($_SESSION['detection_result']);
    unset($_SESSION['uploaded_image']);
}

// Handle image upload and disease detection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["cropImage"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["cropImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is a valid image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["cropImage"]["tmp_name"]);
        if ($check !== false) {
            // Additional check: verify the image appears to be of a plant (green-dominant)
            $image = imagecreatefromstring(file_get_contents($_FILES["cropImage"]["tmp_name"]));
            $width = imagesx($image);
            $height = imagesy($image);
            $greenPixels = 0;
            $totalPixels = 0;
            for ($x = 0; $x < $width; $x += 4) {
                for ($y = 0; $y < $height; $y += 4) {
                    $rgb = imagecolorat($image, $x, $y);
                    $colors = imagecolorsforindex($image, $rgb);
                    if ($colors['green'] > $colors['red'] && $colors['green'] > $colors['blue']) {
                        $greenPixels++;
                    }
                    $totalPixels++;
                }
            }
            $greenPercentage = ($greenPixels / $totalPixels) * 100;
            if ($greenPercentage < 15) {
                $error = $translations[$lang]['error_not_crop'];
                $uploadOk = 0;
            }
            imagedestroy($image);
        } else {
            $error = $translations[$lang]['error_not_image'];
            $uploadOk = 0;
        }
    }

    // Check file size limit (5 MB)
    if ($_FILES["cropImage"]["size"] > 5000000) {
        $error = $translations[$lang]['error_large_file'];
        $uploadOk = 0;
    }

    // Allow only specific image formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $error = $translations[$lang]['error_file_type'];
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        $error = isset($error) ? $error : $translations[$lang]['error_upload'];
    } else {
        if (move_uploaded_file($_FILES["cropImage"]["tmp_name"], $target_file)) {
            // Call the Python script for prediction.
            // Adjust the path below to the actual location of your predict.py file.
            $pythonScript = '/path/to/predict.py';
            $command = escapeshellcmd("python3 " . $pythonScript . " " . escapeshellarg($target_file));
            $pythonOutput = shell_exec($command);
            $predictedDisease = trim($pythonOutput);

            // Map the predicted disease to our database details
            if (array_key_exists($predictedDisease, $disease_database)) {
                $_SESSION['detection_result'] = $disease_database[$predictedDisease];
            } else {
                // If the prediction is unknown, set a default response
                $_SESSION['detection_result'] = [
                    'name' => [
                        'en' => ucfirst($predictedDisease),
                        'hi' => ucfirst($predictedDisease),
                        'gu' => ucfirst($predictedDisease)
                    ],
                    'confidence' => 80,
                    'treatment' => [
                        'en' => 'No treatment available',
                        'hi' => 'कोई उपचार उपलब्ध नहीं है',
                        'gu' => 'કોઈ સારવાર ઉપલબ્ધ નથી'
                    ],
                    'image' => 'images/diseases/default.jpg'
                ];
            }
            $_SESSION['uploaded_image'] = $target_file;
        } else {
            $error = $translations[$lang]['error_upload'];
        }
    }
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
    /* CSS styles omitted for brevity.
       Use your existing styles or customize as needed. */
    :root {
      --primary-color: #2F855A;
      --secondary-color: #276749;
      --accent-color: #C6F6D5;
      --background-color: #F0FFF4;
      --text-color: #234E52;
      --border-color: #9AE6B4;
      --card-bg: rgba(255, 255, 255, 0.95);
    }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--background-color);
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
    }
    .main-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 5rem 2rem;
    }
    .upload-section, .result-section {
      background: var(--card-bg);
      border-radius: 24px;
      padding: 3rem;
      box-shadow: 0 20px 40px rgba(47,133,90,0.1);
      border: 1px solid var(--border-color);
    }
    .upload-zone {
      border: 2px dashed var(--primary-color);
      border-radius: 20px;
      padding: 4rem 2rem;
      text-align: center;
      cursor: pointer;
      background: var(--accent-color);
    }
    .preview-image {
      max-width: 100%;
      max-height: 400px;
      border-radius: 20px;
      margin-bottom: 1rem;
    }
    .disease-card {
      border-radius: 20px;
      padding: 2rem;
      background: #fff;
      margin-top: 2rem;
    }
    .btn-detect {
      background: var(--primary-color);
      border: none;
      padding: 1.2rem 2rem;
      color: #fff;
      border-radius: 12px;
      width: 100%;
      margin-top: 2rem;
      text-transform: uppercase;
    }
  </style>
</head>
<body>
  <a href="dashboard.php" class="back-button">
    <i class="fas fa-arrow-left me-2"></i> <?php echo $translations[$lang]['back_to_dashboard']; ?>
  </a>
  <div class="main-container">
    <?php if(isset($error)): ?>
      <div class="alert alert-danger" role="alert">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>
    <div class="row">
      <div class="col-lg-6">
        <div class="upload-section">
          <h2><?php echo $translations[$lang]['disease_detection']; ?></h2>
          <form method="POST" enctype="multipart/form-data">
            <div class="upload-zone" id="dropZone">
              <i class="fas fa-cloud-upload-alt"></i>
              <h4><?php echo $translations[$lang]['upload_image']; ?></h4>
              <p>Drag and drop your image here or click to browse</p>
              <input type="file" class="d-none" id="cropImage" name="cropImage" accept="image/*" required>
            </div>
            <div id="imagePreview"></div>
            <button type="submit" class="btn-detect" name="submit">
              <i class="fas fa-microscope me-2"></i><?php echo $translations[$lang]['detect']; ?>
            </button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="result-section">
          <div class="text-center">
            <i class="fas fa-leaf"></i>
            <h3><?php echo $translations[$lang]['results']; ?></h3>
          </div>
          <?php if(isset($_SESSION['detection_result']) && isset($_SESSION['uploaded_image'])): ?>
            <img src="<?php echo $_SESSION['uploaded_image']; ?>" class="preview-image" alt="Uploaded crop">
            <div class="disease-card">
              <h5><?php echo $translations[$lang]['detected_disease']; ?></h5>
              <h3>
                <?php echo $_SESSION['detection_result']['name'][$lang] ?? ''; ?>
              </h3>
              <?php
              $confidence = $_SESSION['detection_result']['confidence'] ?? 0;
              $severity = ($confidence >= 90) ? 'High Severity' : (($confidence >= 70) ? 'Medium Severity' : 'Low Severity');
              ?>
              <p><?php echo $translations[$lang]['confidence']; ?> <?php echo $confidence; ?>%</p>
              <div class="treatment-card">
                <h6><?php echo $translations[$lang]['treatment']; ?></h6>
                <p><?php echo $_SESSION['detection_result']['treatment'][$lang] ?? ''; ?></p>
              </div>
            </div>
          <?php else: ?>
            <div class="empty-result-state">
              <i class="fas fa-microscope"></i>
              <p><?php echo $translations[$lang]['no_results']; ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <script>
    // Drag and drop image upload functionality
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('cropImage');
    const preview = document.getElementById('imagePreview');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, e => {
        e.preventDefault();
        e.stopPropagation();
      }, false);
    });
    
    ['dragenter', 'dragover'].forEach(eventName => {
      dropZone.addEventListener(eventName, () => dropZone.classList.add('bg-light'), false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, () => dropZone.classList.remove('bg-light'), false);
    });
    
    dropZone.addEventListener('click', () => fileInput.click());
    
    dropZone.addEventListener('drop', e => {
      const dt = e.dataTransfer;
      const files = dt.files;
      fileInput.files = files;
      showPreview(files[0]);
    });
    
    fileInput.addEventListener('change', e => {
      showPreview(e.target.files[0]);
    });
    
    function showPreview(file) {
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          preview.innerHTML = `<img src="${e.target.result}" class="preview-image">`;
        };
        reader.readAsDataURL(file);
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
