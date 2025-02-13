CREATE DATABASE kisan_db;
USE kisan_db;

-- Users Table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15),
    region VARCHAR(100)
);

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