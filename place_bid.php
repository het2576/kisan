<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/models/Auction.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false, 'message' => '');
    
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        $response['message'] = 'Please login to place a bid';
        echo json_encode($response);
        exit;
    }

    $auction_id = isset($_POST['auction_id']) ? intval($_POST['auction_id']) : 0;
    $bid_amount = isset($_POST['bid_amount']) ? floatval($_POST['bid_amount']) : 0;
    $bidder_id = $_SESSION['user']['id'];
    $bidder_name = $_SESSION['user']['name'];

    $auction = new Auction();
    $current_auction = $auction->getById($auction_id);

    if (!$current_auction) {
        $response['message'] = 'Auction not found';
        echo json_encode($response);
        exit;
    }

    // Validate bid
    if ($bid_amount <= $current_auction['current_bid']) {
        $response['message'] = 'Bid must be higher than current bid';
        echo json_encode($response);
        exit;
    }

    if ($bid_amount < ($current_auction['current_bid'] + $current_auction['min_increment'])) {
        $response['message'] = 'Bid must be at least ' . number_format($current_auction['min_increment'], 2) . ' more than current bid';
        echo json_encode($response);
        exit;
    }

    // Place bid
    $result = $auction->placeBid($auction_id, $bidder_id, $bidder_name, $bid_amount);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Bid placed successfully!';
        $response['new_bid'] = number_format($bid_amount, 2);
    } else {
        $response['message'] = 'Failed to place bid. Please try again.';
    }

    echo json_encode($response);
    exit;
} 