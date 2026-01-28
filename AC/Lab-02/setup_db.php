<?php
/**
 * Lab 02: TechCorp Database Setup
 * Unpredictable Admin URL Vulnerability
 */

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];
$dbname = 'techcorp_lab2';

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

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
    
    // Clear and reset
    $pdo->exec("TRUNCATE TABLE users");
    
    // Insert users
    $users = [
        ['admin', 'admin123', 'admin@techcorp.com', 'System Administrator', 'admin', 'IT Security', 'Chief Security Officer', 125000.00, '100 Corporate Blvd', '+1-555-0001', 'Emergency: +1-555-0911', 'top-secret', 'Root admin with full access'],
        ['carlos', 'carlos123', 'carlos.rodriguez@techcorp.com', 'Carlos Rodriguez', 'user', 'Marketing', 'Marketing Specialist', 65000.00, '123 Sunset Ave', '+1-555-0002', 'Maria: +1-555-0922', 'basic', 'TARGET USER FOR DELETION'],
        ['sarah', 'sarah123', 'sarah.johnson@techcorp.com', 'Sarah Johnson', 'manager', 'Human Resources', 'HR Manager', 85000.00, '456 Professional Way', '+1-555-0003', 'David: +1-555-0933', 'confidential', 'HR manager'],
        ['mike', 'mike123', 'mike.chen@techcorp.com', 'Michael Chen', 'user', 'Engineering', 'Senior Engineer', 95000.00, '789 Developer Lane', '+1-555-0004', 'Lisa: +1-555-0944', 'secret', 'Lead engineer'],
        ['emma', 'emma123', 'emma.davis@techcorp.com', 'Emma Davis', 'user', 'Finance', 'Financial Analyst', 70000.00, '321 Finance Street', '+1-555-0005', 'Robert: +1-555-0955', 'confidential', 'Finance team'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role, department, position, salary, address, phone, emergency_contact, security_clearance, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($users as $u) {
        $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3], $u[4], $u[5], $u[6], $u[7], $u[8], $u[9], $u[10], $u[11], $u[12]]);
    }
    
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - Lab 02</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .card { background: white; border-radius: 20px; padding: 3rem; max-width: 700px; width: 100%; box-shadow: 0 25px 50px rgba(0,0,0,0.3); }
        h1 { color: #1e3a5f; margin-bottom: 1.5rem; }
        .success { background: #d1fae5; border-left: 4px solid #10b981; padding: 1rem; border-radius: 8px; color: #065f46; margin-bottom: 1.5rem; }
        h2 { color: #333; font-size: 1.2rem; margin: 1.5rem 0 1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; font-size: 0.9rem; }
        th { background: #f3f4f6; color: #1e3a5f; }
        code { background: #fef3c7; color: #92400e; padding: 0.2rem 0.5rem; border-radius: 4px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #1e3a5f, #0d1b2a); color: white; padding: 1rem 2rem; border-radius: 10px; text-decoration: none; font-weight: 600; margin-top: 1.5rem; }
        .hint { background: #fef3c7; border: 1px solid #fbbf24; padding: 1rem; border-radius: 8px; margin-top: 1rem; color: #92400e; }
    </style>
</head>
<body>
    <div class='card'>
        <h1>✅ Lab 02 Database Ready!</h1>
        <div class='success'><strong>Database '$dbname' created successfully!</strong></div>
        
        <h2>👥 Test Accounts</h2>
        <table>
            <tr><th>Username</th><th>Password</th><th>Role</th><th>Clearance</th></tr>
            <tr><td><code>admin</code></td><td>admin123</td><td>admin</td><td>top-secret</td></tr>
            <tr><td><code>carlos</code></td><td>carlos123</td><td>user</td><td>basic</td></tr>
            <tr><td><code>sarah</code></td><td>sarah123</td><td>manager</td><td>confidential</td></tr>
            <tr><td><code>mike</code></td><td>mike123</td><td>user</td><td>secret</td></tr>
            <tr><td><code>emma</code></td><td>emma123</td><td>user</td><td>confidential</td></tr>
        </table>
        
        <div class='hint'>
            <strong>🎯 Vulnerability:</strong> Hidden admin panel with obscure URL. Check <code>robots.txt</code> or source code for clues!
        </div>
        
        <a href='index.php' class='btn'>🚀 Start Lab</a>
    </div>
</body>
</html>";

} catch (PDOException $e) {
    echo "<h1>Database Error</h1><p style='color:red;'>" . $e->getMessage() . "</p>";
}
?>
