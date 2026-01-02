<?php
/**
 * Lab 01: SecureShop Database Setup
 * Unprotected Admin Panel Vulnerability
 */

$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'secureshop_lab1';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
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
    
    // Clear and reset
    $pdo->exec("TRUNCATE TABLE users");
    
    // Insert users
    $users = [
        ['admin', 'admin123', 'admin@secureshop.com', 'Administrator', 'admin', '123 Admin St, Admin City', '+1-555-0001', 'Super admin account with full access'],
        ['carlos', 'carlos123', 'carlos@example.com', 'Carlos Rodriguez', 'user', '456 User Ave, User Town', '+1-555-0002', 'Regular user account for Carlos'],
        ['alice', 'alice123', 'alice@example.com', 'Alice Johnson', 'user', '789 Customer Blvd, Customer City', '+1-555-0003', 'Premium customer account'],
        ['bob', 'bob123', 'bob@example.com', 'Bob Smith', 'user', '321 Buyer Lane, Buyer Village', '+1-555-0004', 'Frequent buyer with loyalty status'],
        ['eve', 'eve123', 'eve@example.com', 'Eve Wilson', 'user', '654 Shopper St, Shopper Town', '+1-555-0005', 'New customer account']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role, address, phone, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($users as $u) {
        $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3], $u[4], $u[5], $u[6], $u[7]]);
    }
    
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - Lab 01</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .card { background: white; border-radius: 20px; padding: 3rem; max-width: 600px; width: 100%; box-shadow: 0 25px 50px rgba(0,0,0,0.2); }
        h1 { color: #667eea; margin-bottom: 1.5rem; }
        .success { background: #d1fae5; border-left: 4px solid #10b981; padding: 1rem; border-radius: 8px; color: #065f46; margin-bottom: 1.5rem; }
        h2 { color: #333; font-size: 1.2rem; margin: 1.5rem 0 1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; color: #667eea; }
        code { background: #f3f4f6; padding: 0.2rem 0.5rem; border-radius: 4px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 1rem 2rem; border-radius: 10px; text-decoration: none; font-weight: 600; margin-top: 1.5rem; }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class='card'>
        <h1>✅ Lab 01 Database Ready!</h1>
        <div class='success'><strong>Database '$dbname' created successfully!</strong></div>
        
        <h2>👥 Test Accounts</h2>
        <table>
            <tr><th>Username</th><th>Password</th><th>Role</th></tr>
            <tr><td><code>admin</code></td><td>admin123</td><td>admin</td></tr>
            <tr><td><code>carlos</code></td><td>carlos123</td><td>user</td></tr>
            <tr><td><code>alice</code></td><td>alice123</td><td>user</td></tr>
            <tr><td><code>bob</code></td><td>bob123</td><td>user</td></tr>
            <tr><td><code>eve</code></td><td>eve123</td><td>user</td></tr>
        </table>
        
        <h2>🎯 Vulnerability</h2>
        <p>Admin panel at <code>/administrator-panel.php</code> has no authentication!</p>
        
        <a href='index.php' class='btn'>🚀 Start Lab</a>
    </div>
</body>
</html>";

} catch (PDOException $e) {
    echo "<h1>Database Error</h1><p style='color:red;'>" . $e->getMessage() . "</p>";
}
?>
