<?php
// edit_product.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit();
}
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$product_id = intval($_GET['id']);

// Ensure that the logged‐in farmer is the owner.
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
$stmt->execute([$product_id, $_SESSION['user_id']]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found or access denied.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $description  = $_POST['description'];
    $price        = $_POST['price'];

    // Handle optional file upload.
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $upload_dir = 'uploads/';
        $file_name   = time() . '_' . basename($_FILES['product_image']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            $image = $target_file;
        } else {
            echo "Error uploading file.";
            exit();
        }
    } else {
        $image = $product['image'];
    }

    $stmt = $pdo->prepare("UPDATE products SET product_name = ?, description = ?, price = ?, image = ? WHERE id = ? AND farmer_id = ?");
    $stmt->execute([$product_name, $description, $price, $image, $product_id, $_SESSION['user_id']]);
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-5">
    <h2>Edit Product</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="product_name" class="form-label">Product Name</label>
        <input type="text" name="product_name" id="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" required><?php echo htmlspecialchars($product['description']); ?></textarea>
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Price (₹)</label>
        <input type="number" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="product_image" class="form-label">Product Image</label>
        <input type="file" name="product_image" id="product_image" class="form-control">
        <small>If you don’t upload a new image, the current image will be retained.</small>
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
