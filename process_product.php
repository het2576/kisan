<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seller_id = $_SESSION['user_id'];
    
    // Basic product data
    $data = [
        'seller_id' => $seller_id,
        'category_id' => $_POST['category_id'],
        'name' => $_POST['name'],
        'price_per_kg' => $_POST['price_per_kg'],
        'quantity_available' => $_POST['quantity_available'],
        'unit' => $_POST['unit'],
        'harvest_date' => $_POST['harvest_date'] ?: null,
        'expiry_date' => $_POST['expiry_date'] ?: null,
        'is_organic' => isset($_POST['is_organic']) ? 1 : 0,
        'location' => $_POST['location'],
        'min_order_quantity' => $_POST['min_order_quantity'] ?: null,
        'delivery_options' => isset($_POST['delivery_options']) ? implode(',', $_POST['delivery_options']) : null,
        'status' => 'available'
    ];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert new product
        $sql = "INSERT INTO products (
            seller_id, category_id, name, price_per_kg, 
            quantity_available, unit, harvest_date, expiry_date,
            is_organic, location, min_order_quantity, delivery_options, 
            status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiisdsssissss",
            $data['seller_id'],
            $data['category_id'],
            $data['name'],
            $data['price_per_kg'],
            $data['quantity_available'],
            $data['unit'],
            $data['harvest_date'],
            $data['expiry_date'],
            $data['is_organic'],
            $data['location'],
            $data['min_order_quantity'],
            $data['delivery_options'],
            $data['status']
        );
        
        $stmt->execute();
        $product_id = $conn->insert_id;
        
        // Handle image uploads
        if (!empty($_FILES['product_images']['name'][0])) {
            $upload_dir = 'uploads/products/';
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['product_images']['error'][$key] === 0) {
                    $file_name = $_FILES['product_images']['name'][$key];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $new_file_name = uniqid() . '.' . $file_ext;
                    $target_file = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        // Set the first image as primary
                        $is_primary = ($key === 0) ? 1 : 0;
                        
                        $sql = "INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("isi", $product_id, $target_file, $is_primary);
                        $stmt->execute();
                    }
                }
            }
        }
        
        // Debug: Check if product was inserted
        $debug_stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $debug_stmt->bind_param("i", $product_id);
        $debug_stmt->execute();
        $debug_result = $debug_stmt->get_result();
        $debug_product = $debug_result->fetch_assoc();
        
        // Write to a log file
        $log_file = 'debug_log.txt';
        $log_message = date('Y-m-d H:i:s') . " - Added product: " . print_r($debug_product, true) . "\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
        
        $conn->commit();
        header('Location: marketplace.php?success=1');
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
        header('Location: marketplace.php?error=1&message=' . urlencode($e->getMessage()));
        exit();
    }
} 