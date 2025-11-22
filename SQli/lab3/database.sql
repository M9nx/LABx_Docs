-- Lab 3: SQL Injection UNION Attack Lab Database
CREATE DATABASE IF NOT EXISTS lab3_vulnerable_shop;
USE lab3_vulnerable_shop;

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    category_id INT,
    image_url VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Users table (sensitive data that shouldn't be accessible)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Electronic devices and gadgets'),
('Clothing', 'Fashionable clothing items'),
('Books', 'Educational and entertainment books'),
('Home & Garden', 'Items for home and garden improvement'),
('Sports', 'Sports equipment and accessories');

-- Insert products
INSERT INTO products (name, description, price, category_id, image_url, stock_quantity) VALUES
('Smartphone X1', 'Latest smartphone with advanced features', 699.99, 1, 'smartphone.jpg', 50),
('Laptop Pro', 'High-performance laptop for professionals', 1299.99, 1, 'laptop.jpg', 25),
('Wireless Headphones', 'Premium noise-canceling headphones', 199.99, 1, 'headphones.jpg', 100),
('Designer Jeans', 'Trendy designer jeans', 89.99, 2, 'jeans.jpg', 75),
('Cotton T-Shirt', 'Comfortable cotton t-shirt', 19.99, 2, 'tshirt.jpg', 200),
('Winter Jacket', 'Warm winter jacket', 149.99, 2, 'jacket.jpg', 40),
('Programming Guide', 'Complete programming tutorial', 49.99, 3, 'programming.jpg', 30),
('Mystery Novel', 'Bestselling mystery novel', 14.99, 3, 'novel.jpg', 60),
('Cookery Book', 'Professional cooking recipes', 24.99, 3, 'cookbook.jpg', 35),
('Garden Tools Set', 'Complete set of garden tools', 79.99, 4, 'tools.jpg', 20),
('Flower Seeds', 'Mixed flower seeds collection', 9.99, 4, 'seeds.jpg', 150),
('Running Shoes', 'Professional running shoes', 119.99, 5, 'shoes.jpg', 80),
('Tennis Racket', 'Professional tennis racket', 89.99, 5, 'racket.jpg', 15);

-- Insert sensitive user data (this is what attackers will try to access)
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('john_doe', 'john@email.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'user'),
('jane_smith', 'jane@email.com', '$2y$10$ZdjQbqnJSKpamf4ZkKqAqePL8M5b.XnJVEIq8QzpYOVuE5A1tKzgy', 'user'),
('bob_wilson', 'bob@email.com', '$2y$10$Rn7V7KsP4qPf8a3F9Y8d2.dK5L6nY8pT3H9kQ2jE0sZ1eR4wG5vL2', 'user'),
('secret_user', 'secret@internal.com', '$2y$10$X1Y2Z3A4B5C6D7E8F9G0H1I2J3K4L5M6N7O8P9Q0R1S2T3U4V5W6X7', 'admin');