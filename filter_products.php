<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query
$query = "SELECT * FROM products WHERE 1=1";

if (!empty($category) && $category !== 'all') {
    $query .= " AND category = '" . mysqli_real_escape_string($conn, $category) . "'";
}

if (!empty($search)) {
    $query .= " AND (name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' OR 
                     description LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
}

$result = mysqli_query($conn, $query);
$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Ensure image path is complete
    $row['image'] = '/uploads/' . $row['image'];
    $products[] = $row;
}

echo json_encode(['products' => $products]);
mysqli_close($conn);
?> 