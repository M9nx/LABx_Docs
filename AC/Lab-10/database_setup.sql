-- Lab 10: URL-based Access Control Bypass via X-Original-URL Header
-- Database Setup Script

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS ac_lab10;
USE ac_lab10;

-- Drop existing tables
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
-- Test user credentials: wiener:peter
INSERT INTO users (username, password, email, role) VALUES
('administrator', 'secureAdminPass!2024', 'admin@securecorp.local', 'admin'),
('wiener', 'peter', 'wiener@test.local', 'user'),
('carlos', 'montoya123', 'carlos@test.local', 'user'),
('john', 'password123', 'john@test.local', 'user'),
('alice', 'alice2024', 'alice@test.local', 'user');

-- Verify data
SELECT id, username, role FROM users;
