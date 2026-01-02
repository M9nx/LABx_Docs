<?php
// Lab 24: Database Setup Script
$host = 'localhost';
$user = 'root';
$pass = 'root';

echo "<html><head><title>Lab 24 - Database Setup</title>";
echo "<style>
    body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #0a0a0a, #1a0a1a); color: #e0e0e0; padding: 40px; min-height: 100vh; }
    .container { max-width: 800px; margin: 0 auto; }
    .card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,68,68,0.3); border-radius: 15px; padding: 30px; margin-bottom: 20px; }
    h1 { color: #ff4444; margin-bottom: 10px; }
    h2 { color: #ff6666; border-bottom: 1px solid rgba(255,68,68,0.3); padding-bottom: 10px; }
    .success { color: #00ff88; background: rgba(0,255,136,0.1); padding: 15px; border-radius: 8px; border-left: 4px solid #00ff88; margin: 10px 0; }
    .error { color: #ff4444; background: rgba(255,68,68,0.1); padding: 15px; border-radius: 8px; border-left: 4px solid #ff4444; margin: 10px 0; }
    .info { color: #00aaff; background: rgba(0,170,255,0.1); padding: 15px; border-radius: 8px; border-left: 4px solid #00aaff; margin: 10px 0; }
    .btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #ff4444, #cc0000); color: white; text-decoration: none; border-radius: 8px; margin: 10px 10px 10px 0; font-weight: 600; transition: all 0.3s; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(255,68,68,0.4); }
    .btn-secondary { background: linear-gradient(135deg, #6366f1, #4f46e5); }
    .btn-secondary:hover { box-shadow: 0 5px 20px rgba(99,102,241,0.4); }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,68,68,0.2); }
    th { color: #ff6666; background: rgba(255,68,68,0.1); }
    code { background: rgba(0,0,0,0.3); padding: 3px 8px; border-radius: 4px; color: #00ff88; }
</style></head><body><div class='container'>";

echo "<div class='card'>";
echo "<h1>🤖 Lab 24: ML Model Registry Setup</h1>";
echo "<p style='color:#888;'>IDOR Exposes All Machine Learning Models</p>";
echo "</div>";

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<div class='card'>";
    echo "<h2>📦 Setting Up Database</h2>";
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database_setup.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<div class='success'>✅ Database 'ac_lab24' created successfully!</div>";
    echo "<div class='success'>✅ All tables created and populated with sample data!</div>";
    echo "</div>";
    
    // Show created data
    $pdo->exec("USE ac_lab24");
    
    echo "<div class='card'>";
    echo "<h2>👥 Test Accounts</h2>";
    echo "<table>
            <tr><th>Username</th><th>Password</th><th>Role</th><th>Description</th></tr>
            <tr><td><code>attacker</code></td><td><code>attacker123</code></td><td>User</td><td>🎯 Use this to exploit</td></tr>
            <tr><td><code>victim_corp</code></td><td><code>victim123</code></td><td>User</td><td>🏢 Has 4 private ML models</td></tr>
            <tr><td><code>data_scientist</code></td><td><code>scientist123</code></td><td>User</td><td>🔬 Has 3 private research models</td></tr>
            <tr><td><code>admin</code></td><td><code>admin123</code></td><td>Admin</td><td>👑 System admin</td></tr>
          </table>";
    echo "</div>";
    
    echo "<div class='card'>";
    echo "<h2>🤖 Target ML Models (PRIVATE)</h2>";
    echo "<div class='info'>These models belong to other users and should NOT be accessible to the attacker!</div>";
    
    $stmt = $pdo->query("SELECT m.internal_id, m.name, m.framework, m.visibility, u.username as owner 
                          FROM ml_models m 
                          JOIN users u ON m.owner_id = u.id 
                          WHERE m.visibility = 'private' 
                          ORDER BY m.internal_id");
    $models = $stmt->fetchAll();
    
    echo "<table>
            <tr><th>Internal ID</th><th>GID</th><th>Model Name</th><th>Framework</th><th>Owner</th></tr>";
    foreach ($models as $model) {
        $gid = "gid://gitlab/Ml::Model/{$model['internal_id']}";
        echo "<tr>
                <td><code>{$model['internal_id']}</code></td>
                <td><code style='font-size:0.8em;'>{$gid}</code></td>
                <td>{$model['name']}</td>
                <td>{$model['framework']}</td>
                <td>{$model['owner']}</td>
              </tr>";
    }
    echo "</table>";
    echo "</div>";
    
    echo "<div class='card'>";
    echo "<h2>🎯 Vulnerability Information</h2>";
    echo "<div class='info'>
            <strong>Vulnerable Endpoint:</strong> <code>POST /api/graphql.php</code><br><br>
            <strong>Attack Vector:</strong> Model IDs are sequential (1000500, 1000501, 1000502...)<br><br>
            <strong>Target Range:</strong> <code>1000501</code> to <code>1000507</code> (victim's private models)
          </div>";
    echo "</div>";
    
    echo "<div class='card'>";
    echo "<a href='lab-description.php' class='btn'>📋 View Lab Description</a>";
    echo "<a href='login.php' class='btn btn-secondary'>🚀 Start Lab</a>";
    echo "<a href='docs.php' class='btn' style='background: linear-gradient(135deg, #10b981, #059669);'>📚 Documentation</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='card'>";
    echo "<div class='error'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='info'>Make sure MySQL is running and credentials are correct (root/root)</div>";
    echo "</div>";
}

echo "</div></body></html>";
