-- Lab 4: SQL Injection UNION Attack Database Schema
-- Drop database if exists and create new one
DROP DATABASE IF EXISTS lab4_sqli_union;
CREATE DATABASE lab4_sqli_union;
USE lab4_sqli_union;

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    image_url VARCHAR(255)
);

-- Create users table (target for UNION attack)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample products
INSERT INTO products (name, description, price, category, image_url) VALUES
('Laptop Pro 15', 'High-performance laptop with 16GB RAM', 1299.99, 'Electronics', 'laptop.jpg'),
('Wireless Headphones', 'Noise-cancelling wireless headphones', 199.99, 'Electronics', 'headphones.jpg'),
('Office Chair', 'Ergonomic office chair with lumbar support', 299.99, 'Furniture', 'chair.jpg'),
('Coffee Table', 'Modern glass coffee table', 199.99, 'Furniture', 'table.jpg'),
('Running Shoes', 'Lightweight running shoes for men', 89.99, 'Sports', 'shoes.jpg'),
('Yoga Mat', 'Premium non-slip yoga mat', 29.99, 'Sports', 'mat.jpg'),
('Smartphone X', 'Latest smartphone with 128GB storage', 799.99, 'Electronics', 'phone.jpg'),
('Desk Lamp', 'LED desk lamp with adjustable brightness', 49.99, 'Furniture', 'lamp.jpg'),
('Tennis Racket', 'Professional tennis racket', 149.99, 'Sports', 'racket.jpg'),
('Gaming Mouse', 'High-precision gaming mouse', 79.99, 'Electronics', 'mouse.jpg');

-- Insert users (including admin credentials to be discovered)
INSERT INTO users (username, password, email, role) VALUES
('administrator', 'admin123!@#', 'admin@lab4.local', 'admin'),
('john_doe', 'password123', 'john@example.com', 'user'),
('jane_smith', 'mypassword', 'jane@example.com', 'user'),
('bob_wilson', 'bobsecret', 'bob@example.com', 'user'),
('alice_cooper', 'alicepass', 'alice@example.com', 'user'),
('test_user', 'testpass', 'test@example.com', 'user');

-- Create a simple sessions table for login tracking
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);