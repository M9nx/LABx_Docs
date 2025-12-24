-- Lab 9: Insecure Direct Object References - Chat Log IDOR
-- Database Setup Script

-- Create database
CREATE DATABASE IF NOT EXISTS ac_lab9;
USE ac_lab9;

-- Drop existing tables
DROP TABLE IF EXISTS chat_logs;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    api_key VARCHAR(64),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create chat_logs table (stores transcript file references)
CREATE TABLE chat_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    filename VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample users
-- Note: In a real vulnerable lab, passwords are stored in plain text intentionally
INSERT INTO users (username, password, email, role, api_key) VALUES
('carlos', 'h5a2xfj8k3', 'carlos@example.com', 'user', 'api-carlos-secret-key-12345'),
('wiener', 'peter', 'wiener@example.com', 'user', 'api-wiener-key-67890'),
('administrator', 'admin123', 'admin@example.com', 'admin', 'api-admin-master-key-99999'),
('support', 'support2024', 'support@example.com', 'user', 'api-support-key-11111');

-- Insert chat log records (filename references to static files)
INSERT INTO chat_logs (user_id, filename) VALUES
(1, '1.txt'),   -- carlos's chat log with password revealed
(2, '2.txt'),   -- wiener's chat log
(3, '3.txt'),   -- admin's chat log
(4, '4.txt');   -- support's chat log

-- Verification
SELECT 'Database setup complete!' AS status;
SELECT * FROM users;
SELECT * FROM chat_logs;