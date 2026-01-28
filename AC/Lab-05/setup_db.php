<?php
// Reset lab progress
require_once '../progress.php';
resetLab(5);

/**
 * Lab 5 Database Setup Script
 * Run this file in browser to create the database
 */

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

echo "<h2>Lab 5: IDOR - Database Setup</h2>";

try {
    // Connect without database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS lab5_idor");
    echo "<p>✓ Database 'lab5_idor' created</p>";
    
    // Use database
    $pdo->exec("USE lab5_idor");
    
    // Drop existing table
    $pdo->exec("DROP TABLE IF EXISTS users");
    echo "<p>✓ Cleaned existing tables</p>";
    
    // Create users table
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
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
        )
    ");
    echo "<p>✓ Users table created</p>";
    
    // Insert sample users
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password, email, full_name, api_key, department, phone, address, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $users = [
        ['administrator', 'admin123', 'admin@idorlab.local', 'System Administrator', 'sk-admin-9f8e7d6c5b4a3210-secretkey', 'IT Security', '+1-555-0100', '100 Admin Tower, Secure City, SC 10000', 'Master admin account. Full system access.'],
        ['carlos', 'carlos123', 'carlos@idorlab.local', 'Carlos Rodriguez', 'sk-carlos-a1b2c3d4e5f6g7h8-targetkey', 'Engineering', '+1-555-0201', '201 Engineering Ave, Tech District, TD 20100', 'Senior Engineer. Working on Project Alpha. API key grants access to internal systems.'],
        ['wiener', 'peter', 'wiener@idorlab.local', 'Peter Wiener', 'sk-wiener-z9y8x7w6v5u4t3s2-userkey', 'Development', '+1-555-0301', '301 Developer Lane, Code City, CC 30100', 'Junior developer. Standard user account.'],
        ['alice', 'alice123', 'alice@idorlab.local', 'Alice Johnson', 'sk-alice-m1n2o3p4q5r6s7t8-financekey', 'Finance', '+1-555-0401', '401 Finance Blvd, Money Town, MT 40100', 'Senior accountant. Access to financial systems.'],
        ['bob', 'bob123', 'bob@idorlab.local', 'Bob Smith', 'sk-bob-u9v8w7x6y5z4a3b2-hrkey', 'Human Resources', '+1-555-0501', '501 HR Street, People City, PC 50100', 'HR manager. Employee data access.']
    ];
    
    foreach ($users as $user) {
        $stmt->execute($user);
    }
    echo "<p>✓ Sample users inserted (5 users)</p>";
    
    // Verify
    $result = $pdo->query("SELECT id, username, api_key FROM users ORDER BY id");
    echo "<h3>Users Created:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Username</th><th>API Key</th></tr>";
    while ($row = $result->fetch()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['api_key']}</td></tr>";
    }
    echo "</table>";
    
    echo "<br><p style='color: green; font-weight: bold;'>✓ Lab 5 database setup complete!</p>";
    echo "<p><a href='index.php'>Go to Lab 5</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

