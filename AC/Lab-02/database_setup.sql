-- TechCorp Lab2 Database Setup
-- This script creates the database and sample data for the Access Control lab

-- Create database
CREATE DATABASE IF NOT EXISTS techcorp_lab2;
USE techcorp_lab2;

-- Create users table with extended corporate information
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user', 'manager') DEFAULT 'user',
    department VARCHAR(50),
    position VARCHAR(100),
    salary DECIMAL(10,2),
    address TEXT,
    phone VARCHAR(20),
    emergency_contact VARCHAR(100),
    security_clearance ENUM('none', 'basic', 'confidential', 'secret', 'top-secret') DEFAULT 'none',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Insert comprehensive sample users (passwords are hashed versions of simple passwords for lab purposes)
-- IMPORTANT: Hash below is for 'password'. Use setup_db.php for proper username123 passwords!
INSERT INTO users (username, password, email, full_name, role, department, position, salary, address, phone, emergency_contact, security_clearance, notes) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@techcorp.com', 'System Administrator', 'admin', 'IT Security', 'Chief Security Officer', 125000.00, '100 Corporate Blvd, Executive Floor, Tech City', '+1-555-0001', 'Emergency: +1-555-0911', 'top-secret', 'Root administrator account with full system access. Handles security audits and compliance.'),
('carlos', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlos.rodriguez@techcorp.com', 'Carlos Rodriguez', 'user', 'Marketing', 'Marketing Specialist', 65000.00, '123 Sunset Ave, Marketing District, Tech City', '+1-555-0002', 'Maria Rodriguez: +1-555-0922', 'basic', 'Marketing team member responsible for digital campaigns. TARGET USER FOR DELETION.'),
('sarah', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sarah.johnson@techcorp.com', 'Sarah Johnson', 'manager', 'Human Resources', 'HR Manager', 85000.00, '456 Professional Way, HR Quarter, Tech City', '+1-555-0003', 'David Johnson: +1-555-0933', 'confidential', 'HR manager handling employee relations and sensitive personnel data.'),
('mike', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mike.chen@techcorp.com', 'Michael Chen', 'user', 'Engineering', 'Senior Software Engineer', 95000.00, '789 Developer Lane, Tech Park, Tech City', '+1-555-0004', 'Lisa Chen: +1-555-0944', 'secret', 'Lead engineer working on classified government contracts and secure systems.'),
('emma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'emma.davis@techcorp.com', 'Emma Davis', 'user', 'Finance', 'Financial Analyst', 70000.00, '321 Finance Street, Business District, Tech City', '+1-555-0005', 'Robert Davis: +1-555-0955', 'confidential', 'Finance team member with access to budget information and financial projections.'),
('alex', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alex.thompson@techcorp.com', 'Alexander Thompson', 'manager', 'Operations', 'Operations Manager', 90000.00, '654 Operations Blvd, Industrial Zone, Tech City', '+1-555-0006', 'Jennifer Thompson: +1-555-0966', 'secret', 'Operations manager overseeing facility security and logistics coordination.'),
('lisa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lisa.martinez@techcorp.com', 'Lisa Martinez', 'user', 'Legal', 'Legal Counsel', 110000.00, '987 Legal Plaza, Law District, Tech City', '+1-555-0007', 'Carlos Martinez: +1-555-0977', 'top-secret', 'Corporate legal counsel handling contracts and compliance with government regulations.');

-- Verify the data insertion
SELECT id, username, email, full_name, role, department, salary, security_clearance FROM users;

-- Show password information for lab purposes
SELECT 
    username,
    CASE username
        WHEN 'admin' THEN 'admin123'
        WHEN 'carlos' THEN 'carlos123'  
        WHEN 'sarah' THEN 'sarah123'
        WHEN 'mike' THEN 'mike123'
        WHEN 'emma' THEN 'emma123'
        WHEN 'alex' THEN 'alex123'
        WHEN 'lisa' THEN 'lisa123'
    END as plain_password,
    role,
    department,
    position
FROM users;