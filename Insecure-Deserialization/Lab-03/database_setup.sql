-- ===========================================
-- Lab 03: Using Application Functionality to Exploit Insecure Deserialization
-- Database Setup SQL
-- ===========================================
-- This SQL file creates the database schema for Lab 03
-- Vulnerability: Arbitrary file deletion via deserialized avatar_link
-- ===========================================

-- Create database
DROP DATABASE IF EXISTS deserial_lab3;
CREATE DATABASE deserial_lab3;
USE deserial_lab3;

-- ===========================================
-- USERS TABLE
-- ===========================================
-- The avatar_link column stores the path to the user's avatar file.
-- This path is also stored in the serialized session cookie.
-- When the account is deleted, the file at avatar_link is deleted.
-- VULNERABILITY: The avatar_link from the cookie is used, not from the database!

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    avatar_link VARCHAR(255) DEFAULT NULL COMMENT 'Path to user avatar file - VULNERABLE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- SEED DATA
-- ===========================================
-- Passwords are hashed using PHP password_hash()
-- avatar_link paths point to user home directories

-- Generate password hashes with PHP:
-- echo password_hash('peter', PASSWORD_DEFAULT);
-- echo password_hash('rosebud', PASSWORD_DEFAULT);
-- echo password_hash('montoya', PASSWORD_DEFAULT);

INSERT INTO users (username, password, email, full_name, avatar_link) VALUES
(
    'wiener',
    '$2y$10$g3YwKn2ecA/Sa2H86XjvUese/8s19dj.CMK53YQR13ROMDord9pj2',
    'wiener@example.com',
    'Peter Wiener',
    '/path/to/lab/home/wiener/avatar.jpg'
),
(
    'gregg',
    '$2y$10$Rz8KxqK9kJ5YqZ8qH3YqK.XqK9kJ5YqZ8qH3YqKzYqZ8qH3YqKzYq',
    'gregg@example.com',
    'Gregg Rosebud',
    '/path/to/lab/home/gregg/avatar.jpg'
),
(
    'carlos',
    '$2y$10$Yz9LxrL0lK6ZrA9rI4ZrL.YrL0lK6ZrA9rI4ZrLzZrA9rI4ZrLzZr',
    'carlos@example.com',
    'Carlos Montoya',
    '/path/to/lab/home/carlos/avatar.jpg'
);

-- ===========================================
-- VERIFY SETUP
-- ===========================================
SELECT 
    id,
    username,
    avatar_link,
    created_at
FROM users
ORDER BY id;

-- ===========================================
-- MANUAL FILE SETUP REQUIRED
-- ===========================================
-- After running this SQL, you must also:
-- 1. Create the home directories:
--    /home/wiener/
--    /home/gregg/
--    /home/carlos/
--
-- 2. Create avatar placeholder files:
--    /home/wiener/avatar.jpg
--    /home/gregg/avatar.jpg
--    /home/carlos/avatar.jpg
--
-- 3. Create the TARGET file:
--    /home/carlos/morale.txt
--
-- The setup_db.php script handles all of this automatically.
-- ===========================================

-- ===========================================
-- VULNERABILITY EXPLANATION
-- ===========================================
-- The vulnerability is in the account deletion logic:
--
-- VULNERABLE CODE (in config.php):
--   function deleteUserAccount($sessionData) {
--       // Gets avatar_link from DESERIALIZED COOKIE, not database!
--       $avatarPath = $sessionData->avatar_link;
--       
--       if (file_exists($avatarPath)) {
--           unlink($avatarPath);  // DELETES ANY FILE!
--       }
--       // ... delete user from database
--   }
--
-- ATTACK:
-- 1. Login and get session cookie
-- 2. Decode cookie to see serialized User object
-- 3. Modify avatar_link to point to /home/carlos/morale.txt
-- 4. Send DELETE request with modified cookie
-- 5. Server deletes morale.txt instead of your avatar!
-- ===========================================
