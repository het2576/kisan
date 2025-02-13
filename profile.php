<?php
// profile.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';

// Only farmers may access this page.
if ($_SESSION['role'] !== 'farmer') {
    echo "Access denied. Only farmers can access this page.";
    exit();
}

// Fetch the farmer’s current details from the "users" table.
// (Assumes columns: id, name, contact_info, address, farm_details)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$farmer = $stmt->fetch();

$profile_updated = false;
$product_uploaded = false;

// Update profile if form submitted.
if (isset($_POST['update_profile'])) {
    $contact_info = $_POST['contact_info'];
    $address      = $_POST['address'];
    $farm_details = $_POST['farm_details'];
    
    $stmt = $pdo->prepare("UPDATE users SET contact_info = ?, address = ?, farm_details = ? WHERE id = ?");
    $stmt->execute([$contact_info, $address, $farm_details, $_SESSION['user_id']]);
    $profile_updated = true;
    
    // Refresh the $farmer variable.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $farmer = $stmt->fetch();
}

// Process product upload.
if (isset($_POST['upload_product'])) {
    $product_name = $_POST['product_name'];
    $description  = $_POST['description'];
    $price        = $_POST['price'];
    
    // Handle file upload.
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name   = time() . '_' . basename($_FILES['product_image']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            // Insert the new product into the "products" table.
            $stmt = $pdo->prepare("INSERT INTO products (farmer_id, product_name, description, price, image, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$_SESSION['user_id'], $product_name, $description, $price, $target_file]);
            $product_uploaded = true;
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Please select a product image.";
    }
}

// Retrieve all products uploaded by this farmer.
$stmt = $pdo->prepare("SELECT * FROM products WHERE farmer_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Farmer Profile - Kisan.ai Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-5">
    <h2>Farmer Profile</h2>
    <?php if ($profile_updated): ?>
      <div class="alert alert-success">Profile updated successfully.</div>
    <?php endif; ?>
    <form method="post" action="profile.php">
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" id="name" class="form-control" value="<?php echo htmlspecialchars($farmer['name']); ?>" disabled>
      </div>
      <div class="mb-3">
        <label for="contact_info" class="form-label">Contact Information</label>
        <input type="text" name="contact_info" id="contact_info" class="form-control" value="<?php echo htmlspecialchars($farmer['contact_info']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea name="address" id="address" class="form-control" required><?php echo htmlspecialchars($farmer['address']); ?></textarea>
      </div>
      <div class="mb-3">
        <label for="farm_details" class="form-label">Farm Details</label>
        <textarea name="farm_details" id="farm_details" class="form-control" required><?php echo htmlspecialchars($farmer['farm_details']); ?></textarea>
      </div>
      <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
    </form>
    
    <hr>
    <h2 class="mt-5">Upload New Product</h2>
    <?php if ($product_uploaded): ?>
      <div class="alert alert-success">Product uploaded successfully.</div>
    <?php endif; ?>
    <form method="post" action="profile.php" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="product_name" class="form-label">Product Name</label>
        <input type="text" name="product_name" id="product_name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" required></textarea>
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Price (₹)</label>
        <input type="number" name="price" id="price" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="product_image" class="form-label">Product Image</label>
        <input type="file" name="product_image" id="product_image" class="form-control" required>
      </div>
      <button type="submit" name="upload_product" class="btn btn-success">Upload Product</button>
    </form>
    
    <hr>
    <h2 class="mt-5">Your Products</h2>
    <div class="row">
      <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
              <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
              <p><strong>Price: </strong>₹<?php echo htmlspecialchars($product['price']); ?></p>
              <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
              <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
