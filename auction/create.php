<?php
require_once '../includes/init.php';
require_once '../models/Auction.php';

// Check if user is farmer or admin
if (!isFarmer() && !isAdmin()) {
    header('Location: index.php');
    exit();
}

// Initialize auction object
$auction = new Auction();
$categories = $auction->getCategories();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle image upload
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/auctions/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_url = 'uploads/auctions/' . $file_name;
            }
        }

        // Create new auction
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'starting_bid' => $_POST['starting_price'],
            'current_bid' => $_POST['starting_price'], // Initial current bid equals starting price
            'min_increment' => $_POST['min_increment'],
            'end_time' => $_POST['end_time'],
            'image_url' => $image_url,
            'seller_name' => $_SESSION['user']['name']
        ];

        if ($auction->create($data)) {
            header('Location: index.php?success=1');
            exit();
        } else {
            throw new Exception("Failed to create auction");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Auction - Kisan.ai</title>
    
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auction.css">
    <link rel="stylesheet" href="assets/css/create-auction.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-tractor me-2"></i>
                Kisan.ai
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="user-info me-3">
                    <span class="user-name">
                        <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest'; ?>
                    </span>
                    <span class="user-role">
                        <?php echo isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Visitor'; ?>
                    </span>
                </div>
                <a href="index.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-gavel me-2"></i>Auction Platform
                </a>
                <a href="../dashboard.php" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="auction-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="create-auction-card">
                        <h1 class="text-center mb-4">Create New Auction</h1>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data" class="create-auction-form">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Product Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Starting Price (₹)</label>
                                    <input type="number" name="starting_price" class="form-control" min="0" step="0.01" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimum Bid Increment (₹)</label>
                                    <input type="number" name="min_increment" class="form-control" min="0" step="0.01" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Time</label>
                                    <input type="datetime-local" name="start_time" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Time</label>
                                    <input type="datetime-local" name="end_time" class="form-control" required>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus-circle me-2"></i>Create Auction
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/create-auction.js"></script>
</body>
</html> 