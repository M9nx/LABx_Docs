<?php
/**
 * Lab 28: Database Setup Helper
 * MTN Developers Portal IDOR
 */

$pageTitle = "Database Setup - Lab 28";
$setupSuccess = false;
$setupError = '';
$setupMessages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_db'])) {
    try {
        // Connect without database selected
        $pdo = new PDO(
            "mysql:host=localhost",
            "root",
            "root",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Read and execute the SQL file
        $sqlFile = __DIR__ . '/database_setup.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL file not found: $sqlFile");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) { return !empty($stmt) && $stmt !== ''; }
        );
        
        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                $pdo->exec($statement);
                // Extract table name for logging
                if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                    $setupMessages[] = "Created table: {$matches[1]}";
                } elseif (preg_match('/INSERT INTO.*?`?(\w+)`?/i', $statement, $matches)) {
                    $setupMessages[] = "Inserted data into: {$matches[1]}";
                } elseif (preg_match('/CREATE DATABASE/i', $statement)) {
                    $setupMessages[] = "Created database: ac_lab28";
                }
            }
        }
        
        $setupSuccess = true;
        $setupMessages[] = "✅ Database setup completed successfully!";
        
    } catch (PDOException $e) {
        $setupError = "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        $setupError = "Setup Error: " . $e->getMessage();
    }
}

// Check current database status
$dbExists = false;
$tableCount = 0;
try {
    $checkPdo = new PDO("mysql:host=localhost", "root", "");
    $result = $checkPdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'ac_lab28'");
    $dbExists = $result->rowCount() > 0;
    
    if ($dbExists) {
        $checkPdo = new PDO("mysql:host=localhost;dbname=ac_lab28", "root", "");
        $tables = $checkPdo->query("SHOW TABLES")->fetchAll();
        $tableCount = count($tables);
    }
} catch (Exception $e) {
    // Ignore connection errors for status check
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 45px;
            height: 45px;
            background: #000;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            color: #ffcc00;
        }
        .logo-text {
            font-size: 1.4rem;
            font-weight: bold;
            color: #000;
        }
        .nav-links a {
            color: #000;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
        }
        .main-content {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
        .back-link:hover { color: #ffcc00; }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .card h1 {
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        .card p {
            color: #aaa;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .status-box {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 204, 0, 0.1);
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .status-item .label {
            color: #888;
        }
        .status-item .value {
            font-weight: 500;
        }
        .status-item .value.success { color: #44ff44; }
        .status-item .value.warning { color: #ffcc00; }
        .status-item .value.error { color: #ff4444; }
        .btn {
            display: inline-block;
            padding: 0.875rem 1.5rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            border: none;
            border-radius: 8px;
            color: #000;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 204, 0, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ffcc00;
            color: #ffcc00;
        }
        .success-box {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid #44ff44;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .success-box h3 {
            color: #44ff44;
            margin-bottom: 0.5rem;
        }
        .error-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid #ff4444;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .error-box h3 {
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .log-messages {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            max-height: 300px;
            overflow-y: auto;
        }
        .log-messages div {
            padding: 0.25rem 0;
            color: #8b949e;
        }
        .log-messages div.success {
            color: #44ff44;
        }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">
            <div class="logo-icon">MTN</div>
            <div class="logo-text">Developers Portal</div>
        </a>
        <nav class="nav-links">
            <a href="index.php">← Back to Lab</a>
            <a href="login.php">Login</a>
            <a href="docs.php">Docs</a>
        </nav>
    </header>

    <main class="main-content">
        <a href="index.php" class="back-link">← Back to Lab Home</a>

        <div class="card">
            <h1>🗄️ Database Setup</h1>
            <p>
                This page helps you set up the required database for Lab 28.
                Click the button below to create the <code>ac_lab28</code> database 
                and populate it with test data.
            </p>

            <div class="status-box">
                <div class="status-item">
                    <span class="label">Database Status</span>
                    <span class="value <?= $dbExists ? 'success' : 'warning' ?>">
                        <?= $dbExists ? '✓ Exists' : '⚠ Not Created' ?>
                    </span>
                </div>
                <div class="status-item">
                    <span class="label">Tables</span>
                    <span class="value <?= $tableCount > 0 ? 'success' : 'warning' ?>">
                        <?= $tableCount > 0 ? "✓ $tableCount tables" : '⚠ No tables' ?>
                    </span>
                </div>
            </div>

            <?php if ($setupSuccess): ?>
            <div class="success-box">
                <h3>✅ Setup Complete!</h3>
                <p>The database has been created and populated successfully.</p>
            </div>
            
            <?php if (!empty($setupMessages)): ?>
            <div class="log-messages">
                <?php foreach ($setupMessages as $msg): ?>
                <div class="<?= strpos($msg, '✅') !== false ? 'success' : '' ?>"><?= htmlspecialchars($msg) ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <br>
            <a href="login.php" class="btn">Go to Login →</a>
            <?php elseif ($setupError): ?>
            <div class="error-box">
                <h3>❌ Setup Failed</h3>
                <p><?= htmlspecialchars($setupError) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!$setupSuccess): ?>
            <form method="POST">
                <button type="submit" name="setup_db" class="btn">
                    <?= $dbExists ? '🔄 Reset Database' : '🚀 Create Database' ?>
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>📋 What Gets Created</h2>
            <ul style="color: #aaa; line-height: 1.8; padding-left: 1.5rem;">
                <li>Database: <code>ac_lab28</code></li>
                <li>Table: <code>users</code> - 9 test users</li>
                <li>Table: <code>teams</code> - 7 teams</li>
                <li>Table: <code>team_members</code> - Team memberships</li>
                <li>Table: <code>team_invitations</code> - Pending invites</li>
                <li>Table: <code>activity_log</code> - Audit logging</li>
            </ul>
        </div>

        <div class="card">
            <h2>🔐 Test Accounts</h2>
            <div class="status-box">
                <div class="status-item">
                    <span class="label">Attacker</span>
                    <span class="value"><code>attacker</code> / <code>attacker123</code></span>
                </div>
                <div class="status-item">
                    <span class="label">Victim (Bob)</span>
                    <span class="value"><code>bob_dev</code> / <code>bob123</code></span>
                </div>
                <div class="status-item">
                    <span class="label">Target (Carol)</span>
                    <span class="value"><code>carol_admin</code> / <code>carol123</code></span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
