-- Lab 11: Method-based Access Control Bypass
-- Database Setup Script

CREATE DATABASE IF NOT EXISTS ac_lab11;
USE ac_lab11;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
-- Admin: administrator:admin
-- Test user: wiener:peter
-- Target: carlos:montoya
INSERT INTO users (username, password, email, role) VALUES
('administrator', 'admin', 'admin@securecorp.local', 'admin'),
('wiener', 'peter', 'wiener@test.local', 'user'),
('carlos', 'montoya', 'carlos@test.local', 'user'),
('john', 'password123', 'john@test.local', 'user'),
('alice', 'alice2024', 'alice@test.local', 'user');

SELECT id, username, role FROM users;
