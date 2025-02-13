<?php
require_once 'db_connect.php';

try {
    // Drop and recreate the products table with correct structure
    $sql = "
    DROP TABLE IF EXISTS products;
    
    CREATE TABLE products (
        product_id INT PRIMARY KEY AUTO_INCREMENT,
        seller_id INT NOT NULL,
        category_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price_per_kg DECIMAL(10,2) NOT NULL,
        quantity_available DECIMAL(10,2) NOT NULL DEFAULT 0,
        unit VARCHAR(20) DEFAULT 'kg',
        harvest_date DATE,
        expiry_date DATE,
        farming_method VARCHAR(100),
        is_organic BOOLEAN DEFAULT FALSE,
        location VARCHAR(255),
        status ENUM('available', 'sold_out', 'removed') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (seller_id) REFERENCES users(user_id),
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
    );
    ";

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
    }
    
    echo "Database structure updated successfully";
} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage();
}
?> 