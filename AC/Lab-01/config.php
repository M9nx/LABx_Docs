<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'secureshop_lab1');

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
                role ENUM('admin', 'user') DEFAULT 'user',
                address TEXT,
                phone VARCHAR(20),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Insert sample users if table is empty
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $users = [
                ['admin', 'admin123', 'admin@secureshop.com', 'Administrator', 'admin', '123 Admin St, Admin City', '+1-555-0001', 'Super admin account with full access'],
                ['carlos', 'carlos123', 'carlos@example.com', 'Carlos Rodriguez', 'user', '456 User Ave, User Town', '+1-555-0002', 'Regular user account for Carlos'],
                ['alice', 'alice123', 'alice@example.com', 'Alice Johnson', 'user', '789 Customer Blvd, Customer City', '+1-555-0003', 'Premium customer account'],
                ['bob', 'bob123', 'bob@example.com', 'Bob Smith', 'user', '321 Buyer Lane, Buyer Village', '+1-555-0004', 'Frequent buyer with loyalty status'],
                ['eve', 'eve123', 'eve@example.com', 'Eve Wilson', 'user', '654 Shopper St, Shopper Town', '+1-555-0005', 'New customer account']
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO users (username, password, email, full_name, role, address, phone, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($users as $user) {
                $hashedPassword = password_hash($user[1], PASSWORD_DEFAULT);
                $stmt->execute([$user[0], $hashedPassword, $user[2], $user[3], $user[4], $user[5], $user[6], $user[7]]);
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