-- Lab 5: Blind SQL Injection with Time Delays Database Schema
-- Drop database if exists and create new one
DROP DATABASE IF EXISTS lab5_blind_sqli;
CREATE DATABASE lab5_blind_sqli;
USE lab5_blind_sqli;

-- Create products table for the shop
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    image_url VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create analytics table (target for blind SQL injection)
CREATE TABLE analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(50) UNIQUE NOT NULL,
    first_seen DATETIME NOT NULL,
    last_seen DATETIME NOT NULL,
    page_views INT DEFAULT 1,
    user_agent TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create users table with sensitive data (for advanced attacks)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    secret_data VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample products
INSERT INTO products (name, description, price, category, image_url, stock_quantity) VALUES
('Wireless Laptop', 'High-performance wireless laptop with 16GB RAM', 1499.99, 'Electronics', 'laptop.jpg', 25),
('Bluetooth Headphones', 'Premium noise-cancelling bluetooth headphones', 299.99, 'Electronics', 'headphones.jpg', 50),
('Smart Watch', 'Fitness tracking smart watch with GPS', 399.99, 'Electronics', 'watch.jpg', 30),
('Gaming Keyboard', 'Mechanical RGB gaming keyboard', 149.99, 'Electronics', 'keyboard.jpg', 40),
('4K Monitor', '27-inch 4K UHD gaming monitor', 599.99, 'Electronics', 'monitor.jpg', 15),
('Office Chair', 'Ergonomic executive office chair', 399.99, 'Furniture', 'chair.jpg', 20),
('Standing Desk', 'Height adjustable standing desk', 699.99, 'Furniture', 'desk.jpg', 12),
('Reading Lamp', 'LED reading lamp with adjustable brightness', 79.99, 'Furniture', 'lamp.jpg', 35),
('Running Shoes', 'Lightweight professional running shoes', 129.99, 'Sports', 'shoes.jpg', 60),
('Yoga Mat', 'Premium eco-friendly yoga mat', 49.99, 'Sports', 'mat.jpg', 45);

-- Insert sample analytics data
INSERT INTO analytics (tracking_id, first_seen, last_seen, page_views, ip_address) VALUES
('TK1234567890abcdef', '2024-11-01 10:00:00', '2024-11-30 15:30:00', 45, '192.168.1.100'),
('TK2345678901bcdefg', '2024-11-05 14:20:00', '2024-11-29 18:45:00', 23, '192.168.1.101'),
('TK3456789012cdefgh', '2024-11-10 09:15:00', '2024-11-30 12:20:00', 67, '192.168.1.102'),
('TK4567890123defghi', '2024-11-15 16:30:00', '2024-11-28 20:10:00', 34, '192.168.1.103'),
('TK5678901234efghij', '2024-11-20 11:45:00', '2024-11-30 08:55:00', 12, '192.168.1.104');

-- Insert sensitive user data (target for advanced blind injection)
INSERT INTO users (username, password, email, role, secret_data) VALUES
('administrator', 'admin123!@#', 'admin@lab5.local', 'admin', 'FLAG{BLIND_SQLI_TIME_DELAY_SUCCESS}'),
('manager', 'manager456$%^', 'manager@lab5.local', 'manager', 'SECRET_API_KEY_987654321'),
('analyst', 'analyst789&*(', 'analyst@lab5.local', 'user', 'DATABASE_BACKUP_PASSWORD_2024'),
('developer', 'dev123pass', 'dev@lab5.local', 'user', 'ENCRYPTION_KEY_ABCDEF123456'),
('support', 'support999', 'support@lab5.local', 'user', 'MAINTENANCE_TOKEN_XYZ789');

-- Create indexes for better performance
CREATE INDEX idx_analytics_tracking ON analytics(tracking_id);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_users_username ON users(username);