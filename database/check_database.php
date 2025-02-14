<?php
require_once '../includes/config.php';

try {
    // Create connection without database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if database exists
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");

    if ($result->num_rows == 0) {
        // Create database
        if ($conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME)) {
            echo "Database created successfully<br>";
        } else {
            throw new Exception("Error creating database: " . $conn->error);
        }
    }

    // Select the database
    $conn->select_db(DB_NAME);
    
    echo "Database check completed successfully<br>";
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} 