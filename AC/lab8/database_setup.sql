-- Lab 8: User ID controlled by request parameter with password disclosure
-- Database Setup Script

CREATE DATABASE IF NOT EXISTS lab8_password;
USE lab8_password;

-- Drop existing tables
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users (passwords stored in plaintext for lab purposes)
INSERT INTO users (username, password, email, role) VALUES
('administrator', 'x4dm1n_s3cr3t_p@ss!', 'admin@passlab.local', 'admin'),
('wiener', 'peter', 'wiener@passlab.local', 'user'),
('carlos', 'montoya', 'carlos@passlab.local', 'user'),
('alice', 'wonderland123', 'alice@passlab.local', 'user'),
('bob', 'builder456', 'bob@passlab.local', 'user');

-- Verify data
SELECT id, username, email, role FROM users;