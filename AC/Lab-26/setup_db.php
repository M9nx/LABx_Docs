<?php
/**
 * Lab 26: Database Setup Script
 * IDOR in API Applications - Pressable-Style
 */

$pageTitle = "Lab 26 Database Setup";
$dbName = "ac_lab26";
$setupFile = __DIR__ . '/database_setup.sql';

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
$messageType = '';

if (isset($_POST['setup'])) {
    try {
        // Connect without database
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Read and execute SQL file
        if (file_exists($setupFile)) {
            $sql = file_get_contents($setupFile);
            $pdo->exec($sql);
            $message = "Database '$dbName' has been successfully created and populated with test data!";
            $messageType = 'success';
        } else {
            $message = "SQL setup file not found at: $setupFile";
            $messageType = 'error';
        }
    } catch (PDOException $e) {
        $message = "Database setup error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Check if database exists
$dbExists = false;
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $user, $pass);
    $dbExists = true;
} catch (PDOException $e) {
    $dbExists = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
            min-height: 100vh;
            color: #e0e0e0;
            padding: 2rem;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 2rem;
        }
        .back-link:hover { color: #00b4d8; }
        .setup-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
        }
        .setup-card h1 {
            color: #00b4d8;
            margin-bottom: 0.5rem;
        }
        .setup-card p {
            color: #888;
            margin-bottom: 1.5rem;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .status-exists {
            background: rgba(0, 200, 100, 0.2);
            color: #00c853;
        }
        .status-missing {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6b6b;
        }
        .message {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .message-success {
            background: rgba(0, 200, 100, 0.2);
            border: 1px solid rgba(0, 200, 100, 0.3);
            color: #00c853;
        }
        .message-error {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
        }
        .info-section {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .info-section h3 {
            color: #00b4d8;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }
        .info-section ul {
            padding-left: 1.25rem;
            color: #ccc;
            line-height: 1.8;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 180, 216, 0.3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ff4444);
            color: white;
        }
        .button-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            color: #00ff88;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Back to Lab 26</a>
        
        <div class="setup-card">
            <h1>🗄️ Database Setup</h1>
            <p>Initialize the database for Lab 26: IDOR in API Applications</p>
            
            <?php if ($message): ?>
            <div class="message message-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <div class="status-badge <?php echo $dbExists ? 'status-exists' : 'status-missing'; ?>">
                <?php if ($dbExists): ?>
                ✅ Database Exists
                <?php else: ?>
                ❌ Database Not Found
                <?php endif; ?>
            </div>
            
            <div class="info-section">
                <h3>📋 What This Creates</h3>
                <ul>
                    <li>Database: <code>ac_lab26</code></li>
                    <li>Tables: users, api_applications, sites, collaborators, api_logs, activity_log</li>
                    <li>5 test users (attacker, victim, sarah, mike, admin)</li>
                    <li>9 API applications with sensitive secrets</li>
                </ul>
            </div>
            
            <div class="info-section">
                <h3>🔑 Test Credentials</h3>
                <ul>
                    <li><strong>Attacker:</strong> attacker / attacker123</li>
                    <li><strong>Victim:</strong> victim / victim123</li>
                    <li><strong>Others:</strong> sarah, mike, admin (same pattern)</li>
                </ul>
            </div>
            
            <form method="POST" style="margin-bottom: 1rem;">
                <div class="button-row">
                    <button type="submit" name="setup" class="btn <?php echo $dbExists ? 'btn-danger' : 'btn-primary'; ?>">
                        <?php echo $dbExists ? '🔄 Reset Database' : '🚀 Create Database'; ?>
                    </button>
                    <?php if ($dbExists): ?>
                    <a href="login.php" class="btn btn-secondary">→ Go to Login</a>
                    <?php endif; ?>
                </div>
            </form>
            
            <?php if ($dbExists): ?>
            <p style="color: #888; font-size: 0.9rem;">
                ⚠️ Resetting the database will delete all existing data and restore defaults.
            </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
