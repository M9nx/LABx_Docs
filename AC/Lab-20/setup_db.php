<?php
// Lab 20 - Database Setup Script

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
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database_setup.sql');
    $pdo->exec($sql);
    
    echo "<h1>✅ Lab 20 Database Setup Complete!</h1>";
    echo "<p>Database <strong>ac_lab20</strong> has been created with sample data.</p>";
    echo "<h3>Test Accounts:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Username</th><th>Password</th><th>Role in TechCorp</th><th>Description</th></tr>";
    echo "<tr><td>victim_owner</td><td>victim123</td><td>Owner</td><td>Organization owner with full permissions</td></tr>";
    echo "<tr style='background: #ffe0e0;'><td><strong>attacker_member</strong></td><td><strong>attacker123</strong></td><td><strong>Member</strong></td><td><strong>Limited permissions - USE FOR ATTACK</strong></td></tr>";
    echo "<tr><td>alice_admin</td><td>alice123</td><td>Admin</td><td>Administrator with elevated permissions</td></tr>";
    echo "<tr><td>bob_member</td><td>bob123</td><td>Member</td><td>Regular member</td></tr>";
    echo "</table>";
    echo "<br><a href='index.php' style='padding: 10px 20px; background: #14b8a6; color: white; text-decoration: none; border-radius: 5px;'>Go to Lab 20</a>";
    
} catch (PDOException $e) {
    die("<h1>❌ Setup Failed</h1><p>Error: " . $e->getMessage() . "</p>");
}
?>
