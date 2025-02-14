<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/models/Auction.php';

// Set page variables
$page = 'auctions';
$subpage = 'active_auctions';
$pageTitle = "Auction Platform";

// Initialize auction object
$auction = new Auction();
$active_auctions = $auction->getActive();

// Start output buffering
ob_start();
?>

<!-- Hero Section -->
<div class="auction-hero mb-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4">Agricultural Auction Platform</h1>
                <p class="lead">Buy and sell agricultural products through our transparent bidding system</p>
                <?php if (isFarmer() || isAdmin()): ?>
                    <a href="create_auction.php" class="btn btn-primary btn-lg">Create Auction</a>
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/images/auction-hero.svg" alt="Auction Platform" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="auction-filters mb-4">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <option value="grains">Grains</option>
                            <option value="vegetables">Vegetables</option>
                            <option value="fruits">Fruits</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort By</label>
                        <select class="form-select" name="sort">
                            <option value="ending_soon">Ending Soon</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="newest">Newest First</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" placeholder="Search auctions...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Auctions Grid -->
<div class="auction-grid">
    <div class="container-fluid">
        <div class="row">
            <?php if ($active_auctions && $active_auctions->num_rows > 0): ?>
                <?php while ($auction = $active_auctions->fetch_assoc()): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card auction-card h-100">
                            <div class="auction-status <?php echo $auction['end_time'] < date('Y-m-d H:i:s', strtotime('+24 hours')) ? 'ending-soon' : ''; ?>">
                                <?php echo $auction['end_time'] < date('Y-m-d H:i:s', strtotime('+24 hours')) ? 'Ending Soon' : 'Active'; ?>
                            </div>
                            <?php if ($auction['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($auction['image_url']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($auction['title']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($auction['title']); ?></h5>
                                <p class="card-text text-muted">
                                    <?php echo substr(htmlspecialchars($auction['description']), 0, 100) . '...'; ?>
                                </p>
                                <div class="auction-info">
                                    <div class="current-bid">
                                        <span class="label">Current Bid:</span>
                                        <span class="amount">₹<?php echo number_format($auction['current_bid'], 2); ?></span>
                                    </div>
                                    <div class="time-left" data-end="<?php echo $auction['end_time']; ?>">
                                        <span class="label">Time Left:</span>
                                        <span class="countdown"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-grid">
                                    <a href="view_auction.php?id=<?php echo $auction['auction_id']; ?>" 
                                       class="btn btn-outline-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No active auctions at the moment.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Auction Platform Styles */
.auction-hero {
    background: linear-gradient(135deg, #2F855A, #276749);
    color: white;
    padding: 3rem 0;
    border-radius: 15px;
    margin: 1rem;
}

.auction-filters .card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.auction-card {
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid rgba(0,0,0,0.1);
}

.auction-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.auction-status {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    background: rgba(255,255,255,0.9);
    color: #2F855A;
}

.auction-status.ending-soon {
    background: #FEB2B2;
    color: #C53030;
}

.auction-info {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0,0,0,0.1);
}

.auction-info .label {
    font-size: 0.8rem;
    color: #718096;
}

.auction-info .amount {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2F855A;
}

.auction-info .countdown {
    font-size: 0.9rem;
    font-weight: 500;
    color: #4A5568;
}

.card-img-top {
    height: 200px;
    object-fit: cover;
}

@media (max-width: 768px) {
    .auction-hero {
        padding: 2rem 0;
        margin: 0.5rem;
    }
    
    .auction-hero h1 {
        font-size: 2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update countdowns
    function updateCountdowns() {
        document.querySelectorAll('.time-left').forEach(function(element) {
            const endTime = new Date(element.dataset.end).getTime();
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                element.querySelector('.countdown').innerHTML = 'Auction Ended';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            let countdown = '';
            if (days > 0) countdown += days + 'd ';
            if (hours > 0) countdown += hours + 'h ';
            countdown += minutes + 'm ' + seconds + 's';

            element.querySelector('.countdown').innerHTML = countdown;
        });
    }

    // Update bid amounts
    function updateBids() {
        fetch('get_auction_updates.php')
            .then(response => response.json())
            .then(data => {
                // Update auction data
                data.auctions.forEach(auction => {
                    const card = document.querySelector(`[data-auction-id="${auction.id}"]`);
                    if (card) {
                        card.querySelector('.amount').textContent = '₹' + auction.current_bid;
                    }
                });
            })
            .catch(error => console.error('Error:', error));
    }

    // Initial updates
    updateCountdowns();
    
    // Set intervals for updates
    setInterval(updateCountdowns, 1000);
    setInterval(updateBids, 5000);
});
</script>

<?php
$content = ob_get_clean();
include 'includes/template.php';
?> 