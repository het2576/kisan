<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'kisan_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('Asia/Kolkata');

// Site URL
define('SITE_URL', 'http://localhost:8888/kisan/');

// Upload directories
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/kisan/uploads/');
define('AUCTION_IMAGES_DIR', UPLOAD_DIR . 'auctions/');

// Create upload directories if they don't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!file_exists(AUCTION_IMAGES_DIR)) {
    mkdir(AUCTION_IMAGES_DIR, 0777, true);
}