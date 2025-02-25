<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Add role validation
if (!isset($_SESSION['user']['role'])) {
    $_SESSION['user']['role'] = 'guest'; // Default role
}

// Load configurations
require_once __DIR__ . '/config.php';

// Database connection
require_once __DIR__ . '/db_connect.php';

// Load authentication functions
require_once __DIR__ . '/auth.php';

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1); 