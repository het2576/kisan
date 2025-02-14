<?php
require_once '../includes/init.php';
require_once '../models/Auction.php';

// Set page variables
$page = 'auctions';
$pageTitle = "Agricultural Auction Platform";

// Initialize auction object
$auction = new Auction();

// Get filter parameters
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'ending_soon';
$search = $_GET['search'] ?? '';

// Get active auctions with filters
$active_auctions = $auction->getActiveWithFilters($category, $sort, $search);

// Get categories for filter
$categories = $auction->getCategories();
if (empty($categories)) {
    // If no categories exist, try to create them
    require_once '../database/direct_setup.php';
    $categories = $auction->getCategories();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Kisan.ai</title>
    
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auction.css">
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
                <a href="../dashboard.php" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Add this after the navbar -->
    <?php if (isFarmer() || isAdmin()): ?>
        <div class="quick-access d-none d-lg-block">
            <div class="btn-group-vertical">
                <a href="create.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Create Auction
                </a>
                <a href="my-auctions.php" class="btn btn-outline-primary">
                    <i class="fas fa-list me-2"></i>My Auctions
                </a>
                <a href="auction-dashboard.php" class="btn btn-outline-primary">
                    <i class="fas fa-chart-line me-2"></i>Analytics
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="auction-main">
        <!-- Hero Section -->
        <section class="auction-hero">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1>Agricultural Auction Platform</h1>
                        <p class="lead">Discover and bid on premium agricultural products directly from farmers</p>
                        <?php if (isFarmer() || isAdmin()): ?>
                            <div class="hero-buttons">
                                <a href="create.php" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-plus-circle me-2"></i>Create New Auction
                                </a>
                                <a href="my-auctions.php" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-list me-2"></i>My Auctions
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-6">
                        <div class="auction-stats">
                            <div class="stat-item">
                                <span class="stat-value"><?php echo $auction->getActiveCount(); ?></span>
                                <span class="stat-label">Active Auctions</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value"><?php echo $auction->getTotalBids(); ?></span>
                                <span class="stat-label">Total Bids</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value"><?php echo $auction->getSuccessfulAuctions(); ?></span>
                                <span class="stat-label">Completed Deals</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add this after the hero section -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                Your auction has been created successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Filter Section -->
        <section class="auction-filters">
            <div class="container-fluid">
                <form class="filter-form" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="sort" class="form-select">
                                <option value="ending_soon" <?php echo $sort == 'ending_soon' ? 'selected' : ''; ?>>Ending Soon</option>
                                <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search auctions..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- Auctions Grid -->
        <section class="auction-grid">
            <div class="container-fluid">
                <?php if ($active_auctions && $active_auctions->num_rows > 0): ?>
                    <div class="row">
                        <?php while ($item = $active_auctions->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                <div class="auction-card" data-auction-id="<?php echo $item['auction_id']; ?>">
                                    <div class="auction-image">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['title']); ?>">
                                        <div class="auction-status <?php echo $item['end_time'] < date('Y-m-d H:i:s', strtotime('+24 hours')) ? 'ending-soon' : ''; ?>">
                                            <?php echo $item['end_time'] < date('Y-m-d H:i:s', strtotime('+24 hours')) ? 'Ending Soon' : 'Active'; ?>
                                        </div>
                                    </div>
                                    <div class="auction-details">
                                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                        <p class="description"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?></p>
                                        <div class="bid-info">
                                            <div class="current-bid">
                                                <span class="label">Current Bid</span>
                                                <span class="amount">₹<?php echo number_format($item['current_bid'], 2); ?></span>
                                            </div>
                                            <div class="time-left" data-end="<?php echo $item['end_time']; ?>">
                                                <span class="label">Time Left</span>
                                                <span class="countdown"></span>
                                            </div>
                                        </div>
                                        <div class="auction-footer">
                                            <a href="view.php?id=<?php echo $item['auction_id']; ?>" 
                                               class="btn btn-primary w-100">View & Bid</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-auctions">
                        <i class="fas fa-box-open"></i>
                        <h3>No Active Auctions</h3>
                        <p>There are no active auctions matching your criteria at the moment.</p>
                        <?php if (isFarmer() || isAdmin()): ?>
                            <a href="create.php" class="btn btn-primary">Create an Auction</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="auction-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p>© 2024 Kisan.ai - Agricultural Auction Platform</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="../terms.php">Terms & Conditions</a>
                    <a href="../privacy.php">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/auction.js"></script>

    <!-- Add this just before closing </body> tag -->
    <?php if (isFarmer() || isAdmin()): ?>
        <a href="create.php" class="floating-action-button">
            <i class="fas fa-plus"></i>
        </a>
    <?php endif; ?>
</body>
</html> 