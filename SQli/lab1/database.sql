-- SQL Injection Lab Database Setup
-- Run this script to create the vulnerable lab database

-- Create database
CREATE DATABASE IF NOT EXISTS sqli_lab;
USE sqli_lab;

-- Drop table if exists (for clean setup)
DROP TABLE IF EXISTS products;

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    is_released TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert released products (visible to public)
INSERT INTO products (name, category, description, price, is_released) VALUES
('iPhone 15', 'electronics', 'Latest Apple smartphone with advanced features', 999.99, 1),
('Samsung Galaxy S24', 'electronics', 'Flagship Android phone with AI capabilities', 849.99, 1),
('MacBook Air', 'computers', 'Lightweight laptop perfect for everyday use', 1199.99, 1);

-- Insert unreleased products (hidden from public - these should be revealed by SQL injection)
INSERT INTO products (name, category, description, price, is_released) VALUES
('iPhone 16 Pro Max', 'electronics', 'CONFIDENTIAL: Next generation iPhone with revolutionary features', 1299.99, 0),
('Secret Gaming Laptop', 'computers', 'INTERNAL: High-performance gaming laptop - not yet announced', 2499.99, 0),
('Project Alpha Headphones', 'audio', 'TOP SECRET: Noise-canceling headphones with AI integration', 599.99, 0);

-- Verify data insertion
SELECT 'Released products:' as info;
SELECT * FROM products WHERE is_released = 1;

SELECT 'Unreleased products (should be hidden):' as info;
SELECT * FROM products WHERE is_released = 0;

SELECT 'All products:' as info;
SELECT * FROM products;