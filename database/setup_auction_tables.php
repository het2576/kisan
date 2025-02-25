<?php
require_once '../includes/init.php';

try {
    // First check if tables exist
    $tables = ['auction_categories', 'auctions', 'bids', 'auction_images', 'auction_watchers'];
    $existing_tables = [];
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            $existing_tables[] = $table;
        }
    }

    // Drop existing tables in reverse order to handle foreign key constraints
    if (!empty($existing_tables)) {
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        foreach (array_reverse($existing_tables) as $table) {
            $conn->query("DROP TABLE IF EXISTS `$table`");
            echo "Dropped existing table: $table<br>";
        }
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    }

    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/auction_tables.sql');
    
    // Split SQL into individual queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            if (!$conn->query($query)) {
                throw new Exception("Error executing query: " . $conn->error);
            }
        }
    }
    
    echo "Auction tables created successfully!<br>";
    
    // Verify tables were created
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "Table '$table' verified.<br>";
        } else {
            throw new Exception("Table '$table' was not created properly.");
        }
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} 