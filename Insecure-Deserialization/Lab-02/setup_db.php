<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Database Setup Script
 * 
 * This script initializes/resets the lab database
 */
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fef3c7;border:1px solid #f59e0b;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];
$dbname = 'deserial_lab2';

$message = '';
$messageType = '';

try {
    // Connect without database selected
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop and recreate database
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    $pdo->exec("CREATE DATABASE $dbname");
    $pdo->exec("USE $dbname");
    
    // Create users table with access_token
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            access_token VARCHAR(64) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    // Generate unique access tokens
    function genToken() {
        return bin2hex(random_bytes(32));
    }
    
    // Insert users with access tokens
    $users = [
        ['administrator', 'admin_secret_pass', 'admin@seriallab.com', 'Administrator', 'admin', genToken()],
        ['carlos', 'carlos123', 'carlos@example.com', 'Carlos Rodriguez', 'user', genToken()],
        ['wiener', 'peter', 'wiener@example.com', 'Peter Wiener', 'user', genToken()],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role, access_token) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3], $u[4], $u[5]]);
    }
    
    $message = "Database '$dbname' has been successfully initialized with 3 users.";
    $messageType = 'success';
    
} catch(PDOException $e) {
    $message = "Error: " . $e->getMessage();
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Lab 02</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            min-height: 100vh;
            color: #e5e5e5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            background: linear-gradient(145deg, #1a1a1a, #0d0d0d);
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            border: 1px solid rgba(249, 115, 22, 0.3);
            text-align: center;
        }
        h1 { color: #fff; margin-bottom: 1.5rem; }
        .message {
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .message.success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }
        .message.error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .info {
            background: rgba(249, 115, 22, 0.1);
            border: 1px solid rgba(249, 115, 22, 0.3);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: left;
        }
        .info h3 { color: #f97316; margin-bottom: 0.5rem; }
        .info p { color: #a0a0a0; font-size: 0.9rem; line-height: 1.6; }
        .info code {
            background: rgba(249, 115, 22, 0.2);
            padding: 2px 6px;
            border-radius: 4px;
            color: #fb923c;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin: 0.5rem;
            transition: transform 0.3s;
        }
        .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); color: white; }
        .btn-secondary { background: linear-gradient(145deg, #1a1a1a, #0d0d0d); color: #e5e5e5; border: 1px solid rgba(249, 115, 22, 0.3); }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Setup</h1>
        <p style="color: #888; margin-bottom: 2rem;">Lab 02: Modifying Serialized Data Types</p>
        
        <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        
        <div class="info">
            <h3>Test Accounts Created</h3>
            <p>
                <strong>Admin:</strong> <code>administrator</code> / <code>admin_secret_pass</code><br>
                <strong>User:</strong> <code>carlos</code> / <code>carlos123</code><br>
                <strong>User:</strong> <code>wiener</code> / <code>peter</code>
            </p>
        </div>
        
        <div class="info">
            <h3>Database Info</h3>
            <p>
                <strong>Database:</strong> <code><?= $dbname ?></code><br>
                <strong>Table:</strong> <code>users</code><br>
                <strong>Key column:</strong> <code>access_token</code> (64-char hex)
            </p>
        </div>
        
        <a href="index.php" class="btn btn-primary">Go to Lab</a>
        <a href="setup_db.php" class="btn btn-secondary">Reset Database</a>
        <a href="../" class="btn btn-secondary">Back to Labs</a>
    </div>
</body>
</html>
