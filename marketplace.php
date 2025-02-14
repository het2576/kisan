<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get language from session
$lang = $_SESSION['lang'] ?? 'en';

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
        'is_organic'        => 'Organic',
        'location'          => 'Location',
        'product_image'     => 'Product Image',
        'upload_product'    => 'Upload Product',
        'no_products'       => 'No products found.',
        'buy_now'           => 'Buy Now',
        'edit'              => 'Edit',
        'delete'            => 'Delete',
        'active_listings'   => 'Active Listings',
        'total_value'       => 'Total Inventory Value',
        'low_stock'         => 'Low Stock Items',
        'manage_products'   => 'Manage Products',
        'search_products'   => 'Search products...',
        'price_per_kg'      => 'Price per kg',
        'available'         => 'Available',
        'sold_out'          => 'Sold Out',
        'removed'           => 'Removed',
        'status'            => 'Status',
        'save_changes'      => 'Save Changes',
        'close'             => 'Close',
        'confirm_delete'    => 'Are you sure you want to delete this product?',
        'upload_up_to_5_images' => 'You can upload up to 5 images',
        'product_images'    => 'Product Images',
        'enter_location'    => 'Enter location',
        'this_is_organic_product' => 'This is an organic product',
        'minimum_order_quantity' => 'Minimum Order Quantity',
        'delivery_options'  => 'Delivery Options',
        'pickup_available'  => 'Pickup Available',
        'delivery_available' => 'Delivery Available',
        'edit_product'      => 'Edit Product',
        'all_categories'    => 'All Categories',
        'all_status'        => 'All Status',
        'sort_newest'       => 'Newest First',
        'sort_price_high'   => 'Price: High to Low',
        'sort_price_low'    => 'Price: Low to High',
        'unit_kg'           => 'Kg',
        'unit_gram'         => 'Gram',
        'unit_piece'        => 'Piece',
        'select_category'   => 'Select Category'
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
        'is_organic'        => 'जैविक',
        'location'          => 'स्थान',
        'product_image'     => 'उत्पाद छवि',
        'upload_product'    => 'उत्पाद अपलोड करें',
        'no_products'       => 'कोई उत्पाद नहीं मिले।',
        'buy_now'           => 'अभी खरीदें',
        'edit'              => 'संपादित करें',
        'delete'            => 'हटाएं',
        'active_listings'   => 'सक्रिय सूची',
        'total_value'       => 'कुल विवेचन मूल्य',
        'low_stock'         => 'कम स्टॉक वस्तुएं',
        'manage_products'   => 'उत्पाद प्रबंधन',
        'search_products'   => 'उत्पाद खोजें...',
        'price_per_kg'      => 'प्रति किलो मूल्य',
        'available'         => 'उपलब्ध',
        'sold_out'          => 'बिक गया',
        'removed'           => 'हटा दिया गया',
        'status'            => 'स्थिति',
        'save_changes'      => 'परिवर्तन सहेजें',
        'close'             => 'बंद करें',
        'confirm_delete'    => 'क्या आप वाकई इस उत्पाद को हटाना चाहते हैं?',
        'upload_up_to_5_images' => 'आप 5 तक छवियां अपलोड कर सकते हैं',
        'product_images'    => 'उत्पाद छवियां',
        'enter_location'    => 'स्थान दर्ज करें',
        'this_is_organic_product' => 'यह एक जैविक उत्पाद है',
        'minimum_order_quantity' => 'न्यूनतम ऑर्डर मात्रा',
        'delivery_options'  => 'डिलीवरी विकल्प',
        'pickup_available'  => 'पिकअप उपलब्ध',
        'delivery_available' => 'डिलीवरी उपलब्ध',
        'edit_product'      => 'उत्पाद संपादित करें',
        'all_categories'    => 'सभी वर्ग',
        'all_status'        => 'सभी स्थितियां',
        'sort_newest'       => 'नया पहला',
        'sort_price_high'   => 'मोटा मूल्य',
        'sort_price_low'    => 'कम मूल्य',
        'unit_kg'           => 'किलो',
        'unit_gram'         => 'ग्राम',
        'unit_piece'        => 'नग',
        'select_category'   => 'श्रेणी चुनें'
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
        'is_organic'        => 'ઓર્ગેનિક',
        'location'          => 'સ્થાન',
        'product_image'     => 'પ્રોડક્ટ છબી',
        'upload_product'    => 'પ્રોડક્ટ અપલોડ કરો',
        'no_products'       => 'કોઈ પ્રોડક્ટ મળ્યા નથી.',
        'buy_now'           => 'હવે ખરીદો',
        'edit'              => 'એડિટ',
        'delete'            => 'ડિલીટ',
        'active_listings'   => 'સક્રિય સૂચિ',
        'total_value'       => 'કુલ વિવેચન કિંમત',
        'low_stock'         => 'ઓછ સ્ટૉક વસ્તુઓ',
        'manage_products'   => 'પ્રોડક્ટ મેનેજમેન્ટ',
        'search_products'   => 'પ્રોડક્ટ શોધો...',
        'price_per_kg'      => 'પ્રતિ કિલો કિંમત',
        'available'         => 'ઉપલબ્ધ',
        'sold_out'          => 'વેચાઈ ગયું',
        'removed'           => 'દૂર કરાયું',
        'status'            => 'સ્થિતિ',
        'save_changes'      => 'ફેરફારો સાચવો',
        'close'             => 'બંધ કરો',
        'confirm_delete'    => 'શું તમે ખરેખર આ પ્રોડક્ટ કાઢી નાખવા માંગો છો?',
        'upload_up_to_5_images' => 'તમે 5 સુધી છબીઓ અપલોડ કરી શકો છો',
        'product_images'    => 'પ્રોડક્ટ છબીઓ',
        'enter_location'    => 'સ્થાન દાખલ કરો',
        'this_is_organic_product' => 'આ એક ઓર્ગેનિક પ્રોડક્ટ છે',
        'minimum_order_quantity' => 'ન્યૂનતમ ઓર્ડર જથ્થો',
        'delivery_options'  => 'ડિલિવરી વિકલ્પો',
        'pickup_available'  => 'પિકઅપ ઉપલબ્ધ',
        'delivery_available' => 'ડિલિવરી ઉપલબ્ધ',
        'edit_product'      => 'પ્રોડક્ટ સંપાદિત કરો',
        'all_categories'    => 'બધા વર્ગો',
        'all_status'        => 'બધી સ્થિતિયોં',
        'sort_newest'       => 'નવો પહોળો',
        'sort_price_high'   => 'વધુ કિંમત',
        'sort_price_low'    => 'ઓછ કિંમત',
        'unit_kg'           => 'કિલો',
        'unit_gram'         => 'ગ્રામ',
        'unit_piece'        => 'નંગ',
        'select_category'   => 'કેટેગરી પસંદ કરો'
    ]
];

