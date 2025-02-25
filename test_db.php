<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/db_connect.php';

if (isset($conn)) {
    echo "Database connection successful!<br>";
    
    // Test query
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "Tables in database:<br>";
        while ($row = $result->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
    } else {
        echo "Error running query: " . $conn->error;
    }
} else {
    echo "Database connection failed!";
} 