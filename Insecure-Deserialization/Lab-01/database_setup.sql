-- Lab 01: Modifying Serialized Objects
-- Insecure Deserialization - Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS deserial_lab1;
USE deserial_lab1;

-- Drop existing tables
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
-- Note: Passwords should be hashed with password_hash() in PHP
-- These are placeholder hashes for: admin_secret_pass, carlos123, peter
INSERT INTO users (username, password, email, full_name, role) VALUES
('administrator', '$2y$10$placeholder_hash_admin', 'admin@seriallab.com', 'Administrator', 'admin'),
('carlos', '$2y$10$placeholder_hash_carlos', 'carlos@example.com', 'Carlos Rodriguez', 'user'),
('wiener', '$2y$10$placeholder_hash_wiener', 'wiener@example.com', 'Peter Wiener', 'user');

-- Note: Use setup_db.php to properly initialize the database with correctly hashed passwords
