<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/models/Auction.php';

// Initialize auction object
$auction = new Auction();

// Add this near the top of the file, after session check
if (isset($_SESSION['user'])) {
    echo "<!-- Debug: User Role: " . $_SESSION['user']['role'] . " -->";
    echo "<!-- Debug: User ID: " . $_SESSION['user']['id'] . " -->";
}

// Handle form submission for adding an auction
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_auction'])) {
    error_log("Form submitted. POST data: " . print_r($_POST, true));
    error_log("Files data: " . print_r($_FILES, true));

    // Check if user is logged in and is a farmer
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'farmer') {
        $message = 'Only farmers can create auctions. Please login as a farmer.';
        error_log("User role check failed. Session: " . print_r($_SESSION, true));
    } else {
        // Retrieve and sanitize inputs
        $title         = trim($_POST['title']);
        $description   = trim($_POST['description']);
        $starting_bid  = floatval($_POST['starting_bid']);
        $min_increment = floatval($_POST['min_increment']);
        $end_time      = $_POST['end_time']; // expecting HTML5 datetime-local format
        
        // Handle file upload
        $image_url = '';
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/auction_items/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($file_extension, $allowed_extensions)) {
                $file_name = uniqid('auction_') . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['item_image']['tmp_name'], $upload_path)) {
                    $image_url = $upload_path;
                } else {
                    $message = 'Failed to upload image. Please try again.';
                }
            } else {
                $message = 'Invalid file type. Please upload JPG, JPEG, PNG, or GIF files.';
            }
        }

        // Assume seller_name is taken from the logged-in user's session
        $seller_name   = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'Unknown';

        // Basic validation (you can add more robust checks here)
        if (empty($title) || empty($description) || empty($starting_bid) || empty($min_increment) || empty($end_time)) {
            $message = 'Please fill in all required fields.';
        } else {
            // Convert the datetime-local value to a proper MySQL datetime format
            $end_time_formatted = date('Y-m-d H:i:s', strtotime($end_time));

            // Insert new auction. We assume the create() method sets the current_bid
            // to the starting bid, and returns true on success.
            $result = $auction->create([
                'title'         => $title,
                'description'   => $description,
                'starting_bid'  => $starting_bid,
                'current_bid'   => $starting_bid, // initial current bid equals the starting bid
                'min_increment' => $min_increment,
                'end_time'      => $end_time_formatted,
                'image_url'     => $image_url,
                'seller_name'   => $seller_name
            ]);

            if ($result) {
                $message = 'Auction added successfully.';
                error_log("Auction created successfully");
            } else {
                $message = 'Failed to add auction. Please try again.';
                error_log("Auction creation failed. Last error: " . $auction->getLastError());
            }
        }
    }

    // After form submission handling
    echo "<!-- Debug Info:
    Title: $title
    Description: $description
    Starting Bid: $starting_bid
    Min Increment: $min_increment
    End Time: $end_time_formatted
    Image URL: $image_url
    Seller: $seller_name
    -->";
}

// Get all active auctions
$active_auctions = $auction->getActive();

// Debug database results
if ($active_auctions === false) {
    error_log("Error fetching auctions");
}

// Set page title and current page for sidebar highlighting
$page      = 'auctions';
$subpage   = 'active_auctions';
$pageTitle = "Bidding Platform";

