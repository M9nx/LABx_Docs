<?php
/**
 * Lab 19: Database Setup Script
 * Run this once to initialize the database
 */

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
    
    echo "<h2 style='color: #10b981;'>✓ Lab 19 Database Setup Complete!</h2>";
    echo "<p>Database 'ac_lab19' created with sample data.</p>";
    echo "<h3>Test Accounts:</h3>";
    echo "<ul>";
    echo "<li><strong>victim_designer</strong> / victim123 (Target - has saved projects ID: 101-105)</li>";
    echo "<li><strong>attacker_user</strong> / attacker123 (Use this to exploit)</li>";
    echo "<li><strong>admin</strong> / admin123</li>";
    echo "</ul>";
    echo "<p><a href='index.php'>→ Go to Lab 19</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: #ef4444;'>Setup Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
