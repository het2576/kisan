<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $product_id = $_POST['product_id'];
        $seller_id = $_SESSION['user_id'];
        
        // Verify product belongs to seller
        $check = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND seller_id = ?");
        $check->bind_param("ii", $product_id, $seller_id);
        $check->execute();
        
        if ($check->get_result()->num_rows === 0) {
            throw new Exception('Unauthorized access');
        }
        
        // Update only price, quantity and status
        $stmt = $conn->prepare("
            UPDATE products SET 
                price_per_kg = ?,
                quantity_available = ?,
                status = ?
            WHERE product_id = ? AND seller_id = ?
        ");
        
        $stmt->bind_param(
            "ddsii",
            $_POST['price_per_kg'],
            $_POST['quantity_available'],
            $_POST['status'],
            $product_id,
            $seller_id
        );
        
        if ($stmt->execute()) {
            header("Location: marketplace.php?update_success=1");
            exit();
        } else {
            throw new Exception('Error updating product');
        }
        
    } catch (Exception $e) {
        header("Location: marketplace.php?update_error=" . urlencode($e->getMessage()));
        exit();
    }
}

// If we get here, something went wrong
header("Location: marketplace.php?update_error=Invalid request");
exit();

$conn->close(); 