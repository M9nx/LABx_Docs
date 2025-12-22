-- Lab 4: User Role Can Be Modified in User Profile
-- Database Setup Script
-- 
-- INTENTIONALLY VULNERABLE - FOR EDUCATIONAL PURPOSES ONLY

-- Create database
CREATE DATABASE IF NOT EXISTS lab4_rolemod;
USE lab4_rolemod;

-- Drop existing tables
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS audit_log;

-- Create users table with roleid field
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    roleid INT NOT NULL DEFAULT 1,  -- 1 = regular user, 2 = admin
    department VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    notes TEXT,
    api_key VARCHAR(64),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Create audit log table
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
-- Admin user (roleid = 2)
INSERT INTO users (username, password, email, full_name, roleid, department, phone, address, notes, api_key) VALUES
('administrator', 'admin123', 'admin@rolelab.local', 'System Administrator', 2, 'IT Security', '+1-555-0100', '100 Admin Tower, Secure City, SC 10000', 'Master admin account with full system access. Has access to all user management functions.', 'sk_admin_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6');

-- Target user to delete (roleid = 1)
INSERT INTO users (username, password, email, full_name, roleid, department, phone, address, notes, api_key) VALUES
('carlos', 'carlos123', 'carlos@rolelab.local', 'Carlos Rodriguez', 1, 'Marketing', '+1-555-0201', '201 Marketing Ave, Business District, BD 20100', 'Marketing team lead. Working on Q4 campaign strategy.', 'sk_carlos_q7w8e9r0t1y2u3i4o5p6a7s8d9f0g1h2');

-- Attacker's account (roleid = 1) - this is what user will use
INSERT INTO users (username, password, email, full_name, roleid, department, phone, address, notes, api_key) VALUES
('wiener', 'peter', 'wiener@rolelab.local', 'Peter Wiener', 1, 'Development', '+1-555-0301', '301 Developer Lane, Code City, CC 30100', 'Junior developer. Recently joined the team.', 'sk_wiener_z1x2c3v4b5n6m7k8j9h0g1f2d3s4a5w6');

-- Additional regular users
INSERT INTO users (username, password, email, full_name, roleid, department, phone, address, notes, api_key) VALUES
('alice', 'alice123', 'alice@rolelab.local', 'Alice Johnson', 1, 'Finance', '+1-555-0401', '401 Finance Blvd, Money Town, MT 40100', 'Senior accountant. Handles quarterly reports.', 'sk_alice_p0o9i8u7y6t5r4e3w2q1a2s3d4f5g6h7'),
('bob', 'bob123', 'bob@rolelab.local', 'Bob Smith', 1, 'HR', '+1-555-0501', '501 HR Street, People City, PC 50100', 'HR manager. Manages employee onboarding.', 'sk_bob_m1n2b3v4c5x6z7l8k9j0h1g2f3d4s5a6'),
('manager', 'manager123', 'manager@rolelab.local', 'Sarah Manager', 2, 'Operations', '+1-555-0601', '601 Operations Center, Control City, CC 60100', 'Operations manager with admin access for user management.', 'sk_manager_q1a2z3w4s5x6e7d8c9r0f1v2t3g4b5y6');

-- Verify data
SELECT id, username, email, roleid, department FROM users ORDER BY id;
