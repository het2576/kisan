<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Set language based on GET parameter or session; default is English
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = 'en';
    $_SESSION['lang'] = $lang;
}

// Extended Translations Array for Sidebar and Marketplace
$translations = [
    'en' => [
        // Sidebar / General UI
        'dashboard'         => 'Dashboard',
        'inventory'         => 'Inventory',
        'tools'             => 'Tools',
        'profit_calc'       => 'Profit Calculator',
        'market'            => 'Market Insights',
        'weather'           => 'Weather',
        'ai'                => 'AI Assistant',
        'news'              => 'Agricultural News',
        'disease_detection' => 'Crop Disease Detection',
        'logout'            => 'Logout',
        // Marketplace Page
        'marketplace'       => 'Marketplace',
        'add_product'       => 'Add New Product',
        'product_name'      => 'Product Name',
        'description'       => 'Description',
        'category'          => 'Category',
        'price'             => 'Price per kg (₹)',
        'quantity'          => 'Quantity Available',
        'unit'              => 'Unit',
        'harvest_date'      => 'Harvest Date',
        'expiry_date'       => 'Expiry Date',
        'farming_method'    => 'Farming Method',
        'is_organic'        => 'Organic?',
        'location'          => 'Location',
        'product_image'     => 'Product Image',
        'upload_product'    => 'Upload Product',
        'no_products'       => 'No products found.',
        'buy_now'           => 'Buy Now',
        'edit'              => 'Edit',
        'delete'            => 'Delete'
    ],
    'hi' => [
        'dashboard'         => 'डैशबोर्ड',
        'inventory'         => 'इन्वेंटरी',
        'tools'             => 'उपकरण',
        'profit_calc'       => 'लाभ कैलकुलेटर',
        'market'            => 'बाजार अंतर्दृष्टि',
        'weather'           => 'मौसम',
        'ai'                => 'एआई सहायक',
        'news'              => 'कृषि समाचार',
        'disease_detection' => 'फसल रोग पहचान',
        'logout'            => 'लॉग आउट',
        'marketplace'       => 'मार्केटप्लेस',
        'add_product'       => 'नया उत्पाद जोड़ें',
        'product_name'      => 'उत्पाद का नाम',
        'description'       => 'विवरण',
        'category'          => 'वर्ग',
        'price'             => 'प्रति किग्रा मूल्य (₹)',
        'quantity'          => 'उपलब्ध मात्रा',
        'unit'              => 'इकाई',
        'harvest_date'      => 'फसल कटाई की तारीख',
        'expiry_date'       => 'समाप्ति तिथि',
        'farming_method'    => 'खेती की विधि',
        'is_organic'        => 'जैविक?',
        'location'          => 'स्थान',
        'product_image'     => 'उत्पाद छवि',
        'upload_product'    => 'उत्पाद अपलोड करें',
        'no_products'       => 'कोई उत्पाद नहीं मिले।',
        'buy_now'           => 'अभी खरीदें',
        'edit'              => 'संपादित करें',
        'delete'            => 'हटाएं'
    ],
    'gu' => [
        'dashboard'         => 'ડેશબોર્ડ',
        'inventory'         => 'ઇન્વેન્ટરી',
        'tools'             => 'સાધનો',
        'profit_calc'       => 'નફો કેલ્યુલેટર',
        'market'            => 'બજાર માહિતી',
        'weather'           => 'હવામાન',
        'ai'                => 'AI સહાયક',
        'news'              => 'કૃષિ સમાચાર',
        'disease_detection' => 'પાક રોગ શોધ',
        'logout'            => 'લૉગ આઉટ',
        'marketplace'       => 'માર્કેટપ્લેસ',
        'add_product'       => 'નવો પ્રોડક્ટ ઉમેરો',
        'product_name'      => 'પ્રોડક્ટનું નામ',
        'description'       => 'વર્ણન',
        'category'          => 'કેટેગરી',
        'price'             => 'દર કિગ્રા કિંમત (₹)',
        'quantity'          => 'ઉપલબ્ધ માત્રા',
        'unit'              => 'યુનિટ',
        'harvest_date'      => 'કાપવાની તારીખ',
        'expiry_date'       => 'અવધિ પૂર્ણ થવાની તારીખ',
        'farming_method'    => 'ખેતી પદ્ધતિ',
        'is_organic'        => 'ઓર્ગેનિક?',
        'location'          => 'સ્થાન',
        'product_image'     => 'પ્રોડક્ટ છબી',
        'upload_product'    => 'પ્રોડક્ટ અપલોડ કરો',
        'no_products'       => 'કોઈ પ્રોડક્ટ મળ્યા નથી.',
        'buy_now'           => 'હવે ખરીદો',
        'edit'              => 'એડિટ',
        'delete'            => 'ડિલીટ'
    ]
];