// Add category translations
$category_translations = [
    'en' => [
        'Vegetables' => 'Vegetables',
        'Fruits' => 'Fruits',
        'Grains' => 'Grains',
        'Pulses' => 'Pulses',
        'Spices' => 'Spices',
        'Organic Products' => 'Organic Products',
        'Seeds' => 'Seeds',
        'Others' => 'Others'
    ],
    'hi' => [
        'Vegetables' => 'सब्जियां',
        'Fruits' => 'फल',
        'Grains' => 'अनाज',
        'Pulses' => 'दालें',
        'Spices' => 'मसाले',
        'Organic Products' => 'जैविक उत्पाद',
        'Seeds' => 'बीज',
        'Others' => 'अन्य'
    ],
    'gu' => [
        'Vegetables' => 'શાકભાજી',
        'Fruits' => 'ફળો',
        'Grains' => 'અનાજ',
        'Pulses' => 'કઠોળ',
        'Spices' => 'મસાલા',
        'Organic Products' => 'જૈવિક ઉત્પાદનો',
        'Seeds' => 'બીજ',
        'Others' => 'અન્ય'
    ]
];

// Function to translate category name
function translateCategory($categoryName, $lang, $translations) {
    return $translations[$lang][$categoryName] ?? $categoryName;
}

// Include the database connection file (ensure that db_connect.php defines $conn)
require_once 'db_connect.php';

