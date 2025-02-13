<?php
require_once 'db_connect.php';

try {
    // First check if the column exists
    $check_column = "
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'products' 
        AND COLUMN_NAME = 'quantity_available'
    ";
    
    $result = $conn->query($check_column);
    
    if ($result->num_rows === 0) {
        // Column doesn't exist, add it
        $add_column = "
            ALTER TABLE products 
            ADD COLUMN quantity_available DECIMAL(10,2) NOT NULL DEFAULT 0 
            AFTER price_per_kg
        ";
        $conn->query($add_column);
        echo "Column 'quantity_available' added successfully<br>";
    }

    // Update any NULL values to 0
    $update_nulls = "
        UPDATE products 
        SET quantity_available = 0 
        WHERE quantity_available IS NULL
    ";
    $conn->query($update_nulls);
    echo "NULL values updated to 0<br>";

    // Add NOT NULL constraint if not already present
    $add_constraint = "
        ALTER TABLE products 
        MODIFY COLUMN quantity_available DECIMAL(10,2) NOT NULL DEFAULT 0
    ";
    $conn->query($add_constraint);
    echo "NOT NULL constraint added successfully<br>";

    echo "Database update completed successfully";
} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage();
}
?> 