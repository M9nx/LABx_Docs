<?php
/**
 * Lab 01: Modifying Serialized Objects - Database Setup
 * Insecure Deserialization Vulnerability
 */

require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];
$dbname = 'deserial_lab1';

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fef3c7;border:1px solid #f59e0b;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
    // Drop and recreate users table
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert users
    $users = [
        ['administrator', 'admin_secret_pass', 'admin@seriallab.com', 'Administrator', 'admin'],
        ['carlos', 'carlos123', 'carlos@example.com', 'Carlos Rodriguez', 'user'],
        ['wiener', 'peter', 'wiener@example.com', 'Peter Wiener', 'user'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3], $u[4]]);
    }
    
    // Reset progress for this lab
    $pdo->exec("CREATE DATABASE IF NOT EXISTS id_progress");
    $pdo->exec("USE id_progress");
    $pdo->exec("CREATE TABLE IF NOT EXISTS solved_labs (lab_number INT PRIMARY KEY, solved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, reset_count INT DEFAULT 0)");
    $pdo->exec("DELETE FROM solved_labs WHERE lab_number = 1");
    
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - Lab 01</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 2rem; 
        }
        .card { 
            background: white; 
            border-radius: 20px; 
            padding: 3rem; 
            max-width: 700px; 
            width: 100%; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.2); 
        }
        h1 { color: #f97316; margin-bottom: 1.5rem; }
        .success { 
            background: #d1fae5; 
            border-left: 4px solid #10b981; 
            padding: 1rem; 
            border-radius: 8px; 
            color: #065f46; 
            margin-bottom: 1.5rem; 
        }
        h2 { color: #333; font-size: 1.2rem; margin: 1.5rem 0 1rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #fff7ed; color: #f97316; }
        code { background: #f3f4f6; padding: 0.2rem 0.5rem; border-radius: 4px; font-family: monospace; }
        .vuln-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .vuln-box h3 { color: #b45309; margin-bottom: 0.5rem; }
        .vuln-box p { color: #92400e; margin: 0; font-size: 0.95rem; }
        .btn { 
            display: inline-block; 
            background: linear-gradient(135deg, #f97316, #ea580c); 
            color: white; 
            padding: 1rem 2rem; 
            border-radius: 10px; 
            text-decoration: none; 
            font-weight: 600; 
            margin-top: 1rem;
            transition: all 0.3s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(249, 115, 22, 0.4); }
    </style>
</head>
<body>
    <div class='card'>
        <h1>📦 Lab 01 Database Ready!</h1>
        <div class='success'><strong>Database '$dbname' created successfully!</strong></div>
        
        <h2>👥 Test Accounts</h2>
        <table>
            <tr><th>Username</th><th>Password</th><th>Role</th></tr>
            <tr><td><code>wiener</code></td><td>peter</td><td>user</td></tr>
            <tr><td><code>carlos</code></td><td>carlos123</td><td>user</td></tr>
            <tr><td><code>administrator</code></td><td>admin_secret_pass</td><td>admin</td></tr>
        </table>
        
        <div class='vuln-box'>
            <h3>⚠️ Vulnerability</h3>
            <p>The session cookie contains a serialized PHP object with an <code>admin</code> attribute. The server trusts this value without verification, allowing privilege escalation by modifying the serialized data.</p>
        </div>
        
        <h2>🎯 Objective</h2>
        <p>Login as <code>wiener:peter</code>, modify the serialized session cookie to gain admin privileges, then delete user <code>carlos</code>.</p>
        
        <a href='index.php' class='btn'>🚀 Start Lab</a>
    </div>
</body>
</html>";

} catch (PDOException $e) {
    echo "<div style='padding:20px;background:#fee2e2;border:1px solid #ef4444;margin:20px;border-radius:8px;'><strong>Database Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