// Update the query to ensure quantity_available is always set
$query = "
    SELECT 
        p.product_id,
        p.name,
        p.description,
        p.price_per_kg,
        COALESCE(p.quantity_available, 0) as quantity_available,
        p.unit,
        p.status,
        p.category_id,
        p.is_organic,
        c.name as category_name,
        u.name as seller_name,
        u.phone_number as seller_phone,
        (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN users u ON p.seller_id = u.user_id
    WHERE p.seller_id = ?
    ORDER BY p.created_at DESC
";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Initialize products array
    $products = [];
    
    // Process each row with proper initialization
    while ($row = $result->fetch_assoc()) {
        // Ensure quantity_available is set and is numeric
        $row['quantity_available'] = is_null($row['quantity_available']) ? 0 : floatval($row['quantity_available']);
        $products[] = $row;
    }

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $products = [];
}

// Update the display section where quantity is shown
foreach ($products as $key => $product) {
    $products[$key]['quantity_available'] = floatval($product['quantity_available'] ?? 0);
}

// Calculate statistics
$activeListings = 0;
$totalValue = 0;
$lowStockItems = 0;

foreach ($products as $product) {
    if ($product['status'] === 'available') {
        $activeListings++;
    }
    $quantity = isset($product['quantity_available']) ? $product['quantity_available'] : 0;
    $totalValue += ($product['price_per_kg'] * $quantity);
    if ($quantity < 10) {
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
        --accent-color: #F6E05E;
        --success-color: #48BB78;
        --warning-color: #ED8936;
        --danger-color: #E53E3E;
        --text-dark: #2D3748;
        --text-light: #718096;
        --border-color: #E2E8F0;
        --bg-light: #F9FAFB;
    }

    body {
        background: white;
        font-family: 'Poppins', sans-serif;
    }

    .marketplace-title {
        color: var(--primary-color);
        font-size: 2.5rem;
        font-weight: 700;
        margin: 2rem 0;
        text-align: center;
        position: relative;
    }

    .marketplace-title:after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: var(--primary-color);
        margin: 1rem auto;
        border-radius: 2px;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.1;
        z-index: 0;
    }

    .stat-card.active-listings {
        background: linear-gradient(145deg, #ffffff, #f0f9f5);
        border-left: 4px solid var(--primary-color);
    }

    .stat-card.total-value {
        background: linear-gradient(145deg, #ffffff, #ebf8ff);
        border-left: 4px solid #3182CE;
    }

    .stat-card.low-stock {
        background: linear-gradient(145deg, #ffffff, #fff5f5);
        border-left: 4px solid var(--warning-color);
    }

    .stat-card h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .stat-card.active-listings h3 { color: var(--primary-color); }
    .stat-card.total-value h3 { color: #3182CE; }
    .stat-card.low-stock h3 { color: var(--warning-color); }

    .stat-card p {
        color: var(--text-dark);
        font-size: 1rem;
        margin: 0;
        position: relative;
        z-index: 1;
        font-weight: 500;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        padding: 1rem;
    }

    .product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
    }

    .product-card:hover {
        transform: none;
    }

    .product-image-container {
        position: relative;
        padding-top: 75%;
        overflow: hidden;
        background: var(--bg-light);
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
        top: 1rem;
        right: 1rem;
        background: rgba(47, 133, 90, 0.95);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .product-details {
        padding: 1.5rem;
    }

    .product-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
    }

    .price-tag {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-badge.available {
        background-color: rgba(72, 187, 120, 0.1);
        color: var(--success-color);
    }

    .status-badge.sold-out {
        background-color: rgba(237, 137, 54, 0.1);
        color: var(--warning-color);
    }

    .product-meta {
        color: var(--text-light);
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .product-meta i {
        color: var(--primary-color);
    }

    .card {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        background: white;
    }

    .card:hover {
        transform: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid var(--border-color);
        padding: 1.5rem;
    }

    .filters-section {
        background: var(--bg-light);
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }

    .form-control, .form-select {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        transition: none;
    }

    .form-control:hover, .form-select:hover {
        transform: none;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(47, 133, 90, 0.1);
        transform: none;
    }

    .btn {
        transition: none;
    }

    .btn:hover {
        transform: none;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }

    .btn-primary:hover {
        background-color: var(--secondary-color);
    }

    .btn-danger {
        transition: none;
    }

    .btn-danger:hover {
        transform: none;
    }

    .modal-content {
        border-radius: 16px;
        border: none;
    }

    .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 1.5rem;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-body {
        padding: 2rem;
        color: var(--text-dark);
    }

    .form-label {
        color: var(--text-dark);
        font-weight: 500;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }

    .form-text, .text-muted {
        color: var(--text-light) !important;
        font-size: 0.875rem;
    }

    .input-group-text {
        background-color: var(--bg-light);
        border-color: var(--border-color);
        color: var(--text-light);
    }

    .form-check-label {
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 1.5rem;
    }

    .form-select {
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    .form-select option {
        color: var(--text-dark);
        padding: 8px;
    }

    @media (max-width: 768px) {
        .marketplace-title {
            font-size: 2rem;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .stat-card {
            padding: 1.5rem;
        }
    }
  </style>
</head>
<body>
  <!-- Mobile Menu Overlay (if needed) -->
  <div class="mobile-menu-overlay"></div>
  
  <!-- Simple Header -->
  <header class="main-header p-3 bg-white shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i><?php echo $translations[$lang]['dashboard']; ?>
        </a>
        <div class="d-flex align-items-center">
            <div class="user-profile">
                <span><?php echo $_SESSION['name']; ?></span>
            </div>
        </div>
    </div>
  </header>
  
  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <h1 class="marketplace-title">
          <i class="fas fa-store me-2"></i>
          <?php echo $translations[$lang]['marketplace']; ?>
      </h1>
      
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
      <div class="stats-container">
          <div class="stat-card active-listings">
              <h3><?php echo $activeListings; ?></h3>
              <p class="mb-0"><?php echo $translations[$lang]['active_listings']; ?></p>
          </div>
          <div class="stat-card total-value">
              <h3>₹<?php echo number_format($totalValue, 2); ?></h3>
              <p class="mb-0"><?php echo $translations[$lang]['total_value']; ?></p>
          </div>
          <div class="stat-card low-stock">
              <h3><?php echo $lowStockItems; ?></h3>
              <p class="mb-0"><?php echo $translations[$lang]['low_stock']; ?></p>
          </div>
      </div>

      <!-- Product Management Section -->
      <div class="card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><?php echo $translations[$lang]['manage_products']; ?></h5>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                  <i class="fas fa-plus"></i> <?php echo $translations[$lang]['add_product']; ?>
              </button>
          </div>
          <div class="card-body">
              <!-- Filters and Search -->
              <div class="row mb-4">
                  <div class="col-md-4">
                      <input type="text" class="form-control" placeholder="<?php echo $translations[$lang]['search_products']; ?>">
                  </div>
                  <div class="col-md-3">
                      <select class="form-select">
                          <option value=""><?php echo $translations[$lang]['all_categories']; ?></option>
                          <?php 
                          if ($categories) {
                              $categories->data_seek(0);
                              while($category = $categories->fetch_assoc()): 
                                  $translated_name = translateCategory($category['name'], $lang, $category_translations);
                              ?>
                                  <option value="<?php echo $category['category_id']; ?>">
                                      <?php echo htmlspecialchars($translated_name); ?>
                                  </option>
                              <?php endwhile;
                          }
                          ?>
                      </select>
                  </div>
                  <div class="col-md-3">
                      <select class="form-select">
                          <option value="all"><?php echo $translations[$lang]['all_status']; ?></option>
                          <option value="available"><?php echo $translations[$lang]['available']; ?></option>
                          <option value="sold_out"><?php echo $translations[$lang]['sold_out']; ?></option>
                          <option value="removed"><?php echo $translations[$lang]['removed']; ?></option>
                      </select>
                  </div>
                  <div class="col-md-2">
                      <select class="form-select">
                          <option value="newest"><?php echo $translations[$lang]['sort_newest']; ?></option>
                          <option value="price_high"><?php echo $translations[$lang]['sort_price_high']; ?></option>
                          <option value="price_low"><?php echo $translations[$lang]['sort_price_low']; ?></option>
                      </select>
                  </div>
              </div>

              <!-- Products Grid -->
              <div class="product-grid">
                  <?php foreach ($products as $product): ?>
                  <div class="product-card">
                      <div class="product-image-container">
                          <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'assets/default-product.jpg'); ?>" 
                               class="product-image" 
                               alt="<?php echo htmlspecialchars($product['name']); ?>">
                          <?php if($product['is_organic']): ?>
                              <div class="organic-badge">
                                  <i class="fas fa-leaf me-1"></i>
                                  <?php echo $translations[$lang]['is_organic']; ?>
                              </div>
                          <?php endif; ?>
                      </div>
                      <div class="product-details p-3">
                          <h5 class="card-title mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                          <p class="text-muted mb-2">
                              <?php echo $translations[$lang]['category']; ?>: 
                              <?php 
                              $translated_category = translateCategory($product['category_name'], $lang, $category_translations);
                              echo htmlspecialchars($translated_category); 
                              ?>
                          </p>
                          <div class="d-flex justify-content-between align-items-center mb-2">
                              <div class="price-tag">₹<?php echo number_format($product['price_per_kg'], 2); ?>/<?php echo htmlspecialchars($product['unit'] ?? 'kg'); ?></div>
                              <span class="status-badge <?php echo $product['status'] === 'available' ? 'available' : 'sold-out'; ?>">
                                  <?php echo $translations[$lang][$product['status'] ?? 'available']; ?>
                              </span>
                          </div>
                          <div class="product-meta mb-3">
                              <div class="mb-1">
                                  <i class="fas fa-box me-2"></i>
                                  <?php echo $translations[$lang]['quantity']; ?>: 
                                  <?php 
                                  $quantity = floatval($product['quantity_available'] ?? 0);
                                  echo number_format($quantity, 2); 
                                  ?> 
                                  <?php echo htmlspecialchars($product['unit'] ?? 'kg'); ?>
                              </div>
                          </div>
                          <div class="d-flex gap-2">
                              <button class="btn btn-primary flex-grow-1 edit-button" 
                                      data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>"
                                      data-price="<?php echo htmlspecialchars(number_format($product['price_per_kg'], 2)); ?>"
                                      data-quantity="<?php echo htmlspecialchars(number_format(floatval($product['quantity_available'] ?? 0), 2)); ?>"
                                      data-status="<?php echo htmlspecialchars($product['status'] ?? 'available'); ?>"
                                      data-category="<?php echo htmlspecialchars($product['category_id']); ?>"
                                      data-bs-toggle="modal" 
                                      data-bs-target="#editProductModal">
                                  <i class="fas fa-edit me-1"></i> <?php echo $translations[$lang]['edit']; ?>
                              </button>
                              <button class="btn btn-danger" onclick="confirmDelete(<?php echo $product['product_id']; ?>)">
                                  <i class="fas fa-trash"></i> <?php echo $translations[$lang]['delete']; ?>
                              </button>
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
                <h5 class="modal-title"><?php echo $translations[$lang]['add_product']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" action="process_product.php" method="POST" enctype="multipart/form-data">
                    <!-- Product Images -->
                    <div class="mb-3">
                        <label class="form-label"><?php echo $translations[$lang]['product_images']; ?></label>
                        <input type="file" class="form-control" name="product_images[]" multiple accept="image/*">
                        <small class="text-muted"><?php echo $translations[$lang]['upload_up_to_5_images']; ?></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $translations[$lang]['product_name']; ?></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $translations[$lang]['category']; ?></label>
                                <select class="form-select" name="category_id" required>
                                    <option value=""><?php echo $translations[$lang]['select_category']; ?></option>
                                    <?php 
                                    if ($categories) {
                                        $categories->data_seek(0);
                                        while ($category = $categories->fetch_assoc()): 
                                            $translated_name = translateCategory($category['name'], $lang, $category_translations);
                                        ?>
                                            <option value="<?php echo $category['category_id']; ?>">
                                                <?php echo htmlspecialchars($translated_name); ?>
                                            </option>
                                        <?php endwhile;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $translations[$lang]['price_per_kg']; ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control" name="price_per_kg" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $translations[$lang]['quantity']; ?></label>
                                <input type="number" step="0.01" class="form-control" name="quantity_available" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $translations[$lang]['unit']; ?></label>
                                <select class="form-select" name="unit">
                                    <option value="kg"><?php echo $translations[$lang]['unit_kg']; ?></option>
                                    <option value="gram"><?php echo $translations[$lang]['unit_gram']; ?></option>
                                    <option value="piece"><?php echo $translations[$lang]['unit_piece']; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $translations[$lang]['harvest_date']; ?></label>
                                <input type="date" class="form-control" name="harvest_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $translations[$lang]['expiry_date']; ?></label>
                                <input type="date" class="form-control" name="expiry_date">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo $translations[$lang]['location']; ?></label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   name="location" 
                                   placeholder="<?php echo $translations[$lang]['enter_location']; ?>"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_organic" value="1">
                            <label class="form-check-label">
                                <i class="fas fa-leaf text-success me-1"></i>
                                <?php echo $translations[$lang]['is_organic']; ?>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo $translations[$lang]['minimum_order_quantity']; ?></label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" name="min_order_quantity">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo $translations[$lang]['delivery_options']; ?></label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="delivery_options[]" value="pickup">
                            <label class="form-check-label"><?php echo $translations[$lang]['pickup_available']; ?></label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="delivery_options[]" value="delivery">
                            <label class="form-check-label"><?php echo $translations[$lang]['delivery_available']; ?></label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $translations[$lang]['close']; ?></button>
                <button type="submit" form="addProductForm" class="btn btn-primary"><?php echo $translations[$lang]['add_product']; ?></button>
            </div>
        </div>
    </div>
  </div>
  
  <!-- Edit Product Modal (Modified) -->
  <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo isset($translations[$lang]['edit_product']) ? $translations[$lang]['edit_product'] : 'Edit Product'; ?></h5>
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
                                <label class="form-label"><?php echo isset($translations[$lang]['price_per_kg']) ? $translations[$lang]['price_per_kg'] : 'Price per kg'; ?></label>
                                <input type="number" step="0.01" class="form-control" name="price_per_kg" id="edit_price_per_kg" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><?php echo isset($translations[$lang]['quantity_available']) ? $translations[$lang]['quantity_available'] : 'Quantity Available'; ?></label>
                                <input type="number" step="0.01" class="form-control" name="quantity_available" id="edit_quantity_available" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label"><?php echo isset($translations[$lang]['status']) ? $translations[$lang]['status'] : 'Status'; ?></label>
                                <select class="form-select" name="status" id="edit_status">
                                    <option value="available"><?php echo isset($translations[$lang]['available']) ? $translations[$lang]['available'] : 'Available'; ?></option>
                                    <option value="sold_out"><?php echo isset($translations[$lang]['sold_out']) ? $translations[$lang]['sold_out'] : 'Sold Out'; ?></option>
                                    <option value="removed"><?php echo isset($translations[$lang]['removed']) ? $translations[$lang]['removed'] : 'Removed'; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo isset($translations[$lang]['close']) ? $translations[$lang]['close'] : 'Close'; ?></button>
                <button type="submit" form="editProductForm" class="btn btn-primary"><?php echo isset($translations[$lang]['save_changes']) ? $translations[$lang]['save_changes'] : 'Save Changes'; ?></button>
            </div>
        </div>
    </div>
</div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));

        // Cache DOM elements
        const addProductForm = document.getElementById('addProductForm');
        const editProductForm = document.getElementById('editProductForm');
        const editModal = document.getElementById('editProductModal');
        
        // Initialize Bootstrap modal
        const editModalInstance = new bootstrap.Modal(editModal);

        // Form validation for Add Product
        if (addProductForm) {
            addProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const requiredFields = this.querySelectorAll('[required]');
                const isValid = Array.from(requiredFields).every(field => {
                    const isFieldValid = field.value.trim() !== '';
                    field.classList.toggle('is-invalid', !isFieldValid);
                    return isFieldValid;
                });

                if (isValid) {
                    this.submit();
                }
            });
        }

        // Edit Button Event Listeners
        const editButtons = document.querySelectorAll('.edit-button');
        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get data with defaults
                const data = {
                    productId: this.dataset.productId || '0',
                    price: this.dataset.price || '0.00',
                    quantity: this.dataset.quantity || '0.00',
                    status: this.dataset.status || 'available',
                    category: this.dataset.category || '0'
                };

                // Set form values
                document.getElementById('edit_product_id').value = data.productId;
                document.getElementById('edit_price_per_kg').value = parseFloat(data.price).toFixed(2);
                document.getElementById('edit_quantity_available').value = parseFloat(data.quantity).toFixed(2);
                document.getElementById('edit_status').value = data.status;
                document.getElementById('edit_category_id').value = data.category;
            });
        });

        // Handle modal cleanup on close
        editModal.addEventListener('hidden.bs.modal', function () {
            // Reset form
            editProductForm.reset();
            // Remove any validation classes
            const fields = editProductForm.querySelectorAll('.form-control, .form-select');
            fields.forEach(field => {
                field.classList.remove('is-invalid');
            });
        });

        // Handle form submission
        if (editProductForm) {
            editProductForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const formData = new FormData(this);
                    const response = await fetch('update_product.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        // Close modal
                        editModalInstance.hide();
                        // Reload page after successful update
                        window.location.reload();
                    } else {
                        throw new Error('Update failed');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to update product. Please try again.');
                }
            });
        }

        // Delete product functionality
        window.confirmDelete = function(productId) {
            if (confirm(document.querySelector('[data-delete-confirm]').dataset.deleteConfirm)) {
                fetch('delete_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('Delete failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete product. Please try again.');
                });
            }
        };

        // Add data attribute for delete confirmation message
        document.body.setAttribute('data-delete-confirm', 
            '<?php echo addslashes($translations[$lang]['confirm_delete']); ?>');
    });
  </script>
  <script>
