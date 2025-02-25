<?php
// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function isFarmer() {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'farmer';
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

function isAuthenticated() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

// Check authentication only if not in login page
$currentPage = basename($_SERVER['PHP_SELF']);
if ($currentPage !== 'login.php' && $currentPage !== 'register.php' && !isLoggedIn()) {
    header('Location: login.php');
    exit();
} 