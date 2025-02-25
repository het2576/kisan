<?php
require_once '../includes/init.php';

try {
    // Test database connection
    echo "<h3>Testing Database Connection</h3>";
    if ($conn->ping()) {
        echo "Database connection is working.<br>";
    } else {
        throw new Exception("Database connection failed");
    }

    // Check if auction_categories table exists
    echo "<h3>Checking Tables</h3>";
    $result = $conn->query("SHOW TABLES LIKE 'auction_categories'");
    if ($result->num_rows > 0) {
        echo "auction_categories table exists.<br>";
        
        // Check categories
        $categories = $conn->query("SELECT * FROM auction_categories");
        echo "Found " . $categories->num_rows . " categories.<br>";
        
        // List all categories
        echo "<h4>Categories:</h4>";
        echo "<ul>";
        while ($cat = $categories->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($cat['name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "auction_categories table does not exist.<br>";
        echo "<a href='direct_setup.php'>Click here to create tables</a>";
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} 