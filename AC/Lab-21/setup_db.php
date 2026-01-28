<?php
// Lab 21: Database Setup Script

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

try {
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents('database_setup.sql');
    $pdo->exec($sql);
    
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - Lab 21</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 3rem;
            max-width: 600px;
            text-align: center;
        }
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        h1 {
            color: #10b981;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }
        p {
            color: #94a3b8;
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .info-box {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .info-box h3 {
            color: #6366f1;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }
        .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            color: #e2e8f0;
            font-size: 0.9rem;
        }
        .cred-row:last-child { border-bottom: none; }
        .cred-user { color: #f59e0b; font-weight: 500; }
        .cred-pass { color: #94a3b8; font-family: monospace; }
        .btn {
            display: inline-block;
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }
        .tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }
        .tag.victim { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .tag.attacker { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='success-icon'>✅</div>
        <h1>Database Setup Complete!</h1>
        <p>Lab 21 - Stocky Application IDOR has been configured successfully.</p>
        
        <div class='info-box'>
            <h3>🔑 Test Credentials</h3>
            <div class='cred-row'>
                <span class='cred-user'>user_a <span class='tag victim'>VICTIM</span></span>
                <span class='cred-pass'>usera123</span>
            </div>
            <div class='cred-row'>
                <span class='cred-user'>user_b <span class='tag attacker'>ATTACKER</span></span>
                <span class='cred-pass'>userb123</span>
            </div>
            <div class='cred-row'>
                <span class='cred-user'>admin_stocky</span>
                <span class='cred-pass'>admin123</span>
            </div>
            <div class='cred-row'>
                <span class='cred-user'>charlie</span>
                <span class='cred-pass'>charlie123</span>
            </div>
            <div class='cred-row'>
                <span class='cred-user'>david</span>
                <span class='cred-pass'>david123</span>
            </div>
        </div>
        
        <div class='info-box'>
            <h3>⚠️ Settings IDs (for IDOR attack)</h3>
            <div class='cred-row'>
                <span class='cred-user'>User A (victim)</span>
                <span class='cred-pass'>111111</span>
            </div>
            <div class='cred-row'>
                <span class='cred-user'>User B (attacker)</span>
                <span class='cred-pass'>111112</span>
            </div>
        </div>
        
        <a href='index.php' class='btn'>Launch Lab →</a>
    </div>
</body>
</html>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Setup Error</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }
        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 2rem;
            max-width: 500px;
        }
        h2 { margin-bottom: 1rem; }
        p { color: #94a3b8; }
        code { background: rgba(0,0,0,0.3); padding: 0.5rem; border-radius: 4px; display: block; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class='error-box'>
        <h2>❌ Setup Failed</h2>
        <p>Error: " . htmlspecialchars($e->getMessage()) . "</p>
        <code>Check MySQL credentials (root/root) and ensure MySQL is running.</code>
    </div>
</body>
</html>";
}
?>
