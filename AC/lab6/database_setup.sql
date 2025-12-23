-- Lab 6: User ID Controlled by Request Parameter with Unpredictable User IDs
-- Database Setup Script
-- 
-- INTENTIONALLY VULNERABLE - FOR EDUCATIONAL PURPOSES ONLY

-- Create database
CREATE DATABASE IF NOT EXISTS lab6_guid;
USE lab6_guid;

-- Drop existing tables
DROP TABLE IF EXISTS blog_posts;
DROP TABLE IF EXISTS users;

-- Create users table with GUID as identifier
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guid VARCHAR(36) NOT NULL UNIQUE,
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

-- Create blog posts table
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_guid VARCHAR(36) NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_guid) REFERENCES users(guid) ON DELETE CASCADE
);

-- Insert sample users with GUIDs
-- Carlos's GUID is the target - attackers need to find it through his blog posts

INSERT INTO users (guid, username, password, email, full_name, api_key, department, phone, address, notes) VALUES
('a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d', 'administrator', 'admin123', 'admin@guidlab.local', 'System Administrator', 'sk-admin-9f8e7d6c5b4a3210-masterkey', 'IT Security', '+1-555-0100', '100 Admin Tower, Secure City, SC 10000', 'Master admin account. Full system access.'),
('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'carlos', 'carlos123', 'carlos@guidlab.local', 'Carlos Rodriguez', 'sk-carlos-x7y8z9a0b1c2d3e4-targetkey', 'Engineering', '+1-555-0201', '201 Engineering Ave, Tech District, TD 20100', 'Senior Engineer. Working on Project Alpha. API key grants access to internal systems.'),
('8d7e6f5a-4b3c-2d1e-0f9a-8b7c6d5e4f3a', 'wiener', 'peter', 'wiener@guidlab.local', 'Peter Wiener', 'sk-wiener-m1n2o3p4q5r6s7t8-userkey', 'Development', '+1-555-0301', '301 Developer Lane, Code City, CC 30100', 'Junior developer. Standard user account.'),
('c9d8e7f6-a5b4-3c2d-1e0f-9a8b7c6d5e4f', 'alice', 'alice123', 'alice@guidlab.local', 'Alice Johnson', 'sk-alice-u9v8w7x6y5z4a3b2-financekey', 'Finance', '+1-555-0401', '401 Finance Blvd, Money Town, MT 40100', 'Senior accountant. Access to financial systems.'),
('b8c7d6e5-f4a3-2b1c-0d9e-8f7a6b5c4d3e', 'bob', 'bob123', 'bob@guidlab.local', 'Bob Smith', 'sk-bob-j1k2l3m4n5o6p7q8-hrkey', 'Human Resources', '+1-555-0501', '501 HR Street, People City, PC 50100', 'HR manager. Employee data access.');

-- Insert blog posts - Carlos's posts will reveal his GUID
INSERT INTO blog_posts (user_guid, title, content) VALUES
('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Introduction to Secure API Design', 'In this post, I will discuss the fundamentals of designing secure APIs. Authentication, rate limiting, and proper access control are essential components that every developer should understand...'),
('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'My Journey in Security Engineering', 'After 5 years working in security engineering, I have learned that the most common vulnerabilities often stem from simple oversights. Today I want to share some of my experiences and lessons learned...'),
('a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d', 'Security Best Practices for 2024', 'As we approach a new year, it is important to review and update our security practices. This post covers the top 10 security recommendations for modern web applications...'),
('c9d8e7f6-a5b4-3c2d-1e0f-9a8b7c6d5e4f', 'Financial Systems and Data Protection', 'Working in finance requires strict adherence to data protection regulations. Here are some key considerations when handling sensitive financial information...'),
('8d7e6f5a-4b3c-2d1e-0f9a-8b7c6d5e4f3a', 'Getting Started with Web Development', 'As a junior developer, I have been learning a lot about web development. In this post, I share my experience setting up my first development environment...');

-- Verify data
SELECT guid, username, api_key FROM users ORDER BY id;
SELECT p.title, u.username, p.user_guid FROM blog_posts p JOIN users u ON p.user_guid = u.guid;
