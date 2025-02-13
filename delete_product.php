<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Check if product_id is provided
if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

try {
    $product_id = $_POST['product_id'];
    $seller_id = $_SESSION['user_id'];

    // Delete the product
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $product_id, $seller_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found or unauthorized']);
        }
    } else {
        throw new Exception("Error deleting product");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close(); 