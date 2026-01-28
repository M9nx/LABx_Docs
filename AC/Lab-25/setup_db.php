<?php
// Database Setup Script for Lab 25

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['auto'])) {
    try {
        // Connect without database
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Read and execute SQL file
        $sql = file_get_contents(__DIR__ . '/database_setup.sql');
        $pdo->exec($sql);
        
        $success = true;
        $message = "Database 'ac_lab25' created successfully! Lab is ready.";
        
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Notes IDOR Lab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e0e0e0;
        }
        .container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2.5rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        h1 {
            color: #fc6d26;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: #888;
            margin-bottom: 1.5rem;
        }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .message.success {
            background: rgba(0, 200, 83, 0.1);
            border: 1px solid rgba(0, 200, 83, 0.3);
            color: #66ff99;
        }
        .message.error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6666;
        }
        .btn {
            display: inline-block;
            padding: 0.85rem 2rem;
            background: linear-gradient(135deg, #fc6d26 0%, #e24329 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(252, 109, 38, 0.4);
        }
        .info {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            text-align: left;
            font-size: 0.9rem;
        }
        .info h4 {
            color: #fc6d26;
            margin-bottom: 0.5rem;
        }
        .info code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">📝</div>
        <h1>Lab 25 Setup</h1>
        <p class="subtitle">Notes IDOR on Personal Snippets</p>
        
        <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <a href="index.php" class="btn">🚀 Start Lab</a>
        <?php else: ?>
        <form method="POST">
            <button type="submit" class="btn">🔧 Initialize Database</button>
        </form>
        <?php endif; ?>
        
        <div class="info">
            <h4>Database Configuration</h4>
            <p>Host: <code>localhost</code></p>
            <p>User: <code>root</code></p>
            <p>Password: <code>root</code></p>
            <p>Database: <code>ac_lab25</code></p>
        </div>
    </div>
</body>
</html>
