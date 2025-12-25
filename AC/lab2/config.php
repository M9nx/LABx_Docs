<?php
// Database configuration for TechCorp Lab2
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'techcorp_lab2');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Initialize database and tables if they don't exist
function initDatabase() {
    try {
        // First, connect without specifying database to create it
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
        
        // Create users table
        $pdo->exec("
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
            )
        ");
        
        // Insert sample users if table is empty
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $users = [
                [
                    'admin', 'admin123', 'admin@techcorp.com', 'System Administrator', 'admin', 'IT Security',
                    'Chief Security Officer', 125000.00, '100 Corporate Blvd, Executive Floor, Tech City', 
                    '+1-555-0001', 'Emergency: +1-555-0911', 'top-secret',
                    'Root administrator account with full system access. Handles security audits and compliance.'
                ],
                [
                    'carlos', 'carlos123', 'carlos.rodriguez@techcorp.com', 'Carlos Rodriguez', 'user', 'Marketing',
                    'Marketing Specialist', 65000.00, '123 Sunset Ave, Marketing District, Tech City',
                    '+1-555-0002', 'Maria Rodriguez: +1-555-0922', 'basic',
                    'Marketing team member responsible for digital campaigns. TARGET USER FOR DELETION.'
                ],
                [
                    'sarah', 'sarah123', 'sarah.johnson@techcorp.com', 'Sarah Johnson', 'manager', 'Human Resources',
                    'HR Manager', 85000.00, '456 Professional Way, HR Quarter, Tech City',
                    '+1-555-0003', 'David Johnson: +1-555-0933', 'confidential',
                    'HR manager handling employee relations and sensitive personnel data.'
                ],
                [
                    'mike', 'mike123', 'mike.chen@techcorp.com', 'Michael Chen', 'user', 'Engineering',
                    'Senior Software Engineer', 95000.00, '789 Developer Lane, Tech Park, Tech City',
                    '+1-555-0004', 'Lisa Chen: +1-555-0944', 'secret',
                    'Lead engineer working on classified government contracts and secure systems.'
                ],
                [
                    'emma', 'emma123', 'emma.davis@techcorp.com', 'Emma Davis', 'user', 'Finance',
                    'Financial Analyst', 70000.00, '321 Finance Street, Business District, Tech City',
                    '+1-555-0005', 'Robert Davis: +1-555-0955', 'confidential',
                    'Finance team member with access to budget information and financial projections.'
                ],
                [
                    'alex', 'alex123', 'alex.thompson@techcorp.com', 'Alexander Thompson', 'manager', 'Operations',
                    'Operations Manager', 90000.00, '654 Operations Blvd, Industrial Zone, Tech City',
                    '+1-555-0006', 'Jennifer Thompson: +1-555-0966', 'secret',
                    'Operations manager overseeing facility security and logistics coordination.'
                ],
                [
                    'lisa', 'lisa123', 'lisa.martinez@techcorp.com', 'Lisa Martinez', 'user', 'Legal',
                    'Legal Counsel', 110000.00, '987 Legal Plaza, Law District, Tech City',
                    '+1-555-0007', 'Carlos Martinez: +1-555-0977', 'top-secret',
                    'Corporate legal counsel handling contracts and compliance with government regulations.'
                ]
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO users (username, password, email, full_name, role, department, position, salary, 
                                 address, phone, emergency_contact, security_clearance, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($users as $user) {
                $hashedPassword = password_hash($user[1], PASSWORD_DEFAULT);
                $stmt->execute([
                    $user[0], $hashedPassword, $user[2], $user[3], $user[4], $user[5], $user[6], $user[7],
                    $user[8], $user[9], $user[10], $user[11], $user[12]
                ]);
            }
        }
        
        return $pdo;
    } catch(PDOException $e) {
        die("Database initialization failed: " . $e->getMessage());
    }
}

// Initialize the database when this file is included
initDatabase();
?>