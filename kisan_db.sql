-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS kisan_db;
USE kisan_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    role ENUM('farmer', 'buyer', 'admin') NOT NULL DEFAULT 'farmer',
    region VARCHAR(100),
    farm_size VARCHAR(50),
    main_crops TEXT,
    farming_type ENUM('organic', 'traditional', 'mixed'),
    soil_type VARCHAR(100),
    irrigation VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
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

-- Product Images table
CREATE TABLE IF NOT EXISTS product_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(user_id),
    FOREIGN KEY (seller_id) REFERENCES users(user_id)
);

-- Order Items table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Vegetables', 'Fresh vegetables and greens'),
('Fruits', 'Fresh seasonal fruits'),
('Grains', 'Wheat, rice, and other grains'),
('Pulses', 'Different types of dals and pulses'),
('Spices', 'Fresh and dried spices'),
('Organic Products', 'Certified organic produce'),
('Seeds', 'Agricultural seeds'),
('Others', 'Other agricultural products');

-- Insert a default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@kisan.ai', '$2y$10$8FPi8P.Y8mhMX/MxWNqXJexX0ZVqZkYxwAI0qXNQzW2MQEHr8JnK.', 'admin');

-- Inventory Table
CREATE TABLE Inventory (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    item_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    expiration_date DATE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Market Prices Table
CREATE TABLE MarketPrices (
    price_id INT AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    market_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    date DATE NOT NULL
);

-- Weather Data Table
CREATE TABLE WeatherData (
    weather_id INT AUTO_INCREMENT PRIMARY KEY,
    region VARCHAR(100) NOT NULL,
    forecast_date DATE NOT NULL,
    temperature DECIMAL(5, 2) NOT NULL,
    rainfall DECIMAL(5, 2) NOT NULL
);

-- Tools Table
CREATE TABLE Tools (
    tool_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    tool_name VARCHAR(100) NOT NULL,
    status ENUM('available', 'in_use') DEFAULT 'available',
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Drop existing notifications table if exists
DROP TABLE IF EXISTS notifications;

-- Create notifications table with correct structure
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reference_id INT,
    language VARCHAR(5) NOT NULL DEFAULT 'en',
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS weather_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location_id INT,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS market_prices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    crop_id INT NOT NULL,
    crop_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_crops (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    crop_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Add test notifications with language field
INSERT INTO notifications (user_id, type, title, message, is_read, language) VALUES 
-- English notifications
(1, 'inventory', 'Low Stock Alert', 'Your wheat seeds inventory is running low', 0, 'en'),
(1, 'market', 'Price Update', 'Cotton prices have increased by 5%', 0, 'en'),
(1, 'weather', 'Weather Alert', 'Heavy rainfall expected tomorrow', 0, 'en'),

-- Hindi notifications
(1, 'inventory', 'स्टॉक अलर्ट', 'आपका गेहूं बीज स्टॉक कम हो रहा है', 0, 'hi'),
(1, 'market', 'मूल्य अपडेट', 'कपास की कीमतों में 5% की वृद्धि हुई है', 0, 'hi'),
(1, 'weather', 'मौसम अलर्ट', 'कल भारी बारिश की संभावना है', 0, 'hi'),

-- Gujarati notifications
(1, 'inventory', 'સ્ટોક એલર્ટ', 'તમારો ઘઉંના બીજનો સ્ટોક ઓછો થઈ રહ્યો છે', 0, 'gu'),
(1, 'market', 'ભાવ અપડેટ', 'કપાસના ભાવમાં 5% નો વધારો થયો છે', 0, 'gu'),
(1, 'weather', 'હવામાન એલર્ટ', 'આવતીકાલે ભારે વરસાદની આગાહી', 0, 'gu');

-- Create products table
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price_per_kg DECIMAL(10,2) NOT NULL,
    quantity_available DECIMAL(10,2) NOT NULL,
    unit VARCHAR(50) DEFAULT 'kg',
    harvest_date DATE DEFAULT NULL,
    expiry_date DATE DEFAULT NULL,
    is_organic TINYINT(1) DEFAULT 0,
    location VARCHAR(255) DEFAULT NULL,
    min_order_quantity DECIMAL(10,2) DEFAULT NULL,
    delivery_options VARCHAR(255) DEFAULT NULL,
    status ENUM('available', 'sold_out', 'removed') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Create product_images table
CREATE TABLE product_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Add new columns to users table
ALTER TABLE users
ADD COLUMN farm_size VARCHAR(50) DEFAULT NULL,
ADD COLUMN main_crops TEXT DEFAULT NULL,
ADD COLUMN farming_type ENUM('organic', 'traditional', 'mixed') DEFAULT NULL,
ADD COLUMN soil_type VARCHAR(100) DEFAULT NULL,
ADD COLUMN irrigation VARCHAR(100) DEFAULT NULL;

-- Update users table structure
ALTER TABLE users 
    MODIFY phone_number VARCHAR(20),
    MODIFY region VARCHAR(100),
    MODIFY farm_size VARCHAR(50),
    MODIFY main_crops TEXT,
    MODIFY farming_type ENUM('organic', 'traditional', 'mixed'),
    MODIFY soil_type VARCHAR(100),
    MODIFY irrigation VARCHAR(100);