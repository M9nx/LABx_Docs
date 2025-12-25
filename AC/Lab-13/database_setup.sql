-- Lab 13: Referer-based Access Control
-- Database Setup Script

CREATE DATABASE IF NOT EXISTS ac_lab13;
USE ac_lab13;

-- Drop existing tables
DROP TABLE IF EXISTS access_logs;
DROP TABLE IF EXISTS users;

-- Users table with role management
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    department VARCHAR(50) DEFAULT 'General',
    api_key VARCHAR(64),
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Access logs for audit trail
CREATE TABLE access_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    target_user VARCHAR(50),
    referer_header VARCHAR(500),
    ip_address VARCHAR(45),
    success BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample users
INSERT INTO users (username, password, email, full_name, role, department, api_key) VALUES
('administrator', 'admin', 'admin@referer-lab.local', 'System Administrator', 'admin', 'IT Security', 'ADMIN-KEY-a8f7e6d5c4b3a291'),
('wiener', 'peter', 'wiener@referer-lab.local', 'Peter Wiener', 'user', 'Development', 'USER-KEY-b9e8f7d6c5a4b312'),
('carlos', 'montoya', 'carlos@referer-lab.local', 'Carlos Montoya', 'user', 'Marketing', 'USER-KEY-c0f9e8d7b6a5c423'),
('john', 'password123', 'john@referer-lab.local', 'John Smith', 'user', 'Sales', 'USER-KEY-d1a0f9e8c7b6d534'),
('alice', 'alice2024', 'alice@referer-lab.local', 'Alice Johnson', 'user', 'HR', 'USER-KEY-e2b1a0f9d8c7e645');

-- Display created data
SELECT username, role, email, department FROM users;
