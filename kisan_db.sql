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