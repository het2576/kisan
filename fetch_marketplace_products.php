<?php
session_start();
require_once 'db_connect.php';

$category = $_POST['category'] ?? '';
$sort = $_POST['sort'] ?? 'newest';
$farming_method = $_POST['farming_method'] ?? '';

$query = "SELECT p.*, u.name as seller_name, c.name as category_name 
          FROM products p 
          LEFT JOIN users u ON p.seller_id = u.user_id 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.status = 'available'";

$params = [];
$types = "";

if ($category) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

if ($farming_method) {
    $query .= " AND p.farming_method = ?";
    $params[] = $farming_method;
    $types .= "s";
}

switch ($sort) {
    case 'price_high':
        $query .= " ORDER BY p.price_per_kg DESC";
        break;
    case 'price_low':
        $query .= " ORDER BY p.price_per_kg ASC";
        break;
    case 'oldest':
        $query .= " ORDER BY p.created_at ASC";
        break;
    default: // newest
        $query .= " ORDER BY p.created_at DESC";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

while ($product = $result->fetch_assoc()): ?>
    <div class="col-md-6 col-lg-4">
        <div class="product-card">
            <div class="product-image">
                <?php
                // Get primary image
                $img_query = "SELECT image_url FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1";
                $img_stmt = $conn->prepare($img_query);
                $img_stmt->bind_param("i", $product['product_id']);
                $img_stmt->execute();
                $img_result = $img_stmt->get_result();
                $image = $img_result->fetch_assoc();
                ?>
                <img src="<?php echo $image ? $image['image_url'] : 'assets/images/default-product.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     class="product-img">
            </div>
            <div class="product-info p-3">
                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="product-price">₹<?php echo number_format($product['price_per_kg'], 2); ?>/kg</p>
                <p class="product-location">
                    <i class="fas fa-map-marker-alt"></i> 
                    <?php echo htmlspecialchars($product['location']); ?>
                </p>
                <div class="product-meta">
                    <span class="badge bg-success"><?php echo $product['farming_method']; ?></span>
                    <span class="badge bg-info"><?php echo $product['quantity_available']; ?> <?php echo $product['unit']; ?></span>
                </div>
                <button class="btn btn-primary mt-3 quick-view-btn" data-product-id="<?php echo $product['product_id']; ?>">
                    Quick View
                </button>
            </div>
        </div>
    </div>
<?php endwhile; ?> 