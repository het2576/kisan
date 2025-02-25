<?php
// db.php

// Database configuration
$host = "localhost";
$username = "root";         // Default XAMPP MySQL username
$password = "";            // Empty password for default XAMPP
$database = "kisan_db";    // Your database name

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");

return $conn;
?>
