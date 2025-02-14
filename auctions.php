<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/models/Auction.php';

// Initialize auction object
$auction = new Auction();

// Handle form submission for adding an auction
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_auction'])) {
    // Retrieve and sanitize inputs
    $title         = trim($_POST['title']);
    $description   = trim($_POST['description']);
    $starting_bid  = floatval($_POST['starting_bid']);
    $min_increment = floatval($_POST['min_increment']);
    $end_time      = $_POST['end_time']; // expecting HTML5 datetime-local format
    $image_url     = trim($_POST['image_url']);

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
        } else {
            $message = 'Failed to add auction. Please try again.';
        }
    }
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
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h1 class="page-title">Bidding Platform</h1>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addAuctionModal">
                    Add Auction
                </button>
            </div>
        </div>

        <!-- Display Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Add Auction Modal -->
        <div class="modal fade" id="addAuctionModal" tabindex="-1" role="dialog" aria-labelledby="addAuctionModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <form method="POST" action="">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="addAuctionModalLabel">Add New Auction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                        <div class="form-group">
                            <label for="auctionTitle">Title</label>
                            <input type="text" class="form-control" id="auctionTitle" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="auctionDescription">Description</label>
                            <textarea class="form-control" id="auctionDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="startingBid">Starting Bid</label>
                            <input type="number" step="0.01" class="form-control" id="startingBid" name="starting_bid" required>
                        </div>
                        <div class="form-group">
                            <label for="minIncrement">Minimum Increment</label>
                            <input type="number" step="0.01" class="form-control" id="minIncrement" name="min_increment" required>
                        </div>
                        <div class="form-group">
                            <label for="endTime">End Time</label>
                            <input type="datetime-local" class="form-control" id="endTime" name="end_time" required>
                        </div>
                        <div class="form-group">
                            <label for="imageUrl">Image URL</label>
                            <input type="url" class="form-control" id="imageUrl" name="image_url">
                        </div>
                  </div>
                  <div class="modal-footer">
                    <input type="hidden" name="add_auction" value="1">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Auction</button>
                  </div>
                </div>
            </form>
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
                                    <p><strong>Current Bid:</strong> ₹<?php echo number_format($row['current_bid'], 2); ?></p>
                                    <p><strong>Minimum Increment:</strong> ₹<?php echo number_format($row['min_increment'], 2); ?></p>
                                    <p><strong>Seller:</strong> <?php echo htmlspecialchars($row['seller_name']); ?></p>
                                    <p><strong>Ends:</strong> <?php echo date('d M Y, h:i A', strtotime($row['end_time'])); ?></p>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="view_auction.php?id=<?php echo $row['auction_id']; ?>" 
                                   class="btn btn-primary w-100">View Details</a>
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
</style>

<?php
$content = ob_get_clean();
include 'dashboard.php';
?>

<script>
// Periodically update bid amounts and time remaining
document.addEventListener('DOMContentLoaded', function() {
    setInterval(function() {
        fetch('get_auction_updates.php')
            .then(response => response.json())
            .then(data => {
                // Update auction data as needed
                console.log('Auction data updated');
            })
            .catch(error => console.error('Error:', error));
    }, 5000);
});
</script>
