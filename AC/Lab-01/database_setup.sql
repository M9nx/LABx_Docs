-- SecureShop Lab1 Database Setup
-- This script creates the database and sample data for the Access Control lab

-- Create database
CREATE DATABASE IF NOT EXISTS secureshop_lab1;
USE secureshop_lab1;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    address TEXT,
    phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users (passwords are hashed versions of simple passwords for lab purposes)
-- IMPORTANT: Hash below is for 'password'. Use setup_db.php for proper username123 passwords!
-- Password hashes generated using PHP's password_hash() function
INSERT INTO users (username, password, email, full_name, role, address, phone, notes) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@secureshop.com', 'Administrator', 'admin', '123 Admin St, Admin City', '+1-555-0001', 'Super admin account with full access'),
('carlos', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlos@example.com', 'Carlos Rodriguez', 'user', '456 User Ave, User Town', '+1-555-0002', 'Regular user account for Carlos - TARGET FOR DELETION'),
('alice', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alice@example.com', 'Alice Johnson', 'user', '789 Customer Blvd, Customer City', '+1-555-0003', 'Premium customer account'),
('bob', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bob@example.com', 'Bob Smith', 'user', '321 Buyer Lane, Buyer Village', '+1-555-0004', 'Frequent buyer with loyalty status'),
('eve', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eve@example.com', 'Eve Wilson', 'user', '654 Shopper St, Shopper Town', '+1-555-0005', 'New customer account');

-- Verify the data insertion
SELECT id, username, email, full_name, role, created_at FROM users;

-- Show password information for lab purposes
SELECT 
    username,
    CASE username
        WHEN 'admin' THEN 'admin123'
        WHEN 'carlos' THEN 'carlos123'  
        WHEN 'alice' THEN 'alice123'
        WHEN 'bob' THEN 'bob123'
        WHEN 'eve' THEN 'eve123'
    END as plain_password,
    role
FROM users;