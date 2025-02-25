<?php
// Database configuration
$servername = "localhost";
$username = "root";  // Default XAMPP username
$password = "";      // Default XAMPP password
$database = "kisan_db";

// Create database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products with images
$sql = "SELECT 
          p.product_id, 
          p.name, 
          p.description, 
          p.price_per_kg, 
          REPLACE(pi.image_url, 'uploads/products/', '') AS image_url 
        FROM products p 
        LEFT JOIN product_images pi 
          ON p.product_id = pi.product_id AND pi.is_primary = 1";

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($products);

$conn->close();
?>