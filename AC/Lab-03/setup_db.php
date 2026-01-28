<?php
/**
 * Lab 03: CookieAuth Database Setup
 * Cookie-Based Role Manipulation Vulnerability
 */

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];
$dbname = 'lab3_db';

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
            role ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Clear and reset
    $pdo->exec("DELETE FROM users");
    
    // Insert users (password: password)
    // Hash for 'password' is $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
    $hash = password_hash('password', PASSWORD_DEFAULT);
    $users = [
        ['admin', $hash, 'admin@lab3.com', 'Administrator', 'admin'],
        ['carlos', $hash, 'carlos@lab3.com', 'Carlos Rodriguez', 'user'],
        ['wiener', $hash, 'wiener@lab3.com', 'Peter Wiener', 'user'],
        ['alice', $hash, 'alice@lab3.com', 'Alice Johnson', 'user'],
        ['bob', $hash, 'bob@lab3.com', 'Bob Smith', 'user'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute($u);
    }
    
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - Lab 03</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,68,68,0.3); border-radius: 20px; padding: 3rem; max-width: 600px; width: 100%; backdrop-filter: blur(10px); }
        h1 { color: #ff4444; margin-bottom: 1.5rem; }
        .success { background: rgba(16,185,129,0.2); border-left: 4px solid #10b981; padding: 1rem; border-radius: 8px; color: #6ee7b7; margin-bottom: 1.5rem; }
        h2 { color: #e0e0e0; font-size: 1.2rem; margin: 1.5rem 0 1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid rgba(255,68,68,0.2); color: #e0e0e0; }
        th { color: #ff4444; }
        code { background: rgba(255,68,68,0.2); color: #ff6666; padding: 0.2rem 0.5rem; border-radius: 4px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #ff4444, #cc0000); color: white; padding: 1rem 2rem; border-radius: 10px; text-decoration: none; font-weight: 600; margin-top: 1.5rem; }
        .hint { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.3); padding: 1rem; border-radius: 8px; margin-top: 1rem; color: #ff6666; }
    </style>
</head>
<body>
    <div class='card'>
        <h1>✅ Lab 03 Database Ready!</h1>
        <div class='success'><strong>Database '$dbname' created successfully!</strong></div>
        
        <h2>👥 Test Accounts</h2>
        <table>
            <tr><th>Username</th><th>Password</th><th>Role</th></tr>
            <tr><td><code>admin</code></td><td>password123</td><td>admin</td></tr>
            <tr><td><code>carlos</code></td><td>password123</td><td>user</td></tr>
            <tr><td><code>wiener</code></td><td>password123</td><td>user</td></tr>
            <tr><td><code>alice</code></td><td>password123</td><td>user</td></tr>
            <tr><td><code>bob</code></td><td>password123</td><td>user</td></tr>
        </table>
        
        <div class='hint'>
            <strong>🎯 Vulnerability:</strong> Login sets a cookie with the user's role. Modify the cookie value to escalate privileges!
        </div>
        
        <a href='index.php' class='btn'>🚀 Start Lab</a>
    </div>
</body>
</html>";

} catch (PDOException $e) {
    echo "<h1>Database Error</h1><p style='color:red;'>" . $e->getMessage() . "</p>";
}
?>
