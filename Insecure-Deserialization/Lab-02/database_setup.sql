-- ===========================================
-- Lab 02: Modifying Serialized Data Types
-- Database Setup SQL
-- ===========================================
-- This SQL file creates the database schema for Lab 02
-- Vulnerability: PHP Type Juggling in session validation
-- ===========================================

-- Create database
DROP DATABASE IF EXISTS deserial_lab2;
CREATE DATABASE deserial_lab2;
USE deserial_lab2;

-- ===========================================
-- USERS TABLE
-- ===========================================
-- The access_token column is compared using loose comparison (==)
-- This allows type juggling attacks where integer 0 == "any_string"

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    access_token VARCHAR(64) NOT NULL COMMENT 'Validated with vulnerable loose comparison',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- SEED DATA
-- ===========================================
-- Passwords are hashed using PHP password_hash()
-- For manual setup, you may need to generate new hashes

-- Default password hashes (BCrypt):
-- administrator: admin_secret_pass
-- carlos: carlos123  
-- wiener: peter

INSERT INTO users (username, password, email, full_name, role, access_token) VALUES
(
    'administrator',
    '$2y$10$9o6SBAaqujv1TcFpdjTQVOmipq2GhTvBdFFEElR0gOHRFrPkp7DCW',
    'admin@seriallab.com',
    'Administrator',
    'admin',
    'a1b2c3d4e5f67890abcdef1234567890abcdef1234567890abcdef1234567890'
),
(
    'carlos',
    '$2y$10$gmr2C2c/RIxpsoKOMaETpuvUu/mpGa5NJpoEowBJcJxJLfTTR8ogK',
    'carlos@example.com',
    'Carlos Rodriguez',
    'user',
    'deadbeef1234567890abcdef1234567890abcdef1234567890abcdef12345678'
),
(
    'wiener',
    '$2y$10$g3YwKn2ecA/Sa2H86XjvUese/8s19dj.CMK53YQR13ROMDord9pj2',
    'wiener@example.com',
    'Peter Wiener',
    'user',
    'cafebabe0987654321fedcba9876543210fedcba0987654321fedcba98765432'
);

-- ===========================================
-- VERIFY SETUP
-- ===========================================
-- Check that all users were created correctly

SELECT 
    id,
    username,
    role,
    LENGTH(access_token) as token_length,
    created_at
FROM users
ORDER BY id;

-- ===========================================
-- VULNERABILITY EXPLANATION
-- ===========================================
-- The vulnerability is in PHP code, not SQL.
-- The access_token is validated using loose comparison:
--
--   if ($sessionData->access_token == $user['access_token'])
--
-- When the serialized access_token is changed from a string to integer 0:
--   Original: s:64:"a1b2c3d4...";  (string)
--   Exploit:  i:0;                  (integer)
--
-- PHP type juggling causes: 0 == "a1b2c3d4..." to evaluate TRUE
-- This bypasses authentication allowing access to any account.
-- ===========================================