$(document).ready(function() {
    // Initialize filters
    const categorySelect = $('select:contains("All Categories")');
    const statusSelect = $('select:contains("All Status")');
    const sortSelect = $('select:contains("Sort")');
    const searchInput = $('input[placeholder*="Search"]');

    // Add IDs to the selects for easier reference
    categorySelect.attr('id', 'categoryFilter');
    statusSelect.attr('id', 'statusFilter');
    sortSelect.attr('id', 'sortFilter');
    searchInput.attr('id', 'searchInput');

    // Handle all filter changes
    $('#categoryFilter, #statusFilter, #sortFilter').change(function() {
        filterProducts();
    });

    // Handle search input with debounce
    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterProducts, 500);
    });

    function filterProducts() {
        const category = $('#categoryFilter').val();
        const status = $('#statusFilter').val();
        const sort = $('#sortFilter').val();
        const search = $('#searchInput').val();

        // Add loading state
        $('.product-grid').addClass('loading');
        
        // Make AJAX call
        $.ajax({
            url: 'filter_products.php',
            method: 'POST',
            data: {
                category: category,
                status: status,
                sort: sort,
                search: search
            },
            success: function(response) {
                $('.product-grid').html(response).removeClass('loading');
                // Reinitialize any necessary event handlers
                initializeProductHandlers();
            },
            error: function(xhr, status, error) {
                console.error('Error filtering products:', error);
                $('.product-grid').removeClass('loading');
                alert('Error filtering products. Please try again.');
            }
        });
    }

    function initializeProductHandlers() {
        // Reinitialize edit buttons
        $('.edit-button').click(function() {
            const productId = $(this).data('product-id');
            const price = $(this).data('price');
            const quantity = $(this).data('quantity');
            const status = $(this).data('status');
            const category = $(this).data('category');

            // Populate edit modal
            $('#editProductModal').find('#edit_product_id').val(productId);
            $('#editProductModal').find('#edit_price_per_kg').val(price);
            $('#editProductModal').find('#edit_quantity_available').val(quantity);
            $('#editProductModal').find('#edit_status').val(status);
            $('#editProductModal').find('#edit_category_id').val(category);
        });

        // Reinitialize delete buttons
        $('.delete-button').click(function() {
            if (confirm('Are you sure you want to delete this product?')) {
                const productId = $(this).data('product-id');
                deleteProduct(productId);
            }
        });
    }
});
</script>

<style>
/* Add loading state styles */
.product-grid.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.product-grid.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #4CAF50;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Improve dropdown styles */
.form-select {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.form-select:hover {
    border-color: #4CAF50;
}

.form-select:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.1);
}

/* Improve search input */
.form-control {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.1);
}
</style>
</body>
</html>