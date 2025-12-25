-- Lab 18: IDOR Expire Other User Sessions
-- Database Schema and Seed Data

DROP DATABASE IF EXISTS ac_lab18;
CREATE DATABASE ac_lab18 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ac_lab18;

-- Users table (store owners/staff)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('owner', 'staff', 'admin') DEFAULT 'staff',
    store_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    api_key VARCHAR(64),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- User sessions table - tracks all active sessions
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(64) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    device_info VARCHAR(255),
    location VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_sessions (user_id, is_active),
    INDEX idx_session_token (session_token)
);

-- Session activity log
CREATE TABLE session_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    target_user_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Account settings
CREATE TABLE account_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    login_notifications BOOLEAN DEFAULT TRUE,
    session_timeout INT DEFAULT 1440,
    max_sessions INT DEFAULT 5,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users
INSERT INTO users (username, password, email, role, store_name, phone, address, api_key) VALUES
('admin', 'admin123', 'admin@shopstore.com', 'admin', 'ShopStore HQ', '+1-555-0100', '123 Admin Street, Tech City, TC 10001', 'sk_live_admin_key_7890abcdef'),
('victim_store', 'victim123', 'victim@mystore.com', 'owner', 'Victim\'s Fashion Store', '+1-555-0101', '456 Commerce Ave, Shop Town, ST 20002', 'sk_live_victim_key_1234567890'),
('attacker_store', 'attacker123', 'attacker@evilshop.com', 'owner', 'Attacker\'s Electronics', '+1-555-0102', '789 Hacker Lane, Dark Web, DW 30003', 'sk_live_attacker_key_0987654321'),
('staff_member', 'staff123', 'staff@shopstore.com', 'staff', 'ShopStore HQ', '+1-555-0103', '123 Admin Street, Tech City, TC 10001', NULL),
('another_victim', 'another123', 'another@victimshop.com', 'owner', 'Another Victim Shop', '+1-555-0104', '321 Target Blvd, Vulnerable City, VC 40004', 'sk_live_another_key_abcdef1234');

-- Insert account settings for each user
INSERT INTO account_settings (user_id, two_factor_enabled, login_notifications, session_timeout, max_sessions) VALUES
(1, TRUE, TRUE, 1440, 10),
(2, FALSE, TRUE, 720, 5),
(3, FALSE, FALSE, 720, 5),
(4, FALSE, TRUE, 480, 3),
(5, TRUE, TRUE, 1440, 5);

-- Create some initial sessions for users (simulating active logins)
INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, device_info, location, is_active, expires_at) VALUES
-- Admin sessions
(1, 'admin_session_token_001', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', 'Windows PC - Chrome', 'New York, US', TRUE, DATE_ADD(NOW(), INTERVAL 1 DAY)),
(1, 'admin_session_token_002', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X) Safari/17.0', 'MacBook Pro - Safari', 'New York, US', TRUE, DATE_ADD(NOW(), INTERVAL 1 DAY)),

-- Victim store sessions (these will be targeted)
(2, 'victim_session_token_001', '10.0.0.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/121.0', 'Windows PC - Firefox', 'Los Angeles, US', TRUE, DATE_ADD(NOW(), INTERVAL 12 HOUR)),
(2, 'victim_session_token_002', '10.0.0.51', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) Mobile Safari', 'iPhone 15 - Safari', 'Los Angeles, US', TRUE, DATE_ADD(NOW(), INTERVAL 12 HOUR)),
(2, 'victim_session_token_003', '10.0.0.52', 'Mozilla/5.0 (iPad; CPU OS 17_0) Safari', 'iPad Pro - Safari', 'Los Angeles, US', TRUE, DATE_ADD(NOW(), INTERVAL 12 HOUR)),

-- Attacker store session
(3, 'attacker_session_token_001', '172.16.0.99', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', 'Windows PC - Chrome', 'Unknown VPN', TRUE, DATE_ADD(NOW(), INTERVAL 12 HOUR)),

-- Staff member session
(4, 'staff_session_token_001', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edge/120.0', 'Windows PC - Edge', 'New York, US', TRUE, DATE_ADD(NOW(), INTERVAL 8 HOUR)),

-- Another victim sessions
(5, 'another_victim_token_001', '10.10.10.10', 'Mozilla/5.0 (Linux; Android 14) Chrome/120.0', 'Android Phone - Chrome', 'Chicago, US', TRUE, DATE_ADD(NOW(), INTERVAL 1 DAY)),
(5, 'another_victim_token_002', '10.10.10.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', 'Windows PC - Chrome', 'Chicago, US', TRUE, DATE_ADD(NOW(), INTERVAL 1 DAY));
