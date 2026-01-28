<?php
// Lab 18: Database Setup Script

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

echo "<html><head><title>Lab 18 Setup</title>";
echo "<style>
    body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #e0e0e0; min-height: 100vh; padding: 2rem; }
    .container { max-width: 800px; margin: 0 auto; }
    h1 { color: #5c6ac4; }
    .success { color: #50b83c; background: rgba(80, 184, 60, 0.1); padding: 1rem; border-radius: 8px; margin: 0.5rem 0; border-left: 4px solid #50b83c; }
    .error { color: #de3618; background: rgba(222, 54, 24, 0.1); padding: 1rem; border-radius: 8px; margin: 0.5rem 0; border-left: 4px solid #de3618; }
    .info { color: #5c6ac4; background: rgba(92, 106, 196, 0.1); padding: 1rem; border-radius: 8px; margin: 0.5rem 0; border-left: 4px solid #5c6ac4; }
    .btn { display: inline-block; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #5c6ac4, #47c1bf); color: white; text-decoration: none; border-radius: 8px; margin-top: 1rem; }
    .btn:hover { opacity: 0.9; }
    table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
    th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
    th { color: #5c6ac4; }
</style></head><body><div class='container'>";

echo "<h1>🔐 Lab 18: IDOR Session Expiration Setup</h1>";

try {
    // Connect without database first
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✓ Connected to MySQL server</div>";
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database_setup.sql');
    
    // Split by semicolons but handle edge cases
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<div class='success'>✓ Database 'ac_lab18' created successfully</div>";
    echo "<div class='success'>✓ Tables created (users, user_sessions, session_activity_log, account_settings)</div>";
    echo "<div class='success'>✓ Sample data inserted</div>";
    
    // Show created users
    $pdo->exec("USE ac_lab18");
    $stmt = $pdo->query("SELECT id, username, email, role, store_name FROM users");
    $users = $stmt->fetchAll();
    
    echo "<div class='info'><h3>📋 Test Accounts Created:</h3>";
    echo "<table><tr><th>ID</th><th>Username</th><th>Password</th><th>Role</th><th>Store</th></tr>";
    $passwords = ['admin123', 'victim123', 'attacker123', 'staff123', 'another123'];
    foreach ($users as $i => $user) {
        echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td>{$passwords[$i]}</td><td>{$user['role']}</td><td>{$user['store_name']}</td></tr>";
    }
    echo "</table></div>";
    
    // Show active sessions
    $stmt = $pdo->query("SELECT us.id, u.username, us.device_info, us.location, us.is_active FROM user_sessions us JOIN users u ON us.user_id = u.id");
    $sessions = $stmt->fetchAll();
    
    echo "<div class='info'><h3>🔑 Pre-created Sessions:</h3>";
    echo "<table><tr><th>Session ID</th><th>User</th><th>Device</th><th>Location</th><th>Status</th></tr>";
    foreach ($sessions as $session) {
        $status = $session['is_active'] ? '🟢 Active' : '🔴 Expired';
        echo "<tr><td>{$session['id']}</td><td>{$session['username']}</td><td>{$session['device_info']}</td><td>{$session['location']}</td><td>{$status}</td></tr>";
    }
    echo "</table></div>";
    
    echo "<div class='success'><strong>🎉 Setup Complete!</strong><br>The lab is ready. Use attacker_store/attacker123 to exploit IDOR and expire victim_store's sessions.</div>";
    echo "<a href='index.php' class='btn'>🚀 Start Lab</a> ";
    echo "<a href='lab-description.php' class='btn'>📖 Lab Guide</a>";
    
} catch (PDOException $e) {
    echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='info'>Make sure MySQL is running and credentials are correct (root/root)</div>";
}

echo "</div></body></html>";
?>