// Include the database connection file (ensure that db_connect.php defines $conn)
require_once 'db_connect.php';

// Fetch all products with their images, categories, and seller info
$query = "
    SELECT p.*, c.name as category_name, u.name as seller_name,
    (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN users u ON p.seller_id = u.user_id
    WHERE p.seller_id = ?
    ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// Calculate statistics
$activeListings = 0;
$totalValue = 0;
$lowStockItems = 0;

foreach ($products as $product) {
    if ($product['status'] === 'available') {
        $activeListings++;
    }
    $totalValue += ($product['price_per_kg'] * $product['quantity_available']);
    if ($product['quantity_available'] < 10) {
        $lowStockItems++;
    }
}

// Fetch categories for the add product form
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

// Determine if the logged-in user is a farmer
$isFarmer = true; // Temporarily set to true for testing

// Process the Add Product form submission (only for farmers)
$productUploadError = "";
$productUploadSuccess = "";
if ($isFarmer && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_product'])) {
    // Retrieve and sanitize inputs
    $product_name   = trim($_POST['product_name']);
    $description    = trim($_POST['description']);
    $category_id    = intval($_POST['category_id']);
    $price          = floatval($_POST['price']);
    $quantity       = floatval($_POST['quantity']);
    $unit           = $_POST['unit'];
    $harvest_date   = !empty($_POST['harvest_date']) ? $_POST['harvest_date'] : null;
    $expiry_date    = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
    $farming_method = !empty($_POST['farming_method']) ? trim($_POST['farming_method']) : null;
    $is_organic     = isset($_POST['is_organic']) ? 1 : 0;
    $location       = !empty($_POST['location']) ? trim($_POST['location']) : null;
    
    // Basic validation
    if (empty($product_name) || empty($description) || $price <= 0 || $quantity <= 0 || $category_id <= 0) {
        $productUploadError = "Please fill in all required fields correctly.";
    } elseif (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== 0) {
        $productUploadError = "Please upload a valid image file.";
    } else {
        // Create the uploads directory if it does not exist
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        // Generate a unique file name and target file path for the uploaded image
        $file_name   = time() . '_' . basename($_FILES['product_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            // Insert the new product into the Products table
            $stmt = $conn->prepare("INSERT INTO Products 
                (seller_id, category_id, name, description, price_per_kg, quantity_available, unit, harvest_date, expiry_date, farming_method, is_organic, location, image, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', NOW())");
            $stmt->execute([
                $_SESSION['user_id'],
                $category_id,
                $product_name,
                $description,
                $price,
                $quantity,
                $unit,
                $harvest_date,
                $expiry_date,
                $farming_method,
                $is_organic,
                $location,
                $target_file
            ]);
            $productUploadSuccess = "Product uploaded successfully.";
        } else {
            $productUploadError = "Error uploading file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $translations[$lang]['marketplace']; ?> - Kisan.ai</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_API_KEY&libraries=places"></script>
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
    /* Sidebar Styles (kept for reference if needed) */
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
    /* Updated Header Styles */
    .main-header {
      position: fixed;
      top: 0;
      right: 0;
      left: 0; /* Changed from 280px to 0 */
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
    /* Updated Main Content Area */
    .main-content {
      margin-left: 0; /* Changed from 280px to 0 */
      padding: 80px 1.5rem 1.5rem;
    }
    /* Marketplace Section */
    .marketplace-section {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      margin-bottom: 2rem;
    }
    .card-img-top {
      height: 200px;
      object-fit: cover;
    }
    .product-card {
      transition: transform 0.3s ease;
      height: 100%;
    }
    .product-card:hover {
      transform: translateY(-5px);
    }
    .add-product-fab {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: #2F855A;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .add-product-fab:hover {
      transform: scale(1.1);
      background: #276749;
    }
    .product-image {
      height: 200px;
      object-fit: cover;
    }
    .organic-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(47, 133, 90, 0.9);
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
    }
    /* Google Places Autocomplete Styles */
    .pac-container {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      font-family: 'Poppins', sans-serif;
      margin-top: 5px;
    }
    .pac-item {
      padding: 8px 15px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s;
    }
    .pac-item:hover {
      background-color: #f8f9fa;
    }
    .pac-item-query {
      font-size: 14px;
      color: #2D3748;
    }
    .pac-matched {
      font-weight: bold;
    }
    .pac-icon {
      margin-right: 10px;
    }
    .product-card {
      transition: transform 0.2s;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .product-image {
      height: 200px;
      object-fit: cover;
    }
    .organic-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(47, 133, 90, 0.9);
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
    }
    .delivery-options {
      margin-top: 10px;
    }
    .delivery-options .badge {
      margin-right: 5px;
    }
    .card-footer {
      background: transparent;
      border-top: 1px solid rgba(0,0,0,0.1);
    }
    .product-card {
      border: none;
      transition: transform 0.3s, box-shadow 0.3s;
      height: 100%;
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .product-image-container {
      position: relative;
      padding-top: 75%;
      overflow: hidden;
      border-radius: 8px;
    }
    .product-image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .organic-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(47, 133, 90, 0.9);
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      z-index: 1;
    }
    .price-tag {
      font-size: 1.25rem;
      font-weight: 600;
      color: #2F855A;
    }
    .location-text {
      font-size: 0.9rem;
      color: #666;
    }
    .seller-info {
      font-size: 0.85rem;
      color: #666;
    }
    .filter-section {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .filter-title {
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 10px;
      color: #333;
    }
    .dashboard-stats {
      background: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .stat-card {
      padding: 20px;
      border-radius: 8px;
      color: white;
    }
    .stat-card.primary { background: linear-gradient(45deg, #4CAF50, #45a049); }
    .stat-card.warning { background: linear-gradient(45deg, #ff9800, #f57c00); }
    .stat-card.danger { background: linear-gradient(45deg, #f44336, #e53935); }
    .stat-card.info { background: linear-gradient(45deg, #2196F3, #1976D2); }
  </style>
</head>
<body>
  <!-- Mobile Menu Overlay (if needed) -->
  <div class="mobile-menu-overlay"></div>
  
  <!-- Simple Header -->
  <header class="main-header p-3 bg-white shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
        <div class="d-flex align-items-center">
            <div class="lang-selector me-3">
                <a href="?lang=en" class="btn btn-outline-primary btn-sm">English</a>
                <a href="?lang=hi" class="btn btn-outline-primary btn-sm">हिंदी</a>
                <a href="?lang=gu" class="btn btn-outline-primary btn-sm">ગુજરાતી</a>
            </div>
            <div class="user-profile">
                <span><?php echo $_SESSION['name']; ?></span>
            </div>
        </div>
    </div>
  </header>
  
  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <h1 class="mb-4"><?php echo $translations[$lang]['marketplace']; ?></h1>
      
      <!-- Success/Error Messages -->
      <?php if (isset($_GET['success'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              Product added successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              Error: <?php echo htmlspecialchars($_GET['message'] ?? 'Unknown error'); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
      <?php endif; ?>
      <?php if (isset($_GET['update_success'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              Product updated successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
      <?php endif; ?>

      <!-- Dashboard Stats -->
      <div class="dashboard-stats">
          <div class="row">
              <div class="col-md-3">
                  <div class="stat-card primary">
                      <h3><?php echo $result->num_rows; ?></h3>
                      <p class="mb-0">Total Products</p>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="stat-card info">
                      <h3><?php echo $activeListings; ?></h3>
                      <p class="mb-0">Active Listings</p>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="stat-card warning">
                      <h3>₹<?php echo number_format($totalValue, 2); ?></h3>
                      <p class="mb-0">Total Inventory Value</p>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="stat-card danger">
                      <h3><?php echo $lowStockItems; ?></h3>
                      <p class="mb-0">Low Stock Items</p>
                  </div>
              </div>
          </div>
      </div>

      <!-- Product Management Section -->
      <div class="card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Manage Products</h5>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                  <i class="fas fa-plus"></i> Add New Product
              </button>
          </div>
          <div class="card-body">
              <!-- Filters and Search -->
              <div class="row mb-4">
                  <div class="col-md-4">
                      <input type="text" class="form-control" placeholder="Search products...">
                  </div>
                  <div class="col-md-3">
                      <select class="form-select">
                          <option value="">All Categories</option>
                          <?php 
                          $categories->data_seek(0);
                          while($category = $categories->fetch_assoc()): ?>
                              <option value="<?php echo $category['category_id']; ?>">
                                  <?php echo htmlspecialchars($category['name']); ?>
                              </option>
                          <?php endwhile; ?>
                      </select>
                  </div>
                  <div class="col-md-3">
                      <select class="form-select">
                          <option value="all">All Status</option>
                          <option value="available">Available</option>
                          <option value="sold_out">Sold Out</option>
                          <option value="removed">Removed</option>
                      </select>
                  </div>
                  <div class="col-md-2">
                      <select class="form-select">
                          <option value="newest">Newest First</option>
                          <option value="price_high">Price High</option>
                          <option value="price_low">Price Low</option>
                      </select>
                  </div>
              </div>

              <!-- Products Grid -->
              <div class="row">
                  <?php foreach ($products as $product): ?>
                  <div class="col-md-4 mb-4">
                      <div class="product-card">
                          <div class="position-relative">
                              <img src="<?php echo $product['image_url'] ?? 'assets/default-product.jpg'; ?>" 
                                   class="product-image" 
                                   alt="<?php echo htmlspecialchars($product['name']); ?>">
                              <?php if($product['is_organic']): ?>
                                  <div class="organic-badge">
                                      <i class="fas fa-leaf"></i> Organic
                                  </div>
                              <?php endif; ?>
                          </div>
                          <div class="product-details p-3">
                              <h5 class="card-title mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                              <p class="text-muted mb-2">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                              <div class="d-flex justify-content-between align-items-center mb-2">
                                  <div class="price-tag">₹<?php echo number_format($product['price_per_kg'], 2); ?>/<?php echo htmlspecialchars($product['unit']); ?></div>
                                  <span class="badge bg-<?php echo $product['status'] === 'available' ? 'success' : 'warning'; ?>">
                                      <?php echo ucfirst($product['status']); ?>
                                  </span>
                              </div>
                              <div class="product-meta mb-3">
                                  <div class="mb-1">
                                      <i class="fas fa-box me-2"></i>
                                      Stock: <?php echo $product['quantity_available']; ?> <?php echo htmlspecialchars($product['unit']); ?>
                                  </div>
                                  <?php if($product['quantity_available'] < 10): ?>
                                      <div class="stock-warning">
                                          <i class="fas fa-exclamation-triangle me-1"></i>
                                          Low stock alert!
                                      </div>
                                  <?php endif; ?>
                              </div>
                              <div class="d-flex gap-2">
                                  <!-- Edit button now carries data attributes including category -->
                                  <button class="btn btn-primary flex-grow-1 edit-button" 
                                      data-product-id="<?php echo $product['product_id']; ?>" 
                                      data-price="<?php echo $product['price_per_kg']; ?>" 
                                      data-quantity="<?php echo $product['quantity_available']; ?>" 
                                      data-status="<?php echo $product['status']; ?>"
                                      data-category="<?php echo $product['category_id']; ?>">
                                      <i class="fas fa-edit me-1"></i> Edit
                                  </button>
                                  <button class="btn btn-danger" onclick="confirmDelete(<?php echo $product['product_id']; ?>)">
                                      <i class="fas fa-trash"></i>
                                  </button>
                              </div>
                          </div>
                      </div>
                  </div>
                  <?php endforeach; ?>
              </div>
          </div>
      </div>
    </div>
  </main>
  
  <!-- Add Product Modal -->
  <div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" action="process_product.php" method="POST" enctype="multipart/form-data">
                    <!-- Product Images -->
                    <div class="mb-3">
                        <label class="form-label">Product Images</label>
                        <input type="file" class="form-control" name="product_images[]" multiple accept="image/*">
                        <small class="text-muted">You can upload up to 5 images</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" required>
                                    <?php 
                                    $categories->data_seek(0);
                                    while ($category = $categories->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $category['category_id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Price per kg</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control" name="price_per_kg" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Quantity Available</label>
                                <input type="number" step="0.01" class="form-control" name="quantity_available" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <select class="form-select" name="unit">
                                    <option value="kg">Kg</option>
                                    <option value="gram">Gram</option>
                                    <option value="piece">Piece</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harvest Date</label>
                                <input type="date" class="form-control" name="harvest_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   name="location" 
                                   placeholder="Enter your location"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_organic" value="1">
                            <label class="form-check-label">This is an organic product</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Minimum Order Quantity</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" name="min_order_quantity">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Delivery Options</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="delivery_options[]" value="pickup">
                            <label class="form-check-label">Pickup Available</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="delivery_options[]" value="delivery">
                            <label class="form-check-label">Delivery Available</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="addProductForm" class="btn btn-primary">Add Product</button>
            </div>
        </div>
    </div>
  </div>
  
  <!-- Edit Product Modal (Modified) -->
  <div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" action="update_product.php" method="POST" enctype="multipart/form-data">
                    <!-- Hidden fields to pass product ID and category ID -->
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    
                    <!-- Only allow editing Price, Quantity, and Status -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Price per kg</label>
                                <input type="number" step="0.01" class="form-control" name="price_per_kg" id="edit_price_per_kg" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Quantity Available</label>
                                <input type="number" step="0.01" class="form-control" name="quantity_available" id="edit_quantity_available" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="edit_status">
                                    <option value="available">Available</option>
                                    <option value="sold_out">Sold Out</option>
                                    <option value="removed">Hidden</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="editProductForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Form validation for Add Product
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      var requiredFields = this.querySelectorAll('[required]');
      var isValid = true;
      
      requiredFields.forEach(function(field) {
        if (!field.value) {
          isValid = false;
          field.classList.add('is-invalid');
        } else {
          field.classList.remove('is-invalid');
        }
      });

      if (isValid) {
        this.submit();
      }
    });

    // Edit Button Event Listener: Populate and Show Edit Modal
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const price = this.getAttribute('data-price');
            const quantity = this.getAttribute('data-quantity');
            const status = this.getAttribute('data-status');
            const category = this.getAttribute('data-category');
            
            document.getElementById('edit_product_id').value = productId;
            document.getElementById('edit_price_per_kg').value = price;
            document.getElementById('edit_quantity_available').value = quantity;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_category_id').value = category;
            
            var editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
            editModal.show();
        });
    });

    // Delete product functionality
    function confirmDelete(productId) {
        if(confirm('Are you sure you want to delete this product?')) {
            fetch('delete_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.text())
            .then(data => {
                location.reload();
            });
        }
    }
  </script>
</body>
</html>