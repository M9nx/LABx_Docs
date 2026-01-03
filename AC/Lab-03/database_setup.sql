-- Lab 3 Database Setup
-- User role controlled by request parameter vulnerability

-- Create database
CREATE DATABASE IF NOT EXISTS lab3_db;
USE lab3_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Clear existing users for fresh setup
DELETE FROM users;

-- Insert sample users (passwords are hashed versions of simple passwords for lab purposes)
-- All passwords use: password_hash('password', PASSWORD_DEFAULT)
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@lab3.com', 'Administrator', 'admin'),
('carlos', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlos@lab3.com', 'Carlos Rodriguez', 'user'),
('wiener', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'wiener@lab3.com', 'Peter Wiener', 'user'),
('alice', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alice@lab3.com', 'Alice Johnson', 'user'),
('bob', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bob@lab3.com', 'Bob Smith', 'user');

-- Verify the data insertion
SELECT id, username, email, full_name, role, created_at FROM users;

-- Show password information for lab purposes
SELECT 
    username,
    'password' as plain_password,
    role
FROM users;
