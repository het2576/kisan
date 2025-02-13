<?php
// ecommerce.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';

if (!isset($_GET['product_id'])) {
    header("Location: dashboard.php");
    exit();
}

$product_id = intval($_GET['product_id']);

// Fetch product details (assumes the "users" table holds the farmer's contact info).
$stmt = $pdo->prepare("SELECT p.*, u.name AS farmer_name, u.contact_info AS farmer_contact 
                       FROM products p 
                       JOIN users u ON p.farmer_id = u.id 
                       WHERE p.id = ? AND p.status = 'active'");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found.";
    exit();
}

// Handle order submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consumer_id = $_SESSION['user_id'];
    $quantity    = intval($_POST['quantity']);
    $order_date  = date("Y-m-d H:i:s");

    // Insert the order into the "orders" table.
    // (Assumes an "orders" table with columns: id, product_id, farmer_id, consumer_id, quantity, order_date, status.)
    $stmt = $pdo->prepare("INSERT INTO orders (product_id, farmer_id, consumer_id, quantity, order_date, status) 
                           VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$product['id'], $product['farmer_id'], $consumer_id, $quantity, $order_date]);

    // Retrieve consumer details (assuming they are stored in session or in the users table).
    $consumer_name    = $_SESSION['name'];
    $consumer_contact = isset($_SESSION['contact_info']) ? $_SESSION['contact_info'] : 'Not provided';

    // Display confirmation with both parties' contact info.
    echo "<div class='container mt-5'>";
    echo "<h2>Order Confirmed</h2>";
    echo "<p>Thank you! Your order has been placed.</p>";
    echo "<h4>Contact Information:</h4>";
    echo "<p><strong>Farmer:</strong> " . htmlspecialchars($product['farmer_name']) . " – " . htmlspecialchars($product['farmer_contact']) . "</p>";
    echo "<p><strong>You:</strong> " . htmlspecialchars($consumer_name) . " – " . htmlspecialchars($consumer_contact) . "</p>";
    echo "<a href='dashboard.php' class='btn btn-primary mt-3'>Return to Dashboard</a>";
    echo "</div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($product['product_name']); ?> - Buy Now</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .img-fluid {
      max-height: 400px;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
      </div>
      <div class="col-md-6">
        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
        <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
        <p><strong>Farmer:</strong> <?php echo htmlspecialchars($product['farmer_name']); ?></p>
        <hr>
        <form method="post">
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
          </div>
          <button type="submit" class="btn btn-success">Place Order</button>
        </form>
      </div>
    </div>
  </div>
  <!-- Back to Dashboard Button -->
  <div class="container mt-3">
    <a href="dashboard.php" class="btn btn-outline-success mb-3">
      <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
