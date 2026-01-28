<?php
// Lab 23: Database Setup Script

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Lab 23</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem;
        }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #a78bfa; margin-bottom: 2rem; text-align: center; }
        .card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .success { border-color: #10b981; background: rgba(16, 185, 129, 0.1); }
        .error { border-color: #ef4444; background: rgba(239, 68, 68, 0.1); }
        .step { display: flex; align-items: center; gap: 1rem; margin: 0.75rem 0; }
        .step-icon { font-size: 1.5rem; }
        .step-text { color: #94a3b8; }
        .step-text.done { color: #10b981; }
        .step-text.fail { color: #ef4444; }
        .credentials {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .cred-box {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }
        .cred-box h4 { color: #a78bfa; margin-bottom: 0.5rem; }
        .cred-box .role { font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 10px; margin-bottom: 0.5rem; display: inline-block; }
        .cred-box .role.victim { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .cred-box .role.attacker { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .cred-box .role.admin { background: rgba(99, 102, 241, 0.2); color: #818cf8; }
        .cred-box p { color: #64748b; font-size: 0.9rem; margin: 0.25rem 0; }
        .cred-box code { color: #f59e0b; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
        }
        .info-box {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .info-box h4 { color: #818cf8; margin-bottom: 0.5rem; }
        .info-box code { color: #f59e0b; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏷️ Lab 23: Database Setup</h1>
        
        <?php
        $steps = [];
        $success = true;
        
        try {
            // Step 1: Connect to MySQL (using centralized credentials from top of file)
            $pdo = new PDO("mysql:host=$host", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $steps[] = ['icon' => '✅', 'text' => 'Connected to MySQL server', 'status' => 'done'];
            
            // Step 2: Read SQL file
            $sqlFile = __DIR__ . '/database_setup.sql';
            if (!file_exists($sqlFile)) {
                throw new Exception("SQL file not found: $sqlFile");
            }
            $sql = file_get_contents($sqlFile);
            $steps[] = ['icon' => '✅', 'text' => 'Read database_setup.sql', 'status' => 'done'];
            
            // Step 3: Execute SQL
            $pdo->exec($sql);
            $steps[] = ['icon' => '✅', 'text' => 'Created database ac_lab23', 'status' => 'done'];
            $steps[] = ['icon' => '✅', 'text' => 'Created tables: users, assets, tags, asset_tags, activity_log', 'status' => 'done'];
            $steps[] = ['icon' => '✅', 'text' => 'Inserted sample users and data', 'status' => 'done'];
            $steps[] = ['icon' => '✅', 'text' => 'Inserted victim\'s private custom tags (TARGET!)', 'status' => 'done'];
            
        } catch (Exception $e) {
            $success = false;
            $steps[] = ['icon' => '❌', 'text' => 'Error: ' . $e->getMessage(), 'status' => 'fail'];
        }
        ?>
        
        <div class="card <?= $success ? 'success' : 'error' ?>">
            <h2><?= $success ? '✅ Setup Complete!' : '❌ Setup Failed' ?></h2>
            <?php foreach ($steps as $step): ?>
                <div class="step">
                    <span class="step-icon"><?= $step['icon'] ?></span>
                    <span class="step-text <?= $step['status'] ?>"><?= $step['text'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($success): ?>
        <div class="card">
            <h2>🔑 Test Credentials</h2>
            <div class="credentials">
                <div class="cred-box">
                    <span class="role victim">👤 VICTIM</span>
                    <h4>victim_org</h4>
                    <p>Password: <code>victim123</code></p>
                    <p>Has: 7 private custom tags</p>
                </div>
                <div class="cred-box">
                    <span class="role attacker">☠️ ATTACKER</span>
                    <h4>attacker_user</h4>
                    <p>Password: <code>attacker123</code></p>
                    <p>Will enumerate victim's tags</p>
                </div>
                <div class="cred-box">
                    <span class="role admin">👑 ADMIN</span>
                    <h4>admin</h4>
                    <p>Password: <code>admin123</code></p>
                    <p>Platform administrator</p>
                </div>
                <div class="cred-box">
                    <span class="role">👤 USER</span>
                    <h4>researcher_bob</h4>
                    <p>Password: <code>bob123</code></p>
                    <p>Regular researcher</p>
                </div>
            </div>
            
            <div class="info-box">
                <h4>🎯 Target Tag IDs (Victim's)</h4>
                <p>Internal IDs: <code>49790001</code> to <code>49790007</code></p>
                <p>Example encoded: <code><?= base64_encode('gid://tagscope/AsmTag/49790001') ?></code></p>
                <p>Decodes to: <code>gid://tagscope/AsmTag/49790001</code></p>
            </div>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="index.php" class="btn">🚀 Start Lab</a>
                <a href="lab-description.php" class="btn">📖 Lab Guide</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
