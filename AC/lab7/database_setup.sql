-- Lab 7: User ID controlled by request parameter with data leakage in redirect
-- Database Setup Script

CREATE DATABASE IF NOT EXISTS lab7_redirect;
USE lab7_redirect;

-- Drop existing tables
DROP TABLE IF EXISTS users;

-- Create users table with API keys
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    api_key VARCHAR(64) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users with API keys
INSERT INTO users (username, password, email, api_key, role) VALUES
('administrator', 'admin123', 'admin@redirectlab.local', 'ADMIN-KEY-a9f8e7d6c5b4a3210fedcba987654321', 'admin'),
('wiener', 'peter', 'wiener@redirectlab.local', 'USER-KEY-wiener-1234567890abcdef', 'user'),
('carlos', 'montoya', 'carlos@redirectlab.local', 'API-KEY-carlos-Xt7Kp9Qm2Wn5Bv8J', 'user'),
('alice', 'password123', 'alice@redirectlab.local', 'API-KEY-alice-Hj3Lm6Yn9Rp2Dk5F', 'user'),
('bob', 'secret456', 'bob@redirectlab.local', 'API-KEY-bob-Zw8Qc1Vx4Bt7Ng0K', 'user');

-- Verify data
SELECT id, username, email, api_key, role FROM users;