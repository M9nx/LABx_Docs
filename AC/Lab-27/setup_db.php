<?php
/**
 * Lab 27: Database Setup Helper
 * IDOR in Stats API Endpoint
 */

$pageTitle = "Database Setup - Lab 27";
$setupMessages = [];
$dbError = null;

// Database configuration
$host = 'localhost';
$rootUser = 'root';
$rootPass = 'root';
$dbName = 'ac_lab27';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_db'])) {
    try {
        // Connect without database selected
        $pdo = new PDO("mysql:host=$host", $rootUser, $rootPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Read and execute SQL file
        $sqlFile = __DIR__ . '/database_setup.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL file not found: database_setup.sql");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Split by delimiter for stored procedures
        $statements = [];
        $currentStatement = '';
        $lines = explode("\n", $sql);
        $delimiter = ';';
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Check for delimiter change
            if (preg_match('/^DELIMITER\s+(.+)$/i', $trimmedLine, $matches)) {
                $delimiter = trim($matches[1]);
                continue;
            }
            
            $currentStatement .= $line . "\n";
            
            // Check if statement ends with current delimiter
            if (substr(rtrim($currentStatement), -strlen($delimiter)) === $delimiter) {
                $stmt = rtrim($currentStatement);
                $stmt = substr($stmt, 0, -strlen($delimiter));
                $stmt = trim($stmt);
                
                if (!empty($stmt) && !preg_match('/^--/', $stmt)) {
                    $statements[] = $stmt;
                }
                $currentStatement = '';
            }
        }
        
        // Execute each statement
        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore "database exists" and "table exists" errors
                    if (strpos($e->getMessage(), '1007') === false && 
                        strpos($e->getMessage(), '1050') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        // Call the stored procedure to generate trading stats
        $pdo->exec("USE $dbName");
        try {
            $pdo->exec("CALL generate_stats()");
            $setupMessages[] = ['type' => 'success', 'text' => 'Generated 365 days of trading statistics for all accounts'];
        } catch (PDOException $e) {
            $setupMessages[] = ['type' => 'warning', 'text' => 'Stats generation: ' . $e->getMessage()];
        }
        
        $setupMessages[] = ['type' => 'success', 'text' => 'Database "ac_lab27" created successfully!'];
        $setupMessages[] = ['type' => 'success', 'text' => 'All tables created (users, mt_accounts, trading_stats, orders, api_logs, activity_log)'];
        $setupMessages[] = ['type' => 'success', 'text' => 'Test users created: attacker, victim, whale, sarah, admin'];
        $setupMessages[] = ['type' => 'success', 'text' => '11 MT trading accounts created with various balances'];
        
    } catch (Exception $e) {
        $dbError = $e->getMessage();
    }
}

