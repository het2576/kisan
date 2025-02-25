<?php
require_once __DIR__ . '/../includes/init.php';

// Verify database connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed. Please check your configuration.");
}

try {
    // Add some basic styling
    echo "
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .success { color: green; }
        .error { color: red; }
        .step { margin: 10px 0; }
    </style>
    ";

    echo "<h2>Setting up Auction System Database</h2>";

    // Disable foreign key checks temporarily
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // Drop existing tables in reverse order
    $tables = [
        'auction_watchers',
        'auction_images',
        'bids',
        'auctions',
        'auction_categories'
    ];

    foreach ($tables as $table) {
        $conn->query("DROP TABLE IF EXISTS `$table`");
        echo "<div class='step'>Dropped table if exists: $table</div>";
    }

    // Create auction_categories table
    $createCategoriesTable = "
        CREATE TABLE `auction_categories` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    
    if (!$conn->query($createCategoriesTable)) {
        throw new Exception("Error creating categories table: " . $conn->error);
    }
    echo "<div class='step'>Created auction_categories table</div>";

    // Create auctions table
    $createAuctionsTable = "
        CREATE TABLE `auctions` (
            `auction_id` INT PRIMARY KEY AUTO_INCREMENT,
            `seller_id` INT NOT NULL,
            `category_id` INT NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `image_url` VARCHAR(255),
            `starting_price` DECIMAL(10,2) NOT NULL,
            `min_increment` DECIMAL(10,2) NOT NULL,
            `start_time` DATETIME NOT NULL,
            `end_time` DATETIME NOT NULL,
            `status` ENUM('draft', 'active', 'completed', 'cancelled') DEFAULT 'draft',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`seller_id`) REFERENCES `users`(`user_id`),
            FOREIGN KEY (`category_id`) REFERENCES `auction_categories`(`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    
    if (!$conn->query($createAuctionsTable)) {
        throw new Exception("Error creating auctions table: " . $conn->error);
    }
    echo "<div class='step'>Created auctions table</div>";

    // Create bids table
    $createBidsTable = "
        CREATE TABLE `bids` (
            `bid_id` INT PRIMARY KEY AUTO_INCREMENT,
            `auction_id` INT NOT NULL,
            `bidder_id` INT NOT NULL,
            `amount` DECIMAL(10,2) NOT NULL,
            `status` ENUM('active', 'won', 'lost', 'cancelled') DEFAULT 'active',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`auction_id`),
            FOREIGN KEY (`bidder_id`) REFERENCES `users`(`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    
    if (!$conn->query($createBidsTable)) {
        throw new Exception("Error creating bids table: " . $conn->error);
    }
    echo "<div class='step'>Created bids table</div>";

    // Create auction_images table
    $createImagesTable = "
        CREATE TABLE `auction_images` (
            `image_id` INT PRIMARY KEY AUTO_INCREMENT,
            `auction_id` INT NOT NULL,
            `image_url` VARCHAR(255) NOT NULL,
            `is_primary` BOOLEAN DEFAULT FALSE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`auction_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    
    if (!$conn->query($createImagesTable)) {
        throw new Exception("Error creating auction_images table: " . $conn->error);
    }
    echo "<div class='step'>Created auction_images table</div>";

    // Create auction_watchers table
    $createWatchersTable = "
        CREATE TABLE `auction_watchers` (
            `watcher_id` INT PRIMARY KEY AUTO_INCREMENT,
            `auction_id` INT NOT NULL,
            `user_id` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`auction_id`),
            FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`),
            UNIQUE KEY `unique_watcher` (`auction_id`, `user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    
    if (!$conn->query($createWatchersTable)) {
        throw new Exception("Error creating auction_watchers table: " . $conn->error);
    }
    echo "<div class='step'>Created auction_watchers table</div>";

    // Create indexes
    $indexes = [
        "CREATE INDEX idx_auction_status ON auctions(status)",
        "CREATE INDEX idx_auction_end_time ON auctions(end_time)",
        "CREATE INDEX idx_auction_category ON auctions(category_id)",
        "CREATE INDEX idx_bids_auction ON bids(auction_id)",
        "CREATE INDEX idx_bids_amount ON bids(amount)"
    ];

    foreach ($indexes as $index) {
        if (!$conn->query($index)) {
            throw new Exception("Error creating index: " . $conn->error);
        }
    }
    echo "<div class='step'>Created all indexes</div>";

    // Insert default categories
    $categories = [
        ['Grains', 'Wheat, Rice, Corn, and other grains'],
        ['Vegetables', 'Fresh vegetables and produce'],
        ['Fruits', 'Fresh fruits and orchards produce'],
        ['Dairy', 'Milk and dairy products'],
        ['Livestock', 'Farm animals and livestock'],
        ['Equipment', 'Farming equipment and machinery'],
        ['Seeds', 'Agricultural seeds and planting materials'],
        ['Organic', 'Certified organic products']
    ];

    $stmt = $conn->prepare("INSERT INTO auction_categories (name, description) VALUES (?, ?)");
    foreach ($categories as $category) {
        $stmt->bind_param("ss", $category[0], $category[1]);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting category: " . $stmt->error);
        }
    }
    echo "<div class='step'>Inserted default categories</div>";

    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    echo "<h3 class='success'>Setup Complete!</h3>";
    echo "<div class='step'>All tables created and populated successfully!</div>";

    // Verify data
    $result = $conn->query("SELECT * FROM auction_categories");
    if ($result) {
        echo "<div class='step'>Number of categories: " . $result->num_rows . "</div>";
    } else {
        throw new Exception("Error verifying data: " . $conn->error);
    }

    echo "
    <div style='margin-top: 20px;'>
        <a href='test_setup.php' style='margin-right: 10px;'>Test the Setup</a> | 
        <a href='../auction/'>Go to Auction Platform</a>
    </div>
    ";

} catch (Exception $e) {
    die("<div class='error' style='margin: 20px;'>Error: " . $e->getMessage() . "</div>");
} 