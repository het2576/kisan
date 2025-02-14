<?php
require_once '../includes/init.php';
require_once '../models/Auction.php';

try {
    // Create test auction
    $auction = new Auction();
    $auction->seller_id = 1; // Make sure this user exists
    $auction->category_id = 1; // Grains category
    $auction->title = "Test Wheat Auction";
    $auction->description = "High quality wheat for sale";
    $auction->image_url = "uploads/auctions/default.jpg";
    $auction->starting_price = 1000.00;
    $auction->min_increment = 100.00;
    $auction->start_time = date('Y-m-d H:i:s');
    $auction->end_time = date('Y-m-d H:i:s', strtotime('+7 days'));
    $auction->status = 'active';

    if ($auction->create()) {
        echo "Test auction created successfully!";
    } else {
        throw new Exception("Failed to create test auction");
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} 