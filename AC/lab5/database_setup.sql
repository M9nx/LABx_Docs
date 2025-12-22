-- Lab 5: User ID Controlled by Request Parameter
-- Database Setup Script
-- 
-- INTENTIONALLY VULNERABLE - FOR EDUCATIONAL PURPOSES ONLY

-- Create database
CREATE DATABASE IF NOT EXISTS lab5_idor;
USE lab5_idor;

-- Drop existing tables
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    api_key VARCHAR(64) NOT NULL,
    department VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Insert sample users with API keys
-- The goal is to obtain carlos's API key

INSERT INTO users (username, password, email, full_name, api_key, department, phone, address, notes) VALUES
('administrator', 'admin123', 'admin@idorlab.local', 'System Administrator', 'sk-admin-9f8e7d6c5b4a3210-secretkey', 'IT Security', '+1-555-0100', '100 Admin Tower, Secure City, SC 10000', 'Master admin account. Full system access.'),
('carlos', 'carlos123', 'carlos@idorlab.local', 'Carlos Rodriguez', 'sk-carlos-a1b2c3d4e5f6g7h8-targetkey', 'Engineering', '+1-555-0201', '201 Engineering Ave, Tech District, TD 20100', 'Senior Engineer. Working on Project Alpha. API key grants access to internal systems.'),
('wiener', 'peter', 'wiener@idorlab.local', 'Peter Wiener', 'sk-wiener-z9y8x7w6v5u4t3s2-userkey', 'Development', '+1-555-0301', '301 Developer Lane, Code City, CC 30100', 'Junior developer. Standard user account.'),
('alice', 'alice123', 'alice@idorlab.local', 'Alice Johnson', 'sk-alice-m1n2o3p4q5r6s7t8-financekey', 'Finance', '+1-555-0401', '401 Finance Blvd, Money Town, MT 40100', 'Senior accountant. Access to financial systems.'),
('bob', 'bob123', 'bob@idorlab.local', 'Bob Smith', 'sk-bob-u9v8w7x6y5z4a3b2-hrkey', 'Human Resources', '+1-555-0501', '501 HR Street, People City, PC 50100', 'HR manager. Employee data access.');

-- Verify data
SELECT id, username, email, api_key FROM users ORDER BY id;
