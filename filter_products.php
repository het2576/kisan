<?php
session_start();
require_once 'db_connect.php';

// Get language from session
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Get filter parameters
$category = $_POST['category'] ?? '';
$status = $_POST['status'] ?? '';
$sort = $_POST['sort'] ?? '';
$search = $_POST['search'] ?? '';

// Build query
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.seller_id = ?";
$params = [$_SESSION['user_id']];
$types = "i";

// Add filters
if ($category) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

if ($status && $status !== 'all') {
    $query .= " AND p.status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// Add sorting
switch ($sort) {
    case 'price_high':
        $query .= " ORDER BY p.price_per_kg DESC";
        break;
    case 'price_low':
        $query .= " ORDER BY p.price_per_kg ASC";
        break;
    case 'newest':
        $query .= " ORDER BY p.created_at DESC";
        break;
    case 'oldest':
        $query .= " ORDER BY p.created_at ASC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

// Execute query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Check if we have results
if ($result->num_rows === 0) {
    echo '<div class="alert alert-info text-center">No products found matching your criteria.</div>';
    exit;
}

// Output products
while ($product = $result->fetch_assoc()) {
    include 'product_card.php'; // Create this partial view for consistent product display
}
?> 