// Check current database status
$dbStatus = ['exists' => false, 'tables' => [], 'users' => 0, 'accounts' => 0, 'stats' => 0];
try {
    $checkPdo = new PDO("mysql:host=$host;dbname=$dbName", $rootUser, $rootPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $dbStatus['exists'] = true;
    
    // Check tables
    $tables = $checkPdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $dbStatus['tables'] = $tables;
    
    // Count users
    if (in_array('users', $tables)) {
        $dbStatus['users'] = $checkPdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }
    
    // Count accounts
    if (in_array('mt_accounts', $tables)) {
        $dbStatus['accounts'] = $checkPdo->query("SELECT COUNT(*) FROM mt_accounts")->fetchColumn();
    }
    
    // Count stats records
    if (in_array('trading_stats', $tables)) {
        $dbStatus['stats'] = $checkPdo->query("SELECT COUNT(*) FROM trading_stats")->fetchColumn();
    }
    
} catch (PDOException $e) {
    // Database doesn't exist yet
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
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            min-height: 100vh;
            color: #e0e0e0;
            padding: 2rem;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            color: #ffd700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .header p { color: #888; }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .card h2 {
            color: #ffd700;
            margin-bottom: 1rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .status-item {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }
        .status-item .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffd700;
        }
        .status-item .label {
            font-size: 0.85rem;
            color: #888;
            margin-top: 0.25rem;
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-badge.success {
            background: rgba(68, 255, 68, 0.2);
            color: #44ff44;
        }
        .status-badge.warning {
            background: rgba(255, 200, 0, 0.2);
            color: #ffc800;
        }
        .status-badge.error {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6b6b;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            color: #000;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .message.success {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid rgba(68, 255, 68, 0.3);
            color: #44ff44;
        }
        .message.warning {
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.3);
            color: #ffc800;
        }
        .message.error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
        }
        .table-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .table-tag {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #ffd700;
        }
        .credentials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .credentials-table th, .credentials-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 215, 0, 0.1);
        }
        .credentials-table th {
            color: #ffd700;
            font-weight: 600;
        }
        .credentials-table td { color: #ccc; }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
        }
        .nav-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ Database Setup</h1>
            <p>Lab 27: IDOR in Stats API Endpoint</p>
        </div>

        <?php if ($dbError): ?>
        <div class="message error">
            <span>❌</span>
            <span><?= htmlspecialchars($dbError) ?></span>
        </div>
        <?php endif; ?>

        <?php foreach ($setupMessages as $msg): ?>
        <div class="message <?= $msg['type'] ?>">
            <span><?= $msg['type'] === 'success' ? '✅' : '⚠️' ?></span>
            <span><?= htmlspecialchars($msg['text']) ?></span>
        </div>
        <?php endforeach; ?>

        <div class="card">
            <h2>📊 Database Status</h2>
            
            <div style="margin-bottom: 1rem;">
                Database: <code>ac_lab27</code>
                <?php if ($dbStatus['exists']): ?>
                <span class="status-badge success">Connected</span>
                <?php else: ?>
                <span class="status-badge error">Not Found</span>
                <?php endif; ?>
            </div>

            <?php if ($dbStatus['exists']): ?>
            <div class="status-grid">
                <div class="status-item">
                    <div class="value"><?= $dbStatus['users'] ?></div>
                    <div class="label">Users</div>
                </div>
                <div class="status-item">
                    <div class="value"><?= $dbStatus['accounts'] ?></div>
                    <div class="label">MT Accounts</div>
                </div>
                <div class="status-item">
                    <div class="value"><?= number_format($dbStatus['stats']) ?></div>
                    <div class="label">Stats Records</div>
                </div>
                <div class="status-item">
                    <div class="value"><?= count($dbStatus['tables']) ?></div>
                    <div class="label">Tables</div>
                </div>
            </div>

            <?php if (!empty($dbStatus['tables'])): ?>
            <div class="table-list">
                <?php foreach ($dbStatus['tables'] as $table): ?>
                <span class="table-tag"><?= htmlspecialchars($table) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>🔧 Setup Database</h2>
            <p style="color: #888; margin-bottom: 1rem;">
                Click the button below to create/reset the database with all required tables and test data.
                This will generate 365 days of trading statistics for all accounts.
            </p>
            
            <form method="POST">
                <button type="submit" name="setup_db" class="btn btn-primary">
                    <?= $dbStatus['exists'] ? '🔄 Reset Database' : '🚀 Create Database' ?>
                </button>
            </form>
        </div>

        <div class="card">
            <h2>👥 Test Credentials</h2>
            <table class="credentials-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Balance Range</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>attacker</code></td>
                        <td><code>attacker123</code></td>
                        <td>Attacker (You)</td>
                        <td>$1,250 - $5,000</td>
                    </tr>
                    <tr>
                        <td><code>victim</code></td>
                        <td><code>victim123</code></td>
                        <td>Target Victim</td>
                        <td>$87,500 - $125,000</td>
                    </tr>
                    <tr>
                        <td><code>whale</code></td>
                        <td><code>whale123</code></td>
                        <td>High-Value Target</td>
                        <td>$750K - $2.5M</td>
                    </tr>
                    <tr>
                        <td><code>sarah</code></td>
                        <td><code>sarah123</code></td>
                        <td>Normal Trader</td>
                        <td>$15,000 - $25,000</td>
                    </tr>
                    <tr>
                        <td><code>admin</code></td>
                        <td><code>admin123</code></td>
                        <td>Administrator</td>
                        <td>$10,000,000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>🎯 Target Accounts for IDOR</h2>
            <table class="credentials-table">
                <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Owner</th>
                        <th>Type</th>
                        <th>~Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>MT5-200001</code></td>
                        <td>victim</td>
                        <td>Pro</td>
                        <td>$87,500</td>
                    </tr>
                    <tr>
                        <td><code>MT5-200002</code></td>
                        <td>victim</td>
                        <td>Raw Spread</td>
                        <td>$125,000</td>
                    </tr>
                    <tr>
                        <td><code>MT5-300001</code></td>
                        <td>whale</td>
                        <td>Zero</td>
                        <td>$2,500,000</td>
                    </tr>
                    <tr>
                        <td><code>MT5-000001</code></td>
                        <td>admin</td>
                        <td>Internal</td>
                        <td>$10,000,000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="nav-buttons">
            <a href="index.php" class="btn btn-secondary">← Back to Lab</a>
            <a href="login.php" class="btn btn-primary">Start Lab →</a>
        </div>
    </div>
</body>
</html>
