<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Database Setup Script
 * 
 * This script initializes/resets the lab database and creates the target file
 */
require_once __DIR__ . '/../../db-config.php';
require_once __DIR__ . '/../progress.php';

$creds = getDbCredentials();

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fef3c7;border:1px solid #f59e0b;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];
$dbname = 'deserial_lab3';

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
    
    // Create users table with avatar_link
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            avatar_link VARCHAR(255) DEFAULT NULL COMMENT 'Path to user avatar file',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    // Define base path for avatars
    $basePath = __DIR__;
    
    // Insert users with avatar paths
    $users = [
        ['wiener', 'peter', 'wiener@example.com', 'Peter Wiener', "$basePath/home/wiener/avatar.jpg"],
        ['gregg', 'rosebud', 'gregg@example.com', 'Gregg Rosebud', "$basePath/home/gregg/avatar.jpg"],
        ['carlos', 'montoya', 'carlos@example.com', 'Carlos Montoya', "$basePath/home/carlos/avatar.jpg"],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, avatar_link) VALUES (?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3], $u[4]]);
    }
    
    // Create home directories and files
    $homeDir = __DIR__ . '/home';
    
    // Create user directories if they don't exist
    @mkdir("$homeDir/wiener", 0777, true);
    @mkdir("$homeDir/gregg", 0777, true);
    @mkdir("$homeDir/carlos", 0777, true);
    
    // Create default avatar files (just placeholder files)
    file_put_contents("$homeDir/wiener/avatar.jpg", "WIENER_AVATAR_PLACEHOLDER");
    file_put_contents("$homeDir/gregg/avatar.jpg", "GREGG_AVATAR_PLACEHOLDER");
    file_put_contents("$homeDir/carlos/avatar.jpg", "CARLOS_AVATAR_PLACEHOLDER");
    
    // Create the TARGET FILE - morale.txt in Carlos's home directory
    $moraleContent = <<<EOT
===========================================
CARLOS'S MORALE.TXT - CONFIDENTIAL
===========================================

Dear Carlos,

Your recent performance review has been outstanding!
We're pleased to inform you that you've been selected
for the Employee of the Month award.

Keep up the great work!

Best regards,
HR Department

P.S. This file should never be accessible to other users!
===========================================
EOT;
    
    file_put_contents("$homeDir/carlos/morale.txt", $moraleContent);
    
    // Reset lab progress tracking
    resetLabProgress(3);
    
    $message = "Database '$dbname' has been successfully initialized with 3 users. Target file 'morale.txt' created in Carlos's home directory. Lab progress has been reset.";
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
    <title>Database Setup - Lab 03</title>
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
            max-width: 700px;
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
        .target-file {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: left;
        }
        .target-file h3 { color: #ef4444; margin-bottom: 0.5rem; }
        .target-file code { color: #fca5a5; background: rgba(239, 68, 68, 0.2); padding: 2px 6px; border-radius: 4px; }
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
        <p style="color: #888; margin-bottom: 2rem;">Lab 03: Using Application Functionality</p>
        
        <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        
        <div class="info">
            <h3>Test Accounts Created</h3>
            <p>
                <strong>Primary:</strong> <code>wiener</code> / <code>peter</code><br>
                <strong>Backup:</strong> <code>gregg</code> / <code>rosebud</code><br>
                <strong>Target:</strong> <code>carlos</code> (owns morale.txt)
            </p>
        </div>
        
        <div class="target-file">
            <h3>Target File</h3>
            <p>
                <strong>Path:</strong> <code>/home/carlos/morale.txt</code><br>
                <strong>Goal:</strong> Delete this file using the account deletion functionality
            </p>
        </div>
        
        <div class="info">
            <h3>Database Info</h3>
            <p>
                <strong>Database:</strong> <code><?= $dbname ?></code><br>
                <strong>Table:</strong> <code>users</code><br>
                <strong>Key column:</strong> <code>avatar_link</code> (file path)
            </p>
        </div>
        
        <a href="index.php" class="btn btn-primary">Go to Lab</a>
        <a href="setup_db.php" class="btn btn-secondary">Reset Lab</a>
        <a href="../" class="btn btn-secondary">Back to Labs</a>
    </div>
</body>
</html>
