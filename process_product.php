<?php
session_start();
require_once 'db_connect.php';

// Define log file path
define('LOG_FILE', __DIR__ . '/logs/debug.log');

// Create logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0777, true);
}

// Function to log messages
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "$timestamp - $message\n";
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    logMessage("Unauthorized access attempt");
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve and sanitize inputs
        $product_name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $price = floatval($_POST['price_per_kg'] ?? 0);
        $quantity = floatval($_POST['quantity_available'] ?? 0);
        $unit = $_POST['unit'] ?? 'kg';
        $harvest_date = !empty($_POST['harvest_date']) ? $_POST['harvest_date'] : null;
        $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
        $farming_method = !empty($_POST['farming_method']) ? trim($_POST['farming_method']) : null;
        $is_organic = isset($_POST['is_organic']) ? 1 : 0;
        $location = !empty($_POST['location']) ? trim($_POST['location']) : null;

        // Validation
        $errors = [];
        if (empty($product_name)) $errors[] = "Product name is required";
        if ($category_id <= 0) $errors[] = "Valid category is required";
        if ($price <= 0) $errors[] = "Valid price is required";
        if ($quantity <= 0) $errors[] = "Valid quantity is required";

        if (!empty($errors)) {
            logMessage("Validation errors: " . implode(", ", $errors));
            $_SESSION['error'] = implode("<br>", $errors);
            header('Location: marketplace.php');
            exit();
        }

        // Handle image upload
        $image_urls = [];
        if (isset($_FILES['product_images'])) {
            $upload_dir = 'uploads/products/';
            
            // Create upload directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Handle multiple images
            $files = $_FILES['product_images'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === 0) {
                    $file_name = time() . '_' . basename($files['name'][$i]);
                    $target_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($files['tmp_name'][$i], $target_path)) {
                        $image_urls[] = $target_path;
                    } else {
                        logMessage("Failed to upload image: " . $files['name'][$i]);
                    }
                }
            }
        }

        // Begin transaction
        $conn->begin_transaction();

        // Insert product
        $stmt = $conn->prepare("INSERT INTO products (seller_id, category_id, name, description, 
            price_per_kg, quantity_available, unit, harvest_date, expiry_date, 
            farming_method, is_organic, location) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("iissddsssssi", 
            $_SESSION['user_id'], $category_id, $product_name, $description,
            $price, $quantity, $unit, $harvest_date, $expiry_date,
            $farming_method, $is_organic, $location
        );

        if (!$stmt->execute()) {
            throw new Exception("Error inserting product: " . $stmt->error);
        }

        $product_id = $conn->insert_id;

        // Insert product images
        if (!empty($image_urls)) {
            $image_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, ?)");
            
            foreach ($image_urls as $index => $url) {
                $is_primary = ($index === 0) ? 1 : 0;
                $image_stmt->bind_param("isi", $product_id, $url, $is_primary);
                
                if (!$image_stmt->execute()) {
                    throw new Exception("Error inserting product image: " . $image_stmt->error);
                }
            }
        }

        // Commit transaction
        $conn->commit();

        logMessage("Product added successfully: ID = $product_id");
        $_SESSION['success'] = "Product added successfully!";
        header('Location: marketplace.php');
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        logMessage("Error: " . $e->getMessage());
        $_SESSION['error'] = "Error adding product: " . $e->getMessage();
        header('Location: marketplace.php');
        exit();
    }
} else {
    logMessage("Invalid request method");
    header('Location: marketplace.php');
    exit();
}
?> 