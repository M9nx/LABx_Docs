-- Lab 12: Multi-step process with no access control on one step
-- Database: ac_lab12

CREATE DATABASE IF NOT EXISTS ac_lab12;
USE ac_lab12;

-- Drop existing tables
DROP TABLE IF EXISTS role_change_requests;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    full_name VARCHAR(100),
    department VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Create role change requests table (for audit trail)
CREATE TABLE role_change_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    target_username VARCHAR(50) NOT NULL,
    new_role ENUM('admin', 'user') NOT NULL,
    requested_by VARCHAR(50) NOT NULL,
    confirmed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
INSERT INTO users (username, password, email, role, full_name, department) VALUES
('administrator', 'admin', 'admin@multistep.local', 'admin', 'System Administrator', 'IT Security'),
('wiener', 'peter', 'wiener@multistep.local', 'user', 'Peter Wiener', 'Sales'),
('carlos', 'montoya', 'carlos@multistep.local', 'user', 'Carlos Montoya', 'Marketing'),
('john', 'password123', 'john@multistep.local', 'user', 'John Smith', 'Engineering'),
('alice', 'alice2024', 'alice@multistep.local', 'user', 'Alice Johnson', 'HR');

SELECT 'Database ac_lab12 setup complete!' AS Status;
