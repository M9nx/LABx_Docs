-- SQL Injection Lab 2 - Login Bypass Database Setup
-- Run this script to create the login bypass lab database

-- Create database
CREATE DATABASE IF NOT EXISTS sqli_lab2;
USE sqli_lab2;

-- Drop table if exists (for clean setup)
DROP TABLE IF EXISTS users;

-- Create users table for login system
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert user accounts
INSERT INTO users (username, password, role, email) VALUES
('administrator', 'superSecretAdminPass123!', 'admin', 'admin@company.com'),
('john_doe', 'password123', 'user', 'john@company.com'),
('jane_smith', 'mypassword456', 'user', 'jane@company.com'),
('bob_wilson', 'bobsecret789', 'user', 'bob@company.com'),
('alice_brown', 'alicepass321', 'manager', 'alice@company.com');

-- Verify data insertion
SELECT 'User accounts created:' as info;
SELECT id, username, role, email FROM users;

SELECT 'Admin account (target):' as info;
SELECT id, username, role, email FROM users WHERE role = 'admin';