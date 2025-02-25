<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in and product ID is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $product_id = $_GET['id'];
    $seller_id = $_SESSION['user_id'];
    
    // Get product details
    $stmt = $conn->prepare("
        SELECT * FROM products 
        WHERE product_id = ? AND seller_id = ?
    ");
    $stmt->bind_param("ii", $product_id, $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close(); 