// Start output buffering
ob_start();
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Page Title and Add Auction Button -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">Bidding Platform</h1>
                    <button type="button" class="btn btn-primary btn-lg add-auction-btn" onclick="$('#addAuctionModal').modal('show');">
                        <i class="fas fa-plus-circle"></i> Add Product for Auction
                    </button>
                </div>
                
                <!-- Quick Instructions -->
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> 
                    Click the "Add Product for Auction" button to list your items for bidding. Fill in the product details and set your starting bid.
                </div>
            </div>
        </div>

        <!-- Add this CSS -->
        <style>
        .add-auction-btn {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .add-auction-btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .add-auction-btn i {
            margin-right: 10px;
        }
        </style>

        <!-- Add this JavaScript -->
        <script>
        $(document).ready(function() {
            // Make sure the modal is properly initialized
            $('#addAuctionModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            // Add click handler for the button
            $('.add-auction-btn').click(function() {
                $('#addAuctionModal').modal('show');
            });
        });
        </script>

        <!-- Add a welcome message and instructions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Welcome to Our Bidding Platform!</h5>
                        <p class="card-text">
                            <i class="fas fa-info-circle text-primary"></i> 
                            To add your item for auction:
                        </p>
                        <ol>
                            <li>Click the "Add Your Item for Auction" button above</li>
                            <li>Fill in your item details and upload a clear photo</li>
                            <li>Set your starting bid and auction duration</li>
                            <li>Click "Start Auction" to list your item</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Update the Add Auction Modal -->
        <div class="modal fade" id="addAuctionModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="" enctype="multipart/form-data" id="auctionForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Your Product for Auction</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Product Title*</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="form-group">
                                <label>Description*</label>
                                <textarea class="form-control" name="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Starting Bid (₹)*</label>
                                <input type="number" class="form-control" name="starting_bid" required>
                            </div>
                            <div class="form-group">
                                <label>Minimum Bid Increment (₹)*</label>
                                <input type="number" class="form-control" name="min_increment" required>
                            </div>
                            <div class="form-group">
                                <label>End Time*</label>
                                <input type="datetime-local" class="form-control" name="end_time" required>
                            </div>
                            <div class="form-group">
                                <label>Product Image*</label>
                                <input type="file" class="form-control" name="item_image" accept="image/*" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="add_auction" value="1">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Start Auction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add this right after your filters (the All Categories and Ending Soon dropdowns) -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="add-auction-banner">
                    <div class="banner-content">
                        <div class="banner-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="banner-text">
                            <h2>Start Selling Your Agricultural Products</h2>
                            <p>List your products for auction and get the best price from buyers</p>
                        </div>
                        <button type="button" class="btn-add-auction" data-toggle="modal" data-target="#addAuctionModal">
                            <i class="fas fa-plus-circle"></i> Add Product for Auction
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Auctions -->
        <div class="row">
            <?php if ($active_auctions && $active_auctions->num_rows > 0): ?>
                <?php while ($row = $active_auctions->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($row['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                     class="card-img-top" alt="Auction Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text">
                                    <?php echo substr(htmlspecialchars($row['description']), 0, 100) . '...'; ?>
                                </p>
                                <div class="auction-details">
                                    <p><strong>Current Bid:</strong> 
                                        <span class="current-bid-amount">₹<?php echo number_format($row['current_bid'], 2); ?></span>
                                    </p>
                                    <p><strong>Minimum Increment:</strong> ₹<?php echo number_format($row['min_increment'], 2); ?></p>
                                    <p><strong>Seller:</strong> <?php echo htmlspecialchars($row['seller_name']); ?></p>
                                    <p><strong>Ends:</strong> 
                                        <span class="auction-end-time" data-endtime="<?php echo $row['end_time']; ?>">
                                            <?php echo date('d M Y, h:i A', strtotime($row['end_time'])); ?>
                                        </span>
                                    </p>
                                </div>
                                
                                <?php if (isset($_SESSION['user'])): ?>
                                    <div class="bid-section mt-3">
                                        <form class="bid-form" data-auction-id="<?php echo $row['auction_id']; ?>">
                                            <div class="input-group">
                                                <input type="number" 
                                                       class="form-control bid-amount" 
                                                       step="0.01" 
                                                       min="<?php echo $row['current_bid'] + $row['min_increment']; ?>"
                                                       placeholder="Enter bid amount">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary place-bid" type="submit">Place Bid</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info mt-3">
                                        Please <a href="login.php">login</a> to place a bid
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No active auctions at the moment.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Add this right after the filters section -->
        <div class="add-product-container">
            <div class="add-product-card">
                <div class="card-body text-center">
                    <i class="fas fa-plus-circle add-icon"></i>
                    <h3>Add Your Product for Auction</h3>
                    <p>Start selling your agricultural products today</p>
                    <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addProductModal">
                        <i class="fas fa-plus"></i> Add Product Now
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Product for Auction</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form id="addProductForm" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Product Name*</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="form-group">
                                <label>Description*</label>
                                <textarea class="form-control" name="description" rows="4" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Starting Price (₹)*</label>
                                        <input type="number" class="form-control" name="starting_bid" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Minimum Bid Increment (₹)*</label>
                                        <input type="number" class="form-control" name="min_increment" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Auction End Date/Time*</label>
                                <input type="datetime-local" class="form-control" name="end_time" required>
                            </div>
                            <div class="form-group">
                                <label>Product Image*</label>
                                <input type="file" class="form-control" name="item_image" accept="image/*" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="add_auction" value="1">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Start Auction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.content-wrapper {
    margin-left: 280px;
    padding: 20px;
    margin-top: 70px;
}

.auction-details p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.card {
    transition: transform 0.2s;
    border: 1px solid rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
}

@media (max-width: 768px) {
    .content-wrapper {
        margin-left: 0;
        padding: 15px;
    }
}

.custom-file-label::after {
    content: "Browse";
}

.modal-lg {
    max-width: 800px;
}

.alert i {
    margin-right: 8px;
}

.btn i {
    margin-right: 5px;
}

/* Enhanced Add Auction button */
.add-auction-btn {
    padding: 15px 30px;
    font-size: 1.2rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background-color: #28a745;
    border: none;
    box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
    transition: all 0.3s ease;
}

.add-auction-btn:hover {
    background-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(40, 167, 69, 0.3);
}

.add-auction-btn i {
    margin-right: 10px;
    font-size: 1.1em;
}

/* Welcome card styling */
.card-title {
    color: #2c3e50;
    font-weight: 600;
}

.card-text {
    font-size: 1.1rem;
    color: #34495e;
}

.card ol {
    padding-left: 20px;
    margin-top: 15px;
}

.card ol li {
    padding: 5px 0;
    color: #2c3e50;
}

/* Alert styling */
.alert {
    border-radius: 8px;
    border-left: 5px solid;
}

.alert-info {
    border-left-color: #17a2b8;
}

.alert-success {
    border-left-color: #28a745;
}

.alert-danger {
    border-left-color: #dc3545;
}

.alert i {
    margin-right: 10px;
}

/* Style for the auction cards */
.auction-card {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.auction-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.add-auction-section {
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.add-product-section {
    background: linear-gradient(135deg, #43A047, #2E7D32);
    color: white;
    padding: 40px 20px;
    border-radius: 15px;
    margin: 20px 0 40px 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.auction-icon {
    width: 80px;
    height: 80px;
    margin-bottom: 20px;
}

.add-product-btn {
    background: #FFC107;
    color: #000;
    font-size: 1.2rem;
    padding: 15px 40px;
    border: none;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.add-product-btn:hover {
    background: #FFB300;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

.add-product-btn i {
    margin-right: 10px;
}

.lead {
    font-size: 1.1rem;
    opacity: 0.9;
}

.add-auction-banner {
    background: linear-gradient(135deg, #2E7D32, #43A047);
    border-radius: 12px;
    padding: 30px;
    margin: 20px 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.banner-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
}

.banner-icon {
    font-size: 3rem;
    margin-right: 20px;
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 50%;
}

.banner-text {
    flex-grow: 1;
}

.banner-text h2 {
    font-size: 1.8rem;
    margin-bottom: 10px;
    color: white;
}

.banner-text p {
    font-size: 1.1rem;
    margin: 0;
    opacity: 0.9;
}

.btn-add-auction {
    background: #FFC107;
    color: #000;
    border: none;
    padding: 15px 30px;
    font-size: 1.2rem;
    font-weight: 600;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.btn-add-auction:hover {
    background: #FFB300;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

.btn-add-auction i {
    margin-right: 10px;
}

@media (max-width: 768px) {
    .banner-content {
        flex-direction: column;
        text-align: center;
    }
    
    .banner-icon {
        margin: 0 0 20px 0;
    }
    
    .banner-text {
        margin-bottom: 20px;
    }
    
    .btn-add-auction {
        width: 100%;
    }
}

.add-product-container {
    margin: 30px 0;
    padding: 20px;
}

.add-product-card {
    background: linear-gradient(135deg, #43A047, #2E7D32);
    border-radius: 15px;
    padding: 40px;
    color: white;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.add-icon {
    font-size: 48px;
    margin-bottom: 20px;
    color: #FFC107;
}

.add-product-card h3 {
    margin-bottom: 15px;
    font-size: 24px;
}

.add-product-card p {
    margin-bottom: 25px;
    opacity: 0.9;
}

.add-product-card .btn {
    padding: 12px 30px;
    font-size: 18px;
    font-weight: 600;
    border-radius: 50px;
    background: #FFC107;
    border: none;
    color: #000;
    transition: all 0.3s ease;
}

.add-product-card .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    background: #FFB300;
}

.modal-content {
    border-radius: 15px;
}

.modal-header {
    background: #43A047;
    color: white;
    border-radius: 15px 15px 0 0;
}

.modal-header .close {
    color: white;
}

.form-control {
    border-radius: 8px;
    padding: 12px;
}
</style>

<?php
$content = ob_get_clean();
include 'dashboard.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle bid form submissions
    document.querySelectorAll('.bid-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const auctionId = this.dataset.auctionId;
            const bidAmount = this.querySelector('.bid-amount').value;
            const card = this.closest('.card');

            fetch('place_bid.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `auction_id=${auctionId}&bid_amount=${bidAmount}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    card.querySelector('.current-bid-amount').textContent = '₹' + data.new_bid;
                    this.querySelector('.bid-amount').value = '';
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'An error occurred. Please try again.');
            });
        });
    });

    // Show alert function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Update countdown timers
    setInterval(function() {
        document.querySelectorAll('.auction-end-time').forEach(timeElement => {
            const endTime = new Date(timeElement.dataset.endtime).getTime();
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                timeElement.innerHTML = 'Auction Ended';
                timeElement.closest('.card').querySelector('.bid-section')?.remove();
            } else {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timeElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            }
        });
    }, 1000);

    // Set minimum datetime for auction end time
    const endTimeInput = document.getElementById('endTime');
    if (endTimeInput) {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 10); // Minimum 10 minutes from now
        const minDateTime = now.toISOString().slice(0, 16);
        endTimeInput.min = minDateTime;
    }

    // Update file input label with selected filename
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });

    // Form validation
    document.getElementById('auctionForm').addEventListener('submit', function(e) {
        console.log('Form submitted');
        
        // Log form data
        const formData = new FormData(this);
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
    });
});

// Add this to your existing script
$(document).ready(function() {
    // Initialize modal
    $('#addAuctionModal').modal({
        show: false
    });

    // Add click handler for the button
    $('.add-auction-btn').click(function() {
        $('#addAuctionModal').modal('show');
    });

    // Form submission handler
    $('#auctionForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: 'auctions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#addAuctionModal').modal('hide');
                location.reload(); // Reload to show new auction
            },
            error: function() {
                alert('Error adding auction. Please try again.');
            }
        });
    });
});

// Add this to your existing script
$(document).ready(function() {
    // Initialize modal
    $('#addProductModal').modal({
        show: false,
        backdrop: 'static'
    });

    // Form submission
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'auctions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#addProductModal').modal('hide');
                location.reload();
            },
            error: function() {
                alert('Error adding product. Please try again.');
            }
        });
    });
});
</script>
