<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $product_id = $_POST['product_id'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    // Update status only if the user owns the product
    $stmt = $conn->prepare("UPDATE products SET status = ? WHERE product_id = ? AND seller_id = ?");
    $stmt->bind_param("sii", $status, $product_id, $user_id);
    
    $response = ['success' => $stmt->execute()];
    echo json_encode($response);
} 