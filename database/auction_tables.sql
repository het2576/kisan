-- Drop existing tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS `auction_watchers`;
DROP TABLE IF EXISTS `auction_images`;
DROP TABLE IF EXISTS `bids`;
DROP TABLE IF EXISTS `auctions`;
DROP TABLE IF EXISTS `auction_categories`;

-- Create auction categories table
CREATE TABLE `auction_categories` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default categories
INSERT INTO `auction_categories` (`name`, `description`) VALUES
('Grains', 'Wheat, Rice, Corn, and other grains'),
('Vegetables', 'Fresh vegetables and produce'),
('Fruits', 'Fresh fruits and orchards produce'),
('Dairy', 'Milk and dairy products'),
('Livestock', 'Farm animals and livestock'),
('Equipment', 'Farming equipment and machinery'),
('Seeds', 'Agricultural seeds and planting materials'),
('Organic', 'Certified organic products');

-- Create auctions table
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create bids table
CREATE TABLE `bids` (
    `bid_id` INT PRIMARY KEY AUTO_INCREMENT,
    `auction_id` INT NOT NULL,
    `bidder_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('active', 'won', 'lost', 'cancelled') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`auction_id`),
    FOREIGN KEY (`bidder_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create auction_images table
CREATE TABLE `auction_images` (
    `image_id` INT PRIMARY KEY AUTO_INCREMENT,
    `auction_id` INT NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `is_primary` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`auction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create auction_watchers table
CREATE TABLE `auction_watchers` (
    `watcher_id` INT PRIMARY KEY AUTO_INCREMENT,
    `auction_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`auction_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`),
    UNIQUE KEY `unique_watcher` (`auction_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX idx_auction_status ON auctions(status);
CREATE INDEX idx_auction_end_time ON auctions(end_time);
CREATE INDEX idx_auction_category ON auctions(category_id);
CREATE INDEX idx_bids_auction ON bids(auction_id);
CREATE INDEX idx_bids_amount ON bids(amount);

-- Auction transactions table
CREATE TABLE auction_transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('deposit', 'withdrawal', 'bid_hold', 'bid_release', 'refund') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    reference_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 