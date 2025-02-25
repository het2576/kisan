CREATE DATABASE IF NOT EXISTS kisan2;
USE kisan2;

CREATE TABLE IF NOT EXISTS products (
  product_id INT PRIMARY KEY AUTO_INCREMENT,
  seller_id INT,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price_per_kg DECIMAL(10,2) NOT NULL,
  quantity_available DECIMAL(10,2) NOT NULL,
  unit VARCHAR(20) DEFAULT 'kg',
  harvest_date DATE,
  expiry_date DATE,
  farming_method VARCHAR(50),
  is_organic BOOLEAN DEFAULT 0,
  location VARCHAR(255),
  status VARCHAR(20) DEFAULT 'active',
  min_order_quantity DECIMAL(10,2) DEFAULT 1,
  delivery_options VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_images (
  image_id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT,
  image_url TEXT NOT NULL,
  is_primary BOOLEAN DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(product_id)
